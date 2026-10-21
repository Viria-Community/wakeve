<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleetUnit\StoreFleetUnitRequest;
use App\Http\Requests\FleetUnit\UpdateFleetUnitRequest;
use App\Models\Company;
use App\Models\FleetUnit;
use App\Models\TransportMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FleetUnitController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', FleetUnit::class);

        $query = FleetUnit::query()
            ->with(['company', 'transportMode'])
            ->withCount('documents')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('company_id', $request->user()->company_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(fn ($query) => $query->where('type', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%"));
            })
            ->when($request->filled('transport_mode_id'), fn ($query) => $query->where('transport_mode_id', $request->integer('transport_mode_id')))
            ->when($request->filled('availability'), fn ($query) => $query->where('availability', $request->input('availability')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('company_id') && $request->user()->isAdmin(), fn ($query) => $query->where('company_id', $request->integer('company_id')))
            ->orderBy('type')->orderBy('registration_number');

        $summaryQuery = FleetUnit::query()
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('company_id', $request->user()->company_id));

        return view('fleet.index', [
            'transportModes' => TransportMode::query()->whereHas('companies', fn ($q) => $q->when(! $request->user()->isAdmin(), fn ($q) => $q->where('companies.id', $request->user()->company_id)))->get(),
            'fleetUnits' => $query->paginate(15)->withQueryString(),
            'companies' => $request->user()->isAdmin()
                ? Company::query()->with('transportModes')->orderBy('legal_name')->get()
                : collect(),
            'fleetSummary' => $summaryQuery->selectRaw('COUNT(*) as total')
                ->selectRaw("COALESCE(SUM(CASE WHEN availability = 'available' AND status = 'active' THEN 1 ELSE 0 END), 0) as available")
                ->selectRaw("COALESCE(SUM(CASE WHEN availability = 'unavailable' OR status = 'inactive' THEN 1 ELSE 0 END), 0) as unavailable")
                ->selectRaw('COALESCE(SUM(capacity), 0) as capacity')->first(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', FleetUnit::class);

        return view('fleet.create', [
            'fleetUnit' => new FleetUnit,
            'companies' => $request->user()->isAdmin()
                ? Company::query()->with('transportModes')->orderBy('legal_name')->get()
                : collect(),
        ]);
    }

    public function store(StoreFleetUnitRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('company_id');
        $data['company_id'] = $request->user()->isAdmin()
            ? $request->validated('company_id')
            : $request->user()->company_id;

        $fleetUnit = FleetUnit::create($data);

        return redirect()->route('fleet-units.show', $fleetUnit)
            ->with('status', 'L’unité de flotte a été ajoutée.');
    }

    public function show(FleetUnit $fleetUnit): View
    {
        Gate::authorize('view', $fleetUnit);

        return view('fleet.show', [
            'fleetUnit' => $fleetUnit->load(['company', 'documents.documentType']),
        ]);
    }

    public function edit(FleetUnit $fleetUnit): View
    {
        Gate::authorize('update', $fleetUnit);

        return view('fleet.edit', compact('fleetUnit'));
    }

    public function update(UpdateFleetUnitRequest $request, FleetUnit $fleetUnit): RedirectResponse
    {
        $fleetUnit->update($request->validated());

        return redirect()->route('fleet-units.show', $fleetUnit)
            ->with('status', 'L’unité de flotte a été mise à jour.');
    }
}
