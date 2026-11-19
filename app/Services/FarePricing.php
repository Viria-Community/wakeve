<?php

namespace App\Services;

use App\Models\AgeReduction;
use App\Models\Company;
use App\Models\Line;
use App\Models\Place;

class FarePricing
{
    public function prixTrajetPartiel(float $tarifComplet): float
    {
        return round($tarifComplet * config('offre.pourcentage_trajet_partiel') / 100, 2);
    }

    public function reductionFor(Company $company, ?int $age): int
    {
        if ($age === null) {
            return 0;
        }

        return (int) AgeReduction::query()
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->where('min_age', '<=', $age)
            ->where(fn ($q) => $q->whereNull('max_age')->orWhere('max_age', '>=', $age))
            ->max('percent') ?? 0;
    }

    public function prixFinal(Company $company, float $montant, ?int $age): float
    {
        $pourcent = $this->reductionFor($company, $age);

        return round($montant * (100 - $pourcent) / 100, 2);
    }

    public function tarifComplet(Line $line, string $classe): ?float
    {
        $fare = $line->fares->first(fn ($f) => $f->fare_class === $classe && $f->status === 'active');

        return $fare ? (float) $fare->amount : null;
    }

    public function prixTrajet(Line $line, Place $origine, Place $destination, string $classe, ?int $age): ?float
    {
        $complet = $this->tarifComplet($line, $classe);
        if ($complet === null) {
            return null;
        }

        $ordre = $line->places->pluck('id')->values()->all();
        $i = array_search($origine->id, $ordre, true);
        $j = array_search($destination->id, $ordre, true);

        if ($i === false || $j === false || $i >= $j) {
            return null;
        }

        $base = ($i === 0 && $j === count($ordre) - 1) ? $complet : $this->prixTrajetPartiel($complet);

        return $this->prixFinal($line->company, $base, $age);
    }
}
