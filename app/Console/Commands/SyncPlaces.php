<?php

namespace App\Console\Commands;

use App\Models\Place;
use Illuminate\Console\Command;

class SyncPlaces extends Command
{
    protected $signature = 'offre:sync-places';

    protected $description = 'Aligne les lieux de la base sur la liste des quartiers par ville';

    public function handle(): int
    {
        $provinces = [];
        foreach (config('geography') as $code => $zone) {
            foreach ($zone['cities'] as $ville) {
                $provinces[$ville] = $code;
            }
        }

        $valides = [];
        $crees = 0;

        foreach (config('quartiers') as $ville => $quartiers) {
            foreach ($quartiers as $quartier) {
                $valides[] = $ville.'|'.$quartier;
                $place = Place::updateOrCreate(
                    ['name' => $quartier, 'city' => $ville],
                    ['place_type' => 'quartier', 'province' => $provinces[$ville] ?? null]
                );
                if ($place->wasRecentlyCreated) {
                    $crees++;
                }
                if (! isset($provinces[$ville])) {
                    $this->warn("Ville absente de config/geography.php : {$ville}");
                }
            }
        }

        $supprimes = 0;
        foreach (Place::all() as $place) {
            if (in_array($place->city.'|'.$place->name, $valides, true)) {
                continue;
            }
            $utilise = $place->originLines()->exists() || $place->destinationLines()->exists() || $place->lines()->exists();
            if ($utilise) {
                $this->warn("Lieu utilisé par une ligne, conservé : {$place->city} - {$place->name}");
                continue;
            }
            $place->delete();
            $supprimes++;
        }

        $this->info("Quartiers créés : {$crees} | lieux obsolètes supprimés : {$supprimes}");

        return self::SUCCESS;
    }
}
