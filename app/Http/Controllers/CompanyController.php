<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\DocumentType;
use App\Models\TransportMode;
use App\Services\DocumentStatusService;
use App\Services\HeadquartersService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Company::class);

        $query = Company::query()
            ->with('transportModes')
            ->withCount([
                'agencies',
                'employees' => fn ($query) => $query->whereHas('user', fn ($users) => $users->where('status', 'active')),
                'fleetUnits',
            ])
            ->when(! $request->user()->isPlatformMember(), fn ($query) => $query->whereKey($request->user()->company_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($query) use ($search) {
                    $query->where('legal_name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('rccm', 'like', "%{$search}%")
                        ->orWhere('nif', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('city'), fn ($query) => $query->where('city', 'like', '%'.$request->input('city').'%'))
            ->when($request->filled('transport_mode'), function ($query) use ($request) {
                $query->whereHas('transportModes', fn ($query) => $query->whereKey($request->integer('transport_mode')));
            })
            ->orderBy('legal_name');

        return view('companies.index', [
            'companies' => $query->paginate(15)->withQueryString(),
            'transportModes' => $this->supportedTransportModes(),
            'statuses' => ['pending', 'validated', 'rejected', 'active', 'suspended'],
            'companySummary' => $request->user()->isPlatformMember()
                ? Company::query()
                    ->selectRaw('COUNT(*) as total')
                    ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('active', 'validated') THEN 1 ELSE 0 END), 0) as approved")
                    ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending")
                    ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('suspended', 'rejected') THEN 1 ELSE 0 END), 0) as restricted")
                    ->first()
                : null,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Company::class);

        return view('companies.create', [
            'company' => new Company,
            'transportModes' => $this->supportedTransportModes(),
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $company = DB::transaction(function () use ($request) {
            $company = Company::create($request->safe()->except('transport_mode_id'));
            $company->transportModes()->sync([$request->integer('transport_mode_id')]);
            app(HeadquartersService::class)->create($company);

            return $company;
        });

        return redirect()->route('companies.show', $company)
            ->with('status', 'La compagnie a été enregistrée et placée en attente de validation.');
    }

    public function show(Company $company, DocumentStatusService $documentStatusService): View
    {
        Gate::authorize('view', $company);

        $company->load(['transportModes', 'agencies', 'employees.user', 'fleetUnits', 'documents.documentType', 'statusHistory.decidedByUser']);
        $company->setRelation('employees', $company->employees
            ->filter(fn ($employee) => $employee->user?->status === 'active')
            ->values());
        $company->documents->each(
            fn ($document) => $document->status = $documentStatusService->forDate($document->valid_until)
        );
        if (auth()->user()->custom_role_id) {
            foreach (['agencies', 'employees', 'fleetUnits'] as $relation) {
                $company->setRelation($relation, $company->$relation->filter(fn ($item) => auth()->user()->can('view', $item)));
            }
        }

        return view('companies.show', [
            'company' => $company,
            'requiredDocumentTypes' => DocumentType::query()
                ->where('is_required', true)
                ->whereIn('applicable_to', ['company', 'both'])
                ->orderBy('label')
                ->get(),
        ]);
    }

    public function edit(Company $company): View
    {
        Gate::authorize('update', $company);

        return view('companies.edit', [
            'company' => $company->load('transportModes'),
            'transportModes' => $this->supportedTransportModes(),
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->safe()->except('transport_mode_id'));
        $company->transportModes()->sync([$request->integer('transport_mode_id')]);

        return redirect()->route('companies.show', $company)
            ->with('status', 'Les informations de la compagnie ont été mises à jour.');
    }

    private function supportedTransportModes(): Collection
    {
        return TransportMode::query()
            ->where('label', '!=', 'air')
            ->orderBy('label')
            ->get();
    }
}
