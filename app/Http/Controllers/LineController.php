<?php

namespace App\Http\Controllers;

use App\Http\Requests\Line\StoreLineRequest;
use App\Http\Requests\Line\UpdateLineRequest;
use App\Http\Requests\Line\UpdateLineStatusRequest;
use App\Models\Company;
use App\Models\Line;
use App\Models\Place;
use App\Models\TransportMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LineController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Line::class);

        $query = Line::query()
            ->with(['company', 'transportMode', 'originPlace', 'destinationPlace'])
            ->when(! $request->user()->isAdmin(), function ($query) use ($request) {
                $query->where('company_id', $request->user()->company_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('label', 'like', '%' . trim($request->input('search')) . '%');
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('company_id') && $request->user()->isAdmin(), fn ($query) => $query->where('company_id', $request->integer('company_id')))
            ->when($request->filled('transport_mode_id'), fn ($query) => $query->where('transport_mode_id', $request->integer('transport_mode_id')))
            ->orderBy('label');

        return view('lines.index', [
            'lines' => $query->paginate(15)->withQueryString(),
            'companies' => $request->user()->isAdmin()
                ? Company::query()->orderBy('legal_name')->get()
                : collect(),
            'transportModes' => TransportMode::query()->orderBy('label')->get(),
            'statuses' => ['active', 'inactive'],
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Line::class);

        $companyId = $request->user()->isAdmin()
            ? $request->integer('company_id')
            : $request->user()->company_id;

        return view('lines.create', [
            'line' => new Line,
            'company' => Company::find($companyId),
            'transportModes' => TransportMode::query()->orderBy('label')->get(),
            'places' => Place::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreLineRequest $request): RedirectResponse
    {
        $companyId = $request->user()->isAdmin()
            ? $request->validated('company_id')
            : $request->user()->company_id;

        $data = $request->safe()->except(['places', 'company_id']);
        $data['company_id'] = $companyId;

        DB::transaction(function () use ($request, $data) {
            $line = Line::create($data);

            $placesData = $request->input('places', []);
            $line->places()->syncWithPivotValues($placesData, [], function ($pivot, $id) {
                return ['stop_order' => (int) $pivot['stop_order']];
            });
        });

        return redirect()->route('lines.show', $line)
            ->with('status', 'La ligne a été créée.');
    }

    public function show(Line $line): View
    {
        Gate::authorize('view', $line);

        $line->load(['company', 'transportMode', 'originPlace', 'destinationPlace', 'places', 'departures', 'fares']);

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('lines.show', [
            'line' => $line,
        ]);
    }

    public function edit(Line $line): View
    {
        Gate::authorize('update', $line);

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('lines.edit', [
            'line' => $line->load(['places' => fn ($q) => $q->orderByPivot('stop_order')]),
            'transportModes' => TransportMode::query()->orderBy('label')->get(),
            'places' => Place::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateLineRequest $request, Line $line): RedirectResponse
    {
        $companyId = $request->user()->isAdmin()
            ? $request->validated('company_id')
            : $request->user()->company_id;

        if ($line->company_id !== $companyId && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->safe()->except('places');
        $line->update($data);

        $placesData = $request->input('places', []);
        $line->places()->syncWithPivotValues($placesData, [], function ($pivot, $id) {
            return ['stop_order' => (int) $pivot['stop_order']];
        });

        return redirect()->route('lines.show', $line)
            ->with('status', 'La ligne a été mise à jour.');
    }

    public function updateStatus(UpdateLineStatusRequest $request, Line $line): RedirectResponse
    {
        Gate::authorize('update', $line);

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $line->update($request->validated());

        return redirect()->back()
            ->with('status', 'Le statut de la ligne a été modifié.');
    }
}
