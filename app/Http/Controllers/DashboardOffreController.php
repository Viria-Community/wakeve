<?php

namespace App\Http\Controllers;

use App\Models\Departure;
use App\Models\Fare;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardOffreController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard-offre.index', [
            'stats' => $this->statistiques($request->user()),
            'classes' => config('offre.classes'),
        ]);
    }

    public function donnees(Request $request): JsonResponse
    {
        return response()->json($this->statistiques($request->user()));
    }

    private function statistiques($user): array
    {
        $estAdmin = $user->isAdmin();
        $companyId = $user->company_id;

        $lignes = Line::query()->when(! $estAdmin, fn ($q) => $q->where('company_id', $companyId));
        $departs = Departure::query()->when(! $estAdmin, fn ($q) => $q->whereHas('line', fn ($q2) => $q2->where('company_id', $companyId)));

        $totalLines = (clone $lignes)->count();
        $activeLines = (clone $lignes)->where('status', 'active')->count();

        $aujourdhui = now()->toDateString();
        $totalDepartures = (clone $departs)->whereDate('departure_date', '>=', $aujourdhui)->count();
        $scheduledDepartures = (clone $departs)->where('status', 'scheduled')->whereDate('departure_date', '>=', $aujourdhui)->count();
        $cancelledDepartures = (clone $departs)->where('status', 'cancelled')->count();

        $comptes = (clone $lignes)
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) AS jour, COUNT(*) AS n')
            ->groupBy('jour')
            ->pluck('n', 'jour');

        $creations = [];
        for ($k = 29; $k >= 0; $k--) {
            $jour = now()->subDays($k);
            $creations[] = [
                'date' => $jour->format('d/m'),
                'n' => (int) ($comptes[$jour->toDateString()] ?? 0),
            ];
        }

        $recents = collect();
        (clone $lignes)->latest('created_at')->limit(5)->get()->each(function ($l) use ($recents) {
            $recents->push([
                'type' => 'Ligne',
                'titre' => $l->label.($l->status === 'inactive' ? ' · inactive' : ''),
                'quand' => $l->created_at,
            ]);
        });
        (clone $departs)->with('line')->latest('created_at')->limit(5)->get()->each(function ($d) use ($recents) {
            $recents->push([
                'type' => 'Départ',
                'titre' => ($d->line->label ?? '—').' · '.Carbon::parse($d->departure_date)->format('d/m/Y'),
                'quand' => $d->created_at,
            ]);
        });
        $activite = $recents->sortByDesc('quand')->take(6)->values()->map(fn ($a) => [
            'type' => $a['type'],
            'titre' => $a['titre'],
            'quand' => Carbon::parse($a['quand'])->diffForHumans(),
        ])->all();

        $moyennes = Fare::query()
            ->where('status', 'active')
            ->when(! $estAdmin, fn ($q) => $q->whereHas('line', fn ($q2) => $q2->where('company_id', $companyId)))
            ->selectRaw('fare_class, AVG(amount) AS moyenne')
            ->groupBy('fare_class')
            ->pluck('moyenne', 'fare_class')
            ->map(fn ($v) => round((float) $v, 2))
            ->all();

        return [
            'totalLines' => $totalLines,
            'activeLines' => $activeLines,
            'inactiveLines' => $totalLines - $activeLines,
            'totalDepartures' => $totalDepartures,
            'scheduledDepartures' => $scheduledDepartures,
            'cancelledDepartures' => $cancelledDepartures,
            'creations' => $creations,
            'activite' => $activite,
            'moyennes' => $moyennes,
        ];
    }
}
