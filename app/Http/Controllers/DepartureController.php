<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departure\CancelDepartureRequest;
use App\Http\Requests\Departure\StoreDepartureRequest;
use App\Http\Requests\Departure\UpdateDepartureRequest;
use App\Models\Agency;
use App\Models\Departure;
use App\Models\FleetUnit;
use App\Models\Line;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DepartureController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Departure::class);

        $query = Departure::query()
            ->with(['line.company', 'line.transportMode', 'fleetUnit', 'agency'])
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->when(! $request->user()->isAdmin(), function ($query) use ($request) {
                if ($request->user()->accessScope() === 'agency') {
                    $query->where('agency_id', $request->user()->employee?->agency_id);
                } else {
                    $query->whereHas('line', fn ($q) => $q->where('company_id', $request->user()->company_id));
                }
            })
            ->when($request->filled('line_id'), fn ($query) => $query->where('line_id', $request->integer('line_id')))
            ->when($request->filled('agency_id') && $request->user()->accessScope() !== 'agency', fn ($query) => $query->where('agency_id', $request->integer('agency_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('departure_date')->orderBy('departure_time');

        return view('departures.index', [
            'departures' => $query->paginate(15)->withQueryString(),
            'lines' => Line::query()
                ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('company_id', $request->user()->company_id))
                ->orderBy('label')
                ->get(),
            'agencies' => $request->user()->accessScope() !== 'agency'
                ? Agency::query()
                    ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('company_id', $request->user()->company_id))
                    ->orderBy('name')
                    ->get()
                : collect(),
            'statuses' => ['scheduled', 'cancelled'],
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Departure::class);

        $companyId = $request->user()->isAdmin()
            ? $request->integer('company_id')
            : $request->user()->company_id;

        return view('departures.create', [
            'departure' => new Departure,
            'lines' => Line::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderBy('label')
                ->get(),
            'fleetUnits' => FleetUnit::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->where('availability', 'available')
                ->with('transportMode')
                ->orderBy('type')
                ->get(),
            'agencies' => Agency::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreDepartureRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('company_id');
        $data['status'] = 'scheduled';

        $departure = Departure::create($data);

        return redirect()->route('departures.show', $departure)
            ->with('status', 'Le départ a été créé.');
    }

    public function show(Departure $departure): View
    {
        Gate::authorize('view', $departure);

        if (! auth()->user()->isAdmin() && ! $this->userCanAccessDeparture($departure)) {
            abort(403);
        }

        $departure->load(['line.company', 'line.transportMode', 'line.originPlace', 'line.destinationPlace', 'line.places', 'fleetUnit', 'agency']);

        return view('departures.show', [
            'departure' => $departure,
        ]);
    }

    public function edit(Departure $departure): View
    {
        Gate::authorize('update', $departure);

        if (! auth()->user()->isAdmin() && ! $this->userCanAccessDeparture($departure)) {
            abort(403);
        }

        $companyId = $departure->line->company_id;

        return view('departures.edit', [
            'departure' => $departure,
            'lines' => Line::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderBy('label')
                ->get(),
            'fleetUnits' => FleetUnit::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->where('availability', 'available')
                ->with('transportMode')
                ->orderBy('type')
                ->get(),
            'agencies' => Agency::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateDepartureRequest $request, Departure $departure): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! $this->userCanAccessDeparture($departure)) {
            abort(403);
        }

        $departure->update($request->validated());

        return redirect()->route('departures.show', $departure)
            ->with('status', 'Le départ a été mis à jour.');
    }

    public function cancel(CancelDepartureRequest $request, Departure $departure): RedirectResponse
    {
        Gate::authorize('update', $departure);

        if (! auth()->user()->isAdmin() && ! $this->userCanAccessDeparture($departure)) {
            abort(403);
        }

        $departure->update($request->validated());

        return redirect()->back()
            ->with('status', 'Le départ a été annulé.');
    }

    private function userCanAccessDeparture(Departure $departure): bool
    {
        $user = auth()->user();
        $companyId = $departure->line->company_id;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->company_id !== $companyId) {
            return false;
        }

        if ($user->accessScope() === 'agency') {
            return $departure->agency_id === $user->employee?->agency_id;
        }

        return true;
    }
}
