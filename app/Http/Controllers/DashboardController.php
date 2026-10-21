<?php

namespace App\Http\Controllers;

use App\Enums\CompanyStatus;
use App\Models\Agency;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\FleetDocument;
use App\Models\FleetUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        if ($request->user()->isPlatformStaff()) {
            return view('platform-staff.dashboard', [
                'pendingCompanies' => Company::query()->where('status', CompanyStatus::Pending->value)->count(),
                'activeCompanies' => Company::query()->where('status', CompanyStatus::Active->value)->count(),
                'pendingDocuments' => CompanyDocument::query()->where('review_status', 'pending')->count()
                    + FleetDocument::query()->where('review_status', 'pending')->count(),
            ]);
        }
        if ($request->user()->custom_role_id && ! $request->user()->isAdmin()) {
            return view('roles.dashboard');
        }
        if (! $request->user()->isAdmin()) {
            if ($request->user()->role !== 'company_manager' || ! $request->user()->company_id) {
                return view('dashboard');
            }

            $company = Company::query()
                ->withCount([
                    'agencies',
                    'employees' => fn ($query) => $query->whereHas('user', fn ($users) => $users->where('status', 'active')),
                    'fleetUnits',
                ])
                ->findOrFail($request->user()->company_id);

            $availableFleetCount = $company->fleetUnits()->where('availability', 'available')->count();
            $unavailableFleetCount = $company->fleetUnits()->where('availability', 'unavailable')->count();

            $companyDocuments = $company->documents()
                ->with('documentType')
                ->orderBy('valid_until')
                ->get()
                ->map(fn (CompanyDocument $document) => [
                    'type' => $document->documentType->label,
                    'valid_until' => $document->valid_until,
                    'status' => $document->valid_until->isBefore(today())
                        ? 'expired'
                        : ($document->valid_until->lte(today()->addDays(30)) ? 'to_renew' : 'valid'),
                    'url' => route('companies.show', $company),
                ]);

            $fleetDocuments = FleetDocument::query()
                ->with(['documentType', 'fleetUnit'])
                ->whereHas('fleetUnit', fn ($query) => $query->where('company_id', $company->id))
                ->orderBy('valid_until')
                ->get()
                ->map(fn (FleetDocument $document) => [
                    'type' => $document->documentType->label.' · '.$document->fleetUnit->type,
                    'valid_until' => $document->valid_until,
                    'status' => $document->valid_until->isBefore(today())
                        ? 'expired'
                        : ($document->valid_until->lte(today()->addDays(30)) ? 'to_renew' : 'valid'),
                    'url' => route('fleet-units.show', $document->fleetUnit),
                ]);

            return view('dashboard', [
                'company' => $company,
                'availableFleetCount' => $availableFleetCount,
                'unavailableFleetCount' => $unavailableFleetCount,
                'documentOverview' => $companyDocuments->concat($fleetDocuments)->sortBy('valid_until')->take(4)->values(),
                'recentFleetUnits' => $company->fleetUnits()->latest()->take(3)->get(),
            ]);
        }

        $companyCounts = Company::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $metrics = [
            'total_companies' => $companyCounts->sum(),
            'pending_companies' => (int) $companyCounts->get(CompanyStatus::Pending->value, 0),
            'active_companies' => (int) $companyCounts->get(CompanyStatus::Active->value, 0),
            'suspended_companies' => (int) $companyCounts->get(CompanyStatus::Suspended->value, 0),
            'rejected_companies' => (int) $companyCounts->get(CompanyStatus::Rejected->value, 0),
            'total_agencies' => Agency::query()->count(),
            'total_fleet_units' => FleetUnit::query()->count(),
        ];

        $recentCompanies = Company::query()
            ->with('transportModes')
            ->latest()
            ->take(5)
            ->get();

        $documentAlerts = $this->documentAlerts();
        $suspendedCompanies = Company::query()
            ->where('status', CompanyStatus::Suspended->value)
            ->with(['statusHistory' => fn ($query) => $query
                ->where('new_status', CompanyStatus::Suspended->value)
                ->with('decidedByUser')])
            ->latest('updated_at')
            ->take(5)
            ->get();
        $documentAlertGroups = $documentAlerts
            ->groupBy('company_id')
            ->map(fn (Collection $alerts) => [
                'company' => $alerts->first()['owner'],
                'url' => $alerts->first()['company_url'],
                'expired' => $alerts->where('status', 'expired')->count(),
                'to_renew' => $alerts->where('status', 'to_renew')->count(),
                'nearest_date' => $alerts->min('valid_until'),
                'documents' => $alerts->values(),
            ])
            ->sortByDesc('expired')
            ->take(5)
            ->values();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'recentCompanies' => $recentCompanies,
            'documentAlerts' => $documentAlerts,
            'documentAlertGroups' => $documentAlertGroups,
            'expiredDocumentsCount' => $this->expiredDocumentsCount(),
            'expiringDocumentsCount' => $this->expiringDocumentsCount(),
            'documentsAddedToday' => CompanyDocument::query()->whereDate('created_at', today())->count()
                + FleetDocument::query()->whereDate('created_at', today())->count(),
            'suspendedCompanies' => $suspendedCompanies,
            'recentAuditLogs' => AuditLog::query()->with(['user', 'company'])->latest()->take(8)->get(),
        ]);
    }

    private function expiredDocumentsCount(): int
    {
        return CompanyDocument::query()->whereDate('valid_until', '<', today())->count()
            + FleetDocument::query()->whereDate('valid_until', '<', today())->count();
    }

    private function expiringDocumentsCount(): int
    {
        $deadline = today()->addDays(30);

        return CompanyDocument::query()->whereBetween('valid_until', [today(), $deadline])->count()
            + FleetDocument::query()->whereBetween('valid_until', [today(), $deadline])->count();
    }

    private function documentAlerts(): Collection
    {
        $deadline = today()->addDays(30);

        $companyDocuments = CompanyDocument::query()
            ->with(['company', 'documentType'])
            ->whereDate('valid_until', '<=', $deadline)
            ->orderBy('valid_until')
            ->limit(8)
            ->get()
            ->map(fn (CompanyDocument $document) => [
                'owner' => $document->company->legal_name,
                'company_id' => $document->company_id,
                'company_url' => route('companies.show', $document->company),
                'type' => $document->documentType->label,
                'scope' => 'Compagnie',
                'valid_until' => $document->valid_until,
                'status' => $document->valid_until->isBefore(today()) ? 'expired' : 'to_renew',
                'url' => route('companies.show', $document->company),
            ]);

        $fleetDocuments = FleetDocument::query()
            ->with(['fleetUnit.company', 'documentType'])
            ->whereDate('valid_until', '<=', $deadline)
            ->orderBy('valid_until')
            ->limit(8)
            ->get()
            ->map(fn (FleetDocument $document) => [
                'owner' => $document->fleetUnit->company->legal_name,
                'company_id' => $document->fleetUnit->company_id,
                'company_url' => route('companies.show', $document->fleetUnit->company),
                'type' => $document->documentType->label.' · '.$document->fleetUnit->type,
                'scope' => 'Flotte',
                'valid_until' => $document->valid_until,
                'status' => $document->valid_until->isBefore(today()) ? 'expired' : 'to_renew',
                'url' => route('fleet-units.show', $document->fleetUnit),
            ]);

        return $companyDocuments
            ->concat($fleetDocuments)
            ->sortBy('valid_until')
            ->take(8)
            ->values();
    }
}
