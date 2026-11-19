<?php

namespace App\Http\Controllers;

use App\Models\Fare;
use App\Models\Line;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FareGridController extends Controller
{
    public function show(Request $request, Line $line): View
    {
        $this->autoriser($request, $line);

        $arrets = $line->places()
            ->orderByPivot('stop_order')
            ->get();

        $tarifs = $line->fares()
            ->whereNotNull('origin_place_id')
            ->whereNotNull('destination_place_id')
            ->get()
            ->keyBy(fn ($fare) => implode('-', [
                $fare->origin_place_id,
                $fare->destination_place_id,
                $fare->fare_class,
            ]));

        $anciensTarifs = $line->fares()
            ->whereNull('origin_place_id')
            ->whereNull('destination_place_id')
            ->get()
            ->keyBy('fare_class');

        return view('fares.grille', [
            'line' => $line,
            'classes' => config('offre.classes'),
            'arrets' => $arrets,
            'tarifs' => $tarifs,
            'anciensTarifs' => $anciensTarifs,
        ]);
    }

    public function save(Request $request, Line $line): RedirectResponse
    {
        $this->autoriser($request, $line);

        $data = $request->validate([
            'montants' => ['required', 'array'],
            'montants.*' => ['array'],
            'montants.*.*' => ['array'],
            'montants.*.*.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $arretsIds = $line->places()
            ->pluck('places.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $classes = array_keys(config('offre.classes'));

        DB::transaction(function () use ($data, $line, $arretsIds, $classes) {
            foreach ($data['montants'] as $origineId => $destinations) {
                $origineId = (int) $origineId;

                if (! in_array($origineId, $arretsIds, true)) {
                    continue;
                }

                foreach ($destinations as $destinationId => $montants) {
                    $destinationId = (int) $destinationId;

                    if (
                        ! in_array($destinationId, $arretsIds, true)
                        || $origineId === $destinationId
                    ) {
                        continue;
                    }

                    foreach ($montants as $classe => $montant) {
                        if (
                            ! in_array($classe, $classes, true)
                            || $montant === null
                            || $montant === ''
                        ) {
                            continue;
                        }

                        Fare::updateOrCreate(
                            [
                                'line_id' => $line->id,
                                'origin_place_id' => $origineId,
                                'destination_place_id' => $destinationId,
                                'fare_class' => $classe,
                            ],
                            [
                                'amount' => $montant,
                                'status' => 'active',
                            ]
                        );
                    }
                }
            }
        });

        return redirect()->route('fares.grille', $line)
            ->with('status', 'Les tarifs des trajets ont été enregistrés.');
    }

    private function autoriser(Request $request, Line $line): void
    {
        $user = $request->user();

        $autorise = $user->role === 'company_manager'
            && $user->company_id === $line->company_id;

        abort_unless($autorise, 403);
    }
}
