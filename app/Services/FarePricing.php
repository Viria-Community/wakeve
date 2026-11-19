<?php

namespace App\Services;

use App\Models\AgeReduction;
use App\Models\Company;
use App\Models\Fare;
use App\Models\Line;
use App\Models\Place;

class FarePricing
{
    public function prixTrajetPartiel(float $tarifComplet): float
    {
        return round(
            $tarifComplet * config('offre.pourcentage_trajet_partiel') / 100,
            2
        );
    }

    public function reductionFor(Company $company, ?int $age): int
    {
        if ($age === null) {
            return 0;
        }

        return (int) (
            AgeReduction::query()
                ->where('company_id', $company->id)
                ->where('status', 'active')
                ->where('min_age', '<=', $age)
                ->where(fn ($query) => $query
                    ->whereNull('max_age')
                    ->orWhere('max_age', '>=', $age))
                ->max('percent') ?? 0
        );
    }

    public function prixFinal(Company $company, float $montant, ?int $age): float
    {
        $pourcentage = $this->reductionFor($company, $age);

        return round($montant * (100 - $pourcentage) / 100, 2);
    }

    /**
     * Ancien tarif complet de la ligne.
     * Les tarifs par tronçon ne sont pas pris en compte ici.
     */
    public function tarifComplet(Line $line, string $classe): ?float
    {
        $fare = $line->fares()
            ->where('fare_class', $classe)
            ->where('status', 'active')
            ->whereNull('origin_place_id')
            ->whereNull('destination_place_id')
            ->first();

        return $fare ? (float) $fare->amount : null;
    }

    /**
     * Recherche d'abord un tarif personnalisé pour le trajet.
     * À défaut, conserve l'ancien calcul proportionnel.
     */
    public function prixTrajet(
        Line $line,
        Place $origine,
        Place $destination,
        string $classe,
        ?int $age
    ): ?float {
        $ordre = $line->places()
            ->orderByPivot('stop_order')
            ->pluck('places.id')
            ->values()
            ->all();

        $i = array_search($origine->id, $ordre, true);
        $j = array_search($destination->id, $ordre, true);

        if ($i === false || $j === false || $i >= $j) {
            return null;
        }

        // 1. Tarif personnalisé pour cette origine et cette destination.
        $tarifPersonnalise = Fare::query()
            ->where('line_id', $line->id)
            ->where('origin_place_id', $origine->id)
            ->where('destination_place_id', $destination->id)
            ->where('fare_class', $classe)
            ->where('status', 'active')
            ->first();

        if ($tarifPersonnalise) {
            return $this->prixFinal(
                $line->company,
                (float) $tarifPersonnalise->amount,
                $age
            );
        }

        // 2. Compatibilité avec l'ancien tarif complet.
        $complet = $this->tarifComplet($line, $classe);

        if ($complet === null) {
            return null;
        }

        $dernierIndex = count($ordre) - 1;

        $base = ($i === 0 && $j === $dernierIndex)
            ? $complet
            : $this->prixTrajetPartiel($complet);

        return $this->prixFinal($line->company, $base, $age);
    }
}
