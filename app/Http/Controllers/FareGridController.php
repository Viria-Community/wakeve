<?php

namespace App\Http\Controllers;

use App\Models\Fare;
use App\Models\Line;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FareGridController extends Controller
{
    public function show(Request $request, Line $line): View
    {
        $this->autoriser($request, $line);

        return view('fares.grille', [
            'line' => $line,
            'classes' => config('offre.classes'),
            'grille' => config('offre.grille'),
            'tarifs' => $line->fares()->get()->keyBy('fare_class'),
        ]);
    }

    public function save(Request $request, Line $line): RedirectResponse
    {
        $this->autoriser($request, $line);

        $data = $request->validate([
            'montants' => ['required', 'array'],
            'montants.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach (array_keys(config('offre.classes')) as $classe) {
            $montant = $data['montants'][$classe] ?? null;

            if ($montant === null || $montant === '') {
                continue;
            }

            $fare = Fare::where('line_id', $line->id)->where('fare_class', $classe)->first() ?? new Fare();
            $fare->line_id = $line->id;
            $fare->fare_class = $classe;
            $fare->amount = $montant;
            if (! $fare->exists) {
                $fare->status = 'active';
            }
            $fare->save();
        }

        return redirect()->route('lines.show', $line)
            ->with('status', 'Les tarifs de la ligne ont été enregistrés.');
    }

    private function autoriser(Request $request, Line $line): void
    {
        $user = $request->user();
        $autorise = $user->role === 'company_manager' && $user->company_id === $line->company_id;
        abort_unless($autorise, 403);
    }
}
