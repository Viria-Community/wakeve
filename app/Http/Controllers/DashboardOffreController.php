<?php

namespace App\Http\Controllers;

use App\Models\Departure;
use App\Models\Line;
use Illuminate\View\View;

class DashboardOffreController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;

        $totalLines = Line::query()
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('company_id', $companyId))
            ->count();

        $activeLines = Line::query()
            ->where('status', 'active')
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('company_id', $companyId))
            ->count();

        $totalDepartures = Departure::query()
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->whereHas('line', fn ($q2) => $q2->where('company_id', $companyId)))
            ->count();

        $scheduledDepartures = Departure::query()
            ->where('status', 'scheduled')
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->whereHas('line', fn ($q2) => $q2->where('company_id', $companyId)))
            ->count();

        return view('dashboard-offre.index', compact(
            'totalLines', 'activeLines', 'totalDepartures', 'scheduledDepartures'
        ));
    }
}
