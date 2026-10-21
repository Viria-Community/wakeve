<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Donnée de référence PROVISOIRE, proposée par l'équipe technique.
 *
 * IMPORTANT :
 * Le CDC (RF-007) exige qu'une compagnie choisisse son mode de transport
 * à partir d'une liste de référence, sans fournir de liste officielle.
 * La décision métier du 22 septembre 2026 limite désormais chaque
 * compagnie à un seul mode. Les 5 valeurs ci-dessous (road, river,
 * maritime, rail, air) restent une proposition technique.
 *
 * Statut : Proposition technique — validation métier en attente.
 */
class TransportModeSeeder extends Seeder
{
    public function run(): void
    {
        $modes = ['road', 'river', 'maritime', 'rail', 'air'];

        foreach ($modes as $label) {
            DB::table('transport_modes')->updateOrInsert(
                ['label' => $label],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
