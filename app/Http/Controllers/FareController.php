<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fare\StoreFareRequest;
use App\Http\Requests\Fare\UpdateFareRequest;
use App\Http\Requests\Fare\UpdateFareStatusRequest;
use App\Models\Fare;
use App\Models\Line;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FareController extends Controller
{
    public function index(Request $request, Line $line): View
    {
        Gate::authorize('viewAny', Fare::class);

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $query = Fare::query()
            ->where('line_id', $line->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('amount')
            ->orderBy('fare_class');

        return view('fares.index', [
            'line' => $line,
            'fares' => $query->paginate(15)->withQueryString(),
            'statuses' => ['active', 'inactive'],
        ]);
    }

    public function create(Request $request, Line $line): View
    {
        Gate::authorize('create', Fare::class);

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('fares.create', [
            'line' => $line,
            'fare' => new Fare,
        ]);
    }

    public function store(StoreFareRequest $request, Line $line): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $data = $request->safe()->except('line_id');
        $data['line_id'] = $line->id;

        $fare = Fare::create($data);

        return redirect()->route('fares.index', $line)
            ->with('status', 'Le tarif a été créé.');
    }

    public function edit(Request $request, Line $line, Fare $fare): View
    {
        Gate::authorize('update', $fare);

        if (! auth()->user()->isAdmin() && $fare->line_id !== $line->id) {
            abort(403);
        }

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('fares.edit', [
            'line' => $line,
            'fare' => $fare,
        ]);
    }

    public function update(UpdateFareRequest $request, Line $line, Fare $fare): RedirectResponse
    {
        Gate::authorize('update', $fare);

        if (! auth()->user()->isAdmin() && $fare->line_id !== $line->id) {
            abort(403);
        }

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $fare->update($request->validated());

        return redirect()->route('fares.index', $line)
            ->with('status', 'Le tarif a été mis à jour.');
    }

    public function updateStatus(UpdateFareStatusRequest $request, Line $line, Fare $fare): RedirectResponse
    {
        Gate::authorize('update', $fare);

        if (! auth()->user()->isAdmin() && $fare->line_id !== $line->id) {
            abort(403);
        }

        if (! auth()->user()->isAdmin() && $line->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $fare->update($request->validated());

        return redirect()->back()
            ->with('status', 'Le statut du tarif a été modifié.');
    }
}
