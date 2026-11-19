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

        $company = Company::find($companyId);

        return view('lines.create', [
            'line' => new Line,
            'company' => $company,
            'transportModes' => $company?->transportModes()->orderBy('label')->get() ?? collect(),
            'places' => Place::query()
                ->orderBy('city')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreLineRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['places']);

        $stops = collect($request->validated('places'))
            ->mapWithKeys(fn ($stop) => [
                (int) $stop['place_id'] => ['stop_order' => (int) $stop['stop_order']],
            ])
            ->all();

        $line = DB::transaction(function () use ($data, $stops) {
            $line = Line::create($data);
            $line->places()->sync($stops);

            return $line;
        });

        return redirect()->route('lines.show', $line)
            ->with('status', 'La ligne a été créée.');
    }

    public function show(Line $line): View
    {
        Gate::authorize('view', $line);

        $line->load(['company', 'transportMode', 'originPlace', 'destinationPlace', 'places', 'departures', 'fares']);

        $age = request()->integer('age') ?: null;
        $pricing = app(\App\Services\FarePricing::class);
        $classes = config('offre.classes');
        $arrets = $line->places->values();
        $trajets = [];

        for ($i = 0; $i < $arrets->count(); $i++) {
            for ($j = $i + 1; $j < $arrets->count(); $j++) {
                $prix = [];
                foreach ($classes as $code => $libelle) {
                    $prix[$code] = $pricing->prixTrajet($line, $arrets[$i], $arrets[$j], $code, $age);
                }
                $trajets[] = ['de' => $arrets[$i]->name, 'a' => $arrets[$j]->name, 'prix' => $prix];
            }
        }

        return view('lines.show', [
            'line' => $line,
            'trajets' => $trajets,
            'classes' => $classes,
            'age' => $age,
        ]);
    }

    public function edit(Line $line): View
    {
        Gate::authorize('update', $line);

        return view('lines.edit', [
            'line' => $line->load(['places' => fn ($q) => $q->orderByPivot('stop_order')]),
            'company' => $line->company,
            'transportModes' => TransportMode::query()->orderBy('label')->get(),
            'places' => Place::query()->where('province', $line->company?->province)->whereColumn('name', '!=', 'city')->orderBy('city')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateLineRequest $request, Line $line): RedirectResponse
    {
        $data = $request->safe()->except(['places', 'company_id']);

        $stops = collect($request->validated('places'))
            ->mapWithKeys(fn ($stop) => [
                (int) $stop['place_id'] => ['stop_order' => (int) $stop['stop_order']],
            ])
            ->all();

        DB::transaction(function () use ($line, $data, $stops) {
            $line->update($data);
            $line->places()->sync($stops);
        });

        return redirect()->route('lines.show', $line)
            ->with('status', 'La ligne a été mise à jour.');
    }

    public function updateStatus(UpdateLineStatusRequest $request, Line $line): RedirectResponse
    {
        Gate::authorize('update', $line);

        $line->update($request->validated());

        return redirect()->back()
            ->with('status', 'Le statut de la ligne a été modifié.');
    }
}
