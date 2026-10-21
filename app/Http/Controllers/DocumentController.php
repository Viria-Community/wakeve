<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\StoreCompanyDocumentRequest;
use App\Http\Requests\Document\StoreFleetDocumentRequest;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\DocumentType;
use App\Models\FleetDocument;
use App\Models\FleetUnit;
use App\Services\AuditLogger;
use App\Services\DocumentStatusService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentStatusService $statusService) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if ($user->isPlatformStaff()) {
            abort_unless($user->permitsPlatform('documents.view'), 403);
        }
        $canViewCompanyDocuments = $user->isPlatformMember() || $user->permits('companies.view');
        $canViewFleetDocuments = $user->isPlatformMember() || $user->permits('fleet.view');
        abort_unless($canViewCompanyDocuments || $canViewFleetDocuments, 403);
        abort_if($request->input('scope') === 'company' && ! $canViewCompanyDocuments, 403);
        abort_if($request->input('scope') === 'fleet' && ! $canViewFleetDocuments, 403);

        $directoryScope = in_array($request->input('scope'), ['company', 'fleet'], true);
        $directoryStatus = in_array($request->input('status'), ['valid', 'to_renew', 'expired'], true)
            ? $request->input('status')
            : null;
        $reviewFilter = in_array($request->input('review'), ['approved', 'pending', 'rejected'], true)
            ? $request->input('review')
            : null;

        $companyDocumentsQuery = CompanyDocument::query()
            ->when(! $canViewCompanyDocuments, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(! $user->isPlatformMember(), fn ($query) => $query->where('company_id', $user->company_id));
        $fleetDocumentsQuery = FleetDocument::query()
            ->when(! $canViewFleetDocuments, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(! $user->isPlatformMember(), fn ($query) => $query->whereHas('fleetUnit', fn ($query) => $query->where('company_id', $user->company_id)));
        $companySummary = $this->documentSummary(clone $companyDocumentsQuery);
        $fleetSummary = $this->documentSummary(clone $fleetDocumentsQuery);
        $documentSummary = [
            'total' => $companySummary['total'] + $fleetSummary['total'],
            'valid' => $companySummary['valid'] + $fleetSummary['valid'],
            'toRenew' => $companySummary['toRenew'] + $fleetSummary['toRenew'],
            'expired' => $companySummary['expired'] + $fleetSummary['expired'],
        ];

        $documentRows = collect();
        $documentPage = null;
        if (! $directoryScope) {
            $documentPage = $this->paginatedDocumentRows(
                $request,
                clone $companyDocumentsQuery,
                clone $fleetDocumentsQuery,
                $directoryStatus,
                $reviewFilter,
            );
            $documentRows = collect($documentPage->items());
        }

        [$reviewDocuments, $pendingReviewCount] = $this->pendingReviewDocuments($request, clone $companyDocumentsQuery, clone $fleetDocumentsQuery);

        $fleetUnits = collect();
        if ($request->input('scope') === 'fleet') {
            $fleetUnits = FleetUnit::query()
                ->with('company')
                ->withCount([
                    'documents',
                    'documents as valid_documents_count' => fn ($query) => $this->applyExpiryFilter($query, 'valid'),
                    'documents as to_renew_documents_count' => fn ($query) => $this->applyExpiryFilter($query, 'to_renew'),
                    'documents as expired_documents_count' => fn ($query) => $this->applyExpiryFilter($query, 'expired'),
                ])
                ->when(! $request->user()->isPlatformMember(), fn ($query) => $query->where('company_id', $request->user()->company_id))
                ->tap(fn ($query) => $this->applyDirectoryDocumentFilters($query, $directoryStatus, $reviewFilter))
                ->orderBy('type')
                ->orderBy('registration_number')
                ->paginate(20)
                ->withQueryString();
        }

        $documentCompanies = collect();
        if ($request->input('scope') === 'company') {
            $documentCompanies = Company::query()
                ->when(! $request->user()->isPlatformMember(), fn ($query) => $query->whereKey($request->user()->company_id))
                ->withCount([
                    'documents',
                    'documents as valid_documents_count' => fn ($query) => $this->applyExpiryFilter($query, 'valid'),
                    'documents as to_renew_documents_count' => fn ($query) => $this->applyExpiryFilter($query, 'to_renew'),
                    'documents as expired_documents_count' => fn ($query) => $this->applyExpiryFilter($query, 'expired'),
                ])
                ->tap(fn ($query) => $this->applyDirectoryDocumentFilters($query, $directoryStatus, $reviewFilter))
                ->orderBy('legal_name')
                ->paginate(20)
                ->withQueryString();
        }

        return view('documents.index', [
            'documentRows' => $documentRows,
            'documentPage' => $documentPage,
            'documentSummary' => $documentSummary,
            'companySummary' => $companySummary,
            'fleetSummary' => $fleetSummary,
            'fleetUnits' => $fleetUnits,
            'documentCompanies' => $documentCompanies,
            'reviewDocuments' => $reviewDocuments,
            'pendingReviewCount' => $pendingReviewCount,
            'canViewCompanyDocuments' => $canViewCompanyDocuments,
            'canViewFleetDocuments' => $canViewFleetDocuments,
        ]);
    }

    private function documentSummary(Builder $query): array
    {
        return [
            'total' => (clone $query)->count(),
            'valid' => $this->applyExpiryFilter(clone $query, 'valid')->count(),
            'toRenew' => $this->applyExpiryFilter(clone $query, 'to_renew')->count(),
            'expired' => $this->applyExpiryFilter(clone $query, 'expired')->count(),
        ];
    }

    private function paginatedDocumentRows(
        Request $request,
        Builder $companyQuery,
        Builder $fleetQuery,
        ?string $status,
        ?string $review,
    ): LengthAwarePaginator {
        $companyIndex = $this->applyExpiryFilter($this->applyReviewFilter($companyQuery, $review), $status)
            ->select([
                'company_documents.id',
                DB::raw("'company' as document_source"),
                'company_documents.valid_until',
            ]);
        $fleetIndex = $this->applyExpiryFilter($this->applyReviewFilter($fleetQuery, $review), $status)
            ->select([
                'fleet_documents.id',
                DB::raw("'fleet' as document_source"),
                'fleet_documents.valid_until',
            ]);

        $page = DB::query()
            ->fromSub($companyIndex->unionAll($fleetIndex), 'document_index')
            ->orderBy('valid_until')
            ->orderBy('document_source')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $items = collect($page->items());
        $companyDocuments = CompanyDocument::query()
            ->with(['company', 'documentType'])
            ->whereKey($items->where('document_source', 'company')->pluck('id'))
            ->get()
            ->keyBy('id');
        $fleetDocuments = FleetDocument::query()
            ->with(['fleetUnit.company', 'documentType'])
            ->whereKey($items->where('document_source', 'fleet')->pluck('id'))
            ->get()
            ->keyBy('id');

        $rows = $items->map(function ($item) use ($request, $companyDocuments, $fleetDocuments): array {
            if ($item->document_source === 'company') {
                return $this->companyDocumentRow($request, $companyDocuments->get($item->id));
            }

            return $this->fleetDocumentRow($request, $fleetDocuments->get($item->id));
        });

        return $page->setCollection($rows);
    }

    private function companyDocumentRow(Request $request, CompanyDocument $document): array
    {
        return [
            'id' => $document->id,
            'scope' => 'Compagnie',
            'owner' => $document->company->legal_name,
            'detail' => null,
            'type' => $document->documentType->label,
            'is_required' => $document->documentType->is_required,
            'valid_until' => $document->valid_until,
            'status' => $this->statusService->forDate($document->valid_until),
            'download_url' => route('company-documents.download', $document),
            'preview_url' => route('company-documents.preview', $document),
            'edit_url' => $request->user()->can('update', $document->company) ? route('company-documents.edit', $document) : null,
            'review_status' => $document->review_status,
            'review_comment' => $document->review_comment,
        ];
    }

    private function fleetDocumentRow(Request $request, FleetDocument $document): array
    {
        return [
            'id' => $document->id,
            'scope' => 'Flotte',
            'owner' => $document->fleetUnit->company->legal_name,
            'detail' => trim("{$document->fleetUnit->type} {$document->fleetUnit->registration_number}"),
            'type' => $document->documentType->label,
            'is_required' => $document->documentType->is_required,
            'valid_until' => $document->valid_until,
            'status' => $this->statusService->forDate($document->valid_until),
            'download_url' => route('fleet-documents.download', $document),
            'preview_url' => route('fleet-documents.preview', $document),
            'edit_url' => $request->user()->can('update', $document->fleetUnit) ? route('fleet-documents.edit', $document) : null,
            'review_status' => $document->review_status,
            'review_comment' => $document->review_comment,
        ];
    }

    private function pendingReviewDocuments(Request $request, Builder $companyQuery, Builder $fleetQuery): array
    {
        if ($request->filled('scope')
            || $request->filled('status')
            || ($request->filled('review') && $request->input('review') !== 'pending')
            || ! $request->user()->permitsPlatform('documents.validate')) {
            return [collect(), 0];
        }

        $pendingCount = (clone $companyQuery)->where('review_status', 'pending')->count()
            + (clone $fleetQuery)->where('review_status', 'pending')->count();
        $companyDocuments = $companyQuery
            ->with(['company', 'documentType'])
            ->where('review_status', 'pending')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (CompanyDocument $document) => [
                'document' => $document,
                'owner' => $document->company->legal_name,
                'route' => route('company-documents.review', $document),
            ]);
        $fleetDocuments = $fleetQuery
            ->with(['fleetUnit.company', 'documentType'])
            ->where('review_status', 'pending')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (FleetDocument $document) => [
                'document' => $document,
                'owner' => $document->fleetUnit->company->legal_name.' · '.$document->fleetUnit->registration_number,
                'route' => route('fleet-documents.review', $document),
            ]);

        return [$companyDocuments->concat($fleetDocuments)->sortByDesc(fn ($item) => $item['document']->created_at)->take(20)->values(), $pendingCount];
    }

    private function applyReviewFilter(Builder $query, ?string $review): Builder
    {
        return $query->when($review === 'approved', fn ($query) => $query->where('review_status', 'approved'))
            ->when($review === 'pending', fn ($query) => $query->where(fn ($query) => $query->whereNull('review_status')->orWhere('review_status', 'pending')))
            ->when($review === 'rejected', fn ($query) => $query->where('review_status', 'rejected'));
    }

    private function applyExpiryFilter(Builder $query, ?string $status): Builder
    {
        $today = today();
        $renewalDeadline = today()->addDays(30);

        return $query
            ->when($status === 'expired', fn ($query) => $query->whereDate('valid_until', '<', $today))
            ->when($status === 'to_renew', fn ($query) => $query->whereBetween('valid_until', [$today, $renewalDeadline]))
            ->when($status === 'valid', fn ($query) => $query->whereDate('valid_until', '>', $renewalDeadline));
    }

    private function applyDirectoryDocumentFilters(Builder $query, ?string $status, ?string $review): void
    {
        if (! $status && ! $review) {
            return;
        }

        $query->whereHas('documents', function (Builder $documents) use ($status, $review): void {
            if ($status) {
                $this->applyExpiryFilter($documents, $status);
            }

            $this->applyReviewFilter($documents, $review);
        });

        if ($status === 'valid') {
            $query->whereDoesntHave('documents', fn ($documents) => $documents->whereDate('valid_until', '<=', today()->addDays(30)));
        }
    }

    public function companyDocuments(Request $request, Company $company): View
    {
        if ($request->user()->isPlatformStaff()) {
            abort_unless($request->user()->permitsPlatform('documents.view'), 403);
        } else {
            abort_unless($request->user()->permits('companies.view'), 403);
            Gate::authorize('view', $company);
        }

        $documents = $company->documents()->with('documentType')
            ->when($request->filled('status'), fn ($query) => $this->applyExpiryFilter($query, $request->input('status')))
            ->orderBy('valid_until')->get();
        $documents->each(fn (CompanyDocument $document) => $document->status = $this->statusService->forDate($document->valid_until));

        $requiredDocumentTypes = DocumentType::query()
            ->where('is_required', true)
            ->whereIn('applicable_to', ['company', 'both'])
            ->orderBy('label')
            ->get();
        $requiredDocuments = $company->documents()
            ->whereIn('document_type_id', $requiredDocumentTypes->pluck('id'))
            ->get();
        $requiredDocuments->each(fn (CompanyDocument $document) => $document->status = $this->statusService->forDate($document->valid_until));

        $summary = $this->documentSummary($company->documents()->getQuery());

        return view('documents.company', [
            'company' => $company,
            'documents' => $documents,
            'summary' => $summary,
            'requiredDocumentTypes' => $requiredDocumentTypes,
            'requiredDocuments' => $requiredDocuments,
        ]);
    }

    public function fleetDocuments(Request $request, FleetUnit $fleetUnit): View
    {
        if ($request->user()->isPlatformStaff()) {
            abort_unless($request->user()->permitsPlatform('documents.view'), 403);
        } else {
            abort_unless($request->user()->permits('fleet.view'), 403);
            Gate::authorize('view', $fleetUnit);
        }
        $documents = $fleetUnit->documents()->with('documentType')
            ->when($request->filled('status'), fn ($query) => $this->applyExpiryFilter($query, $request->input('status')))
            ->orderBy('valid_until')->get();
        $documents->each(fn (FleetDocument $document) => $document->status = $this->statusService->forDate($document->valid_until));

        $summary = $this->documentSummary($fleetUnit->documents()->getQuery());

        return view('documents.fleet', [
            'fleetUnit' => $fleetUnit->load('company'),
            'documents' => $documents,
            'summary' => $summary,
        ]);
    }

    public function createForCompany(Company $company): View
    {
        Gate::authorize('update', $company);

        return view('documents.create', [
            'ownerLabel' => $company->legal_name,
            'storeRoute' => route('company-documents.store', $company),
            'documentTypes' => DocumentType::query()
                ->whereIn('applicable_to', ['company', 'both'])
                ->whereDoesntHave('companyDocuments', fn ($query) => $query->where('company_id', $company->id))
                ->orderBy('label')->get(),
            'cancelRoute' => route('companies.show', $company),
        ]);
    }

    public function storeForCompany(StoreCompanyDocumentRequest $request, Company $company): RedirectResponse
    {
        $data = $request->safe()->except('file');
        $validUntil = Carbon::parse($data['valid_until']);
        $data['file_path'] = $request->file('file')->store("documents/companies/{$company->id}", 'local');
        $data['status'] = $this->statusService->forDate($validUntil);

        try {
            $company->documents()->create($data);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($data['file_path']);
            throw $exception;
        }

        return redirect()->route('companies.show', $company)->with('status', 'Le document a été ajouté.');
    }

    public function editCompany(CompanyDocument $companyDocument): View
    {
        Gate::authorize('update', $companyDocument->company);

        return view('documents.create', [
            'document' => $companyDocument,
            'ownerLabel' => $companyDocument->company->legal_name,
            'storeRoute' => route('company-documents.update', $companyDocument),
            'documentTypes' => DocumentType::query()
                ->whereIn('applicable_to', ['company', 'both'])
                ->where(fn ($query) => $query->whereKey($companyDocument->document_type_id)
                    ->orWhereDoesntHave('companyDocuments', fn ($query) => $query->where('company_id', $companyDocument->company_id)))
                ->orderBy('label')->get(),
            'cancelRoute' => route('documents.index'),
        ]);
    }

    public function updateCompany(Request $request, CompanyDocument $companyDocument): RedirectResponse
    {
        Gate::authorize('update', $companyDocument->company);
        $this->replaceDocument($request, $companyDocument, ['company', 'both'], "documents/companies/{$companyDocument->company_id}");

        return redirect()->route('documents.index')->with('status', 'Le document de la compagnie a été mis à jour.');
    }

    public function createForFleetUnit(FleetUnit $fleetUnit): View
    {
        Gate::authorize('update', $fleetUnit);

        return view('documents.create', [
            'ownerLabel' => trim("{$fleetUnit->type} {$fleetUnit->registration_number}"),
            'storeRoute' => route('fleet-documents.store', $fleetUnit),
            'documentTypes' => DocumentType::query()
                ->whereIn('applicable_to', ['fleet_unit', 'both'])
                ->whereDoesntHave('fleetDocuments', fn ($query) => $query->where('fleet_unit_id', $fleetUnit->id))
                ->orderBy('label')->get(),
            'cancelRoute' => route('fleet-units.show', $fleetUnit),
        ]);
    }

    public function storeForFleetUnit(StoreFleetDocumentRequest $request, FleetUnit $fleetUnit): RedirectResponse
    {
        $data = $request->safe()->except('file');
        $validUntil = Carbon::parse($data['valid_until']);
        $data['file_path'] = $request->file('file')->store("documents/fleet/{$fleetUnit->id}", 'local');
        $data['status'] = $this->statusService->forDate($validUntil);

        try {
            $fleetUnit->documents()->create($data);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($data['file_path']);
            throw $exception;
        }

        return redirect()->route('fleet-units.show', $fleetUnit)->with('status', 'Le document a été ajouté.');
    }

    public function editFleet(FleetDocument $fleetDocument): View
    {
        Gate::authorize('update', $fleetDocument->fleetUnit);

        return view('documents.create', [
            'document' => $fleetDocument,
            'ownerLabel' => trim("{$fleetDocument->fleetUnit->type} {$fleetDocument->fleetUnit->registration_number}"),
            'storeRoute' => route('fleet-documents.update', $fleetDocument),
            'documentTypes' => DocumentType::query()
                ->whereIn('applicable_to', ['fleet_unit', 'both'])
                ->where(fn ($query) => $query->whereKey($fleetDocument->document_type_id)
                    ->orWhereDoesntHave('fleetDocuments', fn ($query) => $query->where('fleet_unit_id', $fleetDocument->fleet_unit_id)))
                ->orderBy('label')->get(),
            'cancelRoute' => route('fleet-units.show', $fleetDocument->fleetUnit),
        ]);
    }

    public function updateFleet(Request $request, FleetDocument $fleetDocument): RedirectResponse
    {
        Gate::authorize('update', $fleetDocument->fleetUnit);
        $this->replaceDocument($request, $fleetDocument, ['fleet_unit', 'both'], "documents/fleet/{$fleetDocument->fleet_unit_id}");

        return redirect()->route('fleet-units.show', $fleetDocument->fleetUnit)->with('status', 'Le document de la flotte a été mis à jour.');
    }

    public function downloadCompany(Request $request, CompanyDocument $companyDocument): StreamedResponse
    {
        $this->authorizeDocumentView($request, $companyDocument->company);

        abort_unless(Storage::disk('local')->exists($companyDocument->file_path), 404);

        return Storage::disk('local')->download($companyDocument->file_path);
    }

    public function downloadFleet(Request $request, FleetDocument $fleetDocument): StreamedResponse
    {
        $this->authorizeDocumentView($request, $fleetDocument->fleetUnit);

        abort_unless(Storage::disk('local')->exists($fleetDocument->file_path), 404);

        return Storage::disk('local')->download($fleetDocument->file_path);
    }

    public function previewCompany(Request $request, CompanyDocument $companyDocument): StreamedResponse
    {
        $this->authorizeDocumentView($request, $companyDocument->company);
        AuditLogger::record('consultation', 'Consultation d’un document de compagnie', $companyDocument);

        return $this->inlineResponse($companyDocument->file_path);
    }

    public function previewFleet(Request $request, FleetDocument $fleetDocument): StreamedResponse
    {
        $this->authorizeDocumentView($request, $fleetDocument->fleetUnit);
        AuditLogger::record('consultation', 'Consultation d’un document de flotte', $fleetDocument);

        return $this->inlineResponse($fleetDocument->file_path);
    }

    private function inlineResponse(string $path): StreamedResponse
    {
        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);
        $mime = $disk->mimeType($path);
        abort_unless(in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true), 415);

        return $disk->response($path, basename($path), [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'",
        ], 'inline');
    }

    private function authorizeDocumentView(Request $request, Company|FleetUnit $owner): void
    {
        if ($request->user()->isPlatformStaff()) {
            abort_unless($request->user()->permitsPlatform('documents.view'), 403);

            return;
        }

        $permission = $owner instanceof Company ? 'companies.view' : 'fleet.view';
        abort_unless($request->user()->permits($permission), 403);

        Gate::authorize('view', $owner);
    }

    private function replaceDocument(Request $request, CompanyDocument|FleetDocument $document, array $applicableTo, string $directory): void
    {
        $data = $request->validate([
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')->whereIn('applicable_to', $applicableTo),
                Rule::unique($document->getTable(), 'document_type_id')
                    ->where($document instanceof CompanyDocument ? 'company_id' : 'fleet_unit_id', $document instanceof CompanyDocument ? $document->company_id : $document->fleet_unit_id)
                    ->ignore($document->id),
            ],
            'file' => [$document->review_status === 'rejected' ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'issued_at' => ['nullable', 'date', 'before_or_equal:today'],
            'valid_until' => ['required', 'date', 'after_or_equal:issued_at'],
        ], [], ['document_type_id' => 'type de document']);
        $documentIdentityChanged = (int) $data['document_type_id'] !== $document->document_type_id
            || ($data['issued_at'] ?? null) !== $document->issued_at?->toDateString()
            || $data['valid_until'] !== $document->valid_until->toDateString();
        if ($documentIdentityChanged && ! $request->hasFile('file')) {
            throw ValidationException::withMessages([
                'file' => 'Un nouveau fichier est obligatoire lorsque le type ou les dates du document changent.',
            ]);
        }
        $oldPath = $document->file_path;
        $newPath = null;

        if ($request->hasFile('file')) {
            $newPath = $request->file('file')->store($directory, 'local');
            $data['file_path'] = $newPath;
        }

        $data['status'] = $this->statusService->forDate(Carbon::parse($data['valid_until']));
        if ($newPath) {
            $data['review_status'] = 'pending';
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
            $data['review_comment'] = null;
        }
        try {
            $document->update($data);
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $newPath !== $oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        $scope = $document instanceof CompanyDocument ? 'company' : 'fleet';
        $request->user()->unreadNotifications
            ->filter(fn ($notification) => ($notification->data['document_scope'] ?? null) === $scope
                && (int) ($notification->data['document_id'] ?? 0) === $document->id)
            ->each->markAsRead();
    }
}
