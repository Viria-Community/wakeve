<?php

namespace App\Http\Controllers;

use App\Models\Departure;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardOffreController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $isScoped = ! $user->isAdmin();

        $lines = Line::query()
            ->when($isScoped, fn ($query) => $query->where('company_id', $companyId));

        $departures = Departure::query()
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->when($isScoped, fn ($query) => $query->whereHas(
                'line',
                fn ($lineQuery) => $lineQuery->where('company_id', $companyId)
            ));

        $totalLines = (clone $lines)->count();
        $activeLines = (clone $lines)->where('status', 'active')->count();
        $totalDepartures = (clone $departures)->count();
        $scheduledDepartures = (clone $departures)
            ->where('status', 'scheduled')
            ->count();

        return view('dashboard-offre.index', [
            'totalLines' => $totalLines,
            'activeLines' => $activeLines,
            'totalDepartures' => $totalDepartures,
            'scheduledDepartures' => $scheduledDepartures,
        ]);
    }
}
