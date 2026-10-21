<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Donnée de référence PROVISOIRE, proposée par l'équipe technique.
 *
 * IMPORTANT :
 * Le CDC (RF-008, RF-016) exige que chaque document possède un type et
 * une date de validité, mais NE FOURNIT AUCUNE nomenclature officielle
 * des types de documents. La liste ci-dessous n'est PAS une exigence du
 * cahier des charges — elle doit être validée (ou corrigée) par
 * l'encadreur métier avant mise en production.
 *
 * Modifier cette liste ne nécessite aucun changement de schéma :
 * `document_types` est conçu pour rester générique.
 *
 * Statut : 🟡 Proposition technique — ⏳ validation métier en attente.
 */
class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['label' => 'Autorisation d\'exploitation', 'applicable_to' => 'company', 'is_required' => true],
            ['label' => 'Assurance', 'applicable_to' => 'both', 'is_required' => false],
            ['label' => 'Attestation fiscale', 'applicable_to' => 'company', 'is_required' => false],
            ['label' => 'Carte grise', 'applicable_to' => 'fleet_unit', 'is_required' => false],
            ['label' => 'Visite technique', 'applicable_to' => 'fleet_unit', 'is_required' => false],
            ['label' => 'Autorisation de circulation', 'applicable_to' => 'fleet_unit', 'is_required' => false],
        ];

        foreach ($types as $type) {
            DB::table('document_types')->updateOrInsert(
                ['label' => $type['label']],
                [
                    'applicable_to' => $type['applicable_to'],
                    'is_required' => $type['is_required'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
