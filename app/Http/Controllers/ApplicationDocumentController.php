<?php

namespace App\Http\Controllers;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Services\AuditLogger;
use App\Services\DocumentStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ApplicationDocumentController extends Controller
{
    public function __construct(private readonly DocumentStatusService $statusService) {}

    public function store(Request $request): RedirectResponse
    {
        $company = $this->pendingCompany($request);
        $data = $this->validatedData($request, $company, true);
        $data['file_path'] = $request->file('file')->store("documents/companies/{$company->id}", 'local');
        $data['status'] = $this->statusService->forDate(Carbon::parse($data['valid_until']));

        try {
            $company->documents()->create($data);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($data['file_path']);
            throw $exception;
        }

        return redirect()->route('application.status')->with('status', 'Le document a été ajouté au dossier.');
    }

    public function update(Request $request, CompanyDocument $companyDocument): RedirectResponse
    {
        $company = $this->pendingCompany($request);
        abort_unless($companyDocument->company_id === $company->id, 403);

        $data = $this->validatedData($request, $company, $companyDocument->review_status === 'rejected', $companyDocument);
        $documentIdentityChanged = (int) $data['document_type_id'] !== $companyDocument->document_type_id
            || ($data['issued_at'] ?? null) !== $companyDocument->issued_at?->toDateString()
            || $data['valid_until'] !== $companyDocument->valid_until->toDateString();
        if ($documentIdentityChanged && ! $request->hasFile('file')) {
            throw ValidationException::withMessages([
                'file' => 'Un nouveau fichier est obligatoire lorsque le type ou les dates du document changent.',
            ]);
        }
        $oldPath = $companyDocument->file_path;
        $newPath = null;

        if ($request->hasFile('file')) {
            $newPath = $request->file('file')->store("documents/companies/{$company->id}", 'local');
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
            $companyDocument->update($data);
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        $request->user()->unreadNotifications
            ->filter(fn ($notification) => ($notification->data['document_scope'] ?? null) === 'company'
                && (int) ($notification->data['document_id'] ?? 0) === $companyDocument->id)
            ->each->markAsRead();

        $message = $newPath
            ? 'Le document a été remplacé et sera contrôlé à nouveau.'
            : 'Les informations du document ont été enregistrées.';

        return redirect()->route('application.status')->with('status', $message);
    }

    public function download(Request $request, CompanyDocument $companyDocument): StreamedResponse
    {
        $company = $this->pendingCompany($request);
        abort_unless($companyDocument->company_id === $company->id, 403);
        abort_unless(Storage::disk('local')->exists($companyDocument->file_path), 404);

        return Storage::disk('local')->download($companyDocument->file_path);
    }

    public function preview(Request $request, CompanyDocument $companyDocument): StreamedResponse
    {
        $company = $this->pendingCompany($request);
        abort_unless($companyDocument->company_id === $company->id, 403);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($companyDocument->file_path), 404);
        $mime = $disk->mimeType($companyDocument->file_path);
        abort_unless(in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true), 415);
        AuditLogger::record('consultation', 'Consultation d’un document du dossier d’inscription', $companyDocument);

        return $disk->response($companyDocument->file_path, basename($companyDocument->file_path), [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'",
        ], 'inline');
    }

    private function pendingCompany(Request $request)
    {
        $user = $request->user();
        abort_unless($user->status === 'active' && $user->permits($request->isMethod('GET') ? 'companies.view' : 'companies.update'), 403);
        abort_unless($user?->role === 'company_manager' && $user->company_id !== null, 403);
        abort_if($user->company?->status === CompanyStatus::Active, 404);

        return $user->company;
    }

    private function validatedData(Request $request, Company $company, bool $fileRequired, ?CompanyDocument $document = null): array
    {
        return $request->validate([
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')->whereIn('applicable_to', ['company', 'both']),
                Rule::unique('company_documents', 'document_type_id')
                    ->where('company_id', $company->id)
                    ->ignore($document?->id),
            ],
            'file' => [$fileRequired ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'issued_at' => ['nullable', 'date', 'before_or_equal:today'],
            'valid_until' => ['required', 'date', 'after_or_equal:issued_at'],
        ], [], ['document_type_id' => 'type de document']);
    }
}
