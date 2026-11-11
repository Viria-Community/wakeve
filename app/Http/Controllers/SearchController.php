<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Departure;
use App\Models\Line;
use App\Models\Place;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $linesQuery = Line::query()
            ->with(['company', 'transportMode', 'originPlace', 'destinationPlace'])
            ->where('status', 'active');

        $departuresQuery = Departure::query()
            ->with(['line.company', 'line.transportMode', 'line.originPlace', 'line.destinationPlace', 'fleetUnit'])
            ->where('status', 'scheduled')
            ->whereDate('departure_date', '>=', now()->toDateString());

        if (! $request->user()->isAdmin()) {
            $linesQuery->where('company_id', $request->user()->company_id);
            $departuresQuery->whereHas('line', fn ($q) => $q->where('company_id', $request->user()->company_id));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $linesQuery->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                    ->orWhereHas('originPlace', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('destinationPlace', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('company_id')) {
            $linesQuery->where('company_id', $request->integer('company_id'));
            $departuresQuery->whereHas('line', fn ($q) => $q->where('company_id', $request->integer('company_id')));
        }

        if ($request->filled('origin_place_id')) {
            $linesQuery->where('origin_place_id', $request->integer('origin_place_id'));
            $departuresQuery->whereHas('line', fn ($q) => $q->where('origin_place_id', $request->integer('origin_place_id')));
        }

        if ($request->filled('destination_place_id')) {
            $linesQuery->where('destination_place_id', $request->integer('destination_place_id'));
            $departuresQuery->whereHas('line', fn ($q) => $q->where('destination_place_id', $request->integer('destination_place_id')));
        }

        if ($request->filled('date')) {
            $departuresQuery->whereDate('departure_date', $request->input('date'));
        }

        $lines = $linesQuery->orderBy('label')->paginate(10)->withQueryString();
        $departures = $departuresQuery->orderBy('departure_date')->orderBy('departure_time')->paginate(10)->withQueryString();

        $companies = Company::query()
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('id', $request->user()->company_id))
            ->orderBy('legal_name')
            ->get();

        $originPlaces = Place::query()->orderBy('name')->get();
        $destinationPlaces = Place::query()->orderBy('name')->get();

        return view('search.index', compact(
            'lines', 'departures', 'companies', 'originPlaces', 'destinationPlaces'
        ));
    }
}
