<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\TransportMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OffreTestSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('companies')->updateOrInsert(
            ['rccm' => 'RCCM-TEST-0001'],
            [
                'legal_name' => 'Transport Test SARL',
                'trade_name' => 'TransTest',
                'nif' => 'NIF-TEST-0001',
                'email' => 'contact@transtest.test',
                'phone' => '+24100000000',
                'address' => 'Boulevard de test',
                'city' => 'Libreville',
                'status' => 'validated',
                'validated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $companyId = DB::table('companies')->where('rccm', 'RCCM-TEST-0001')->value('id');

        $mode = TransportMode::orderBy('id')->firstOrFail();
        Company::findOrFail($companyId)->transportModes()->syncWithoutDetaching([$mode->id]);

        DB::table('agencies')->updateOrInsert(
            ['company_id' => $companyId, 'name' => 'Agence Centrale'],
            [
                'address' => 'Centre-ville',
                'city' => 'Libreville',
                'phone' => '+24100000001',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('fleet_units')->updateOrInsert(
            ['registration_number' => 'TEST-001-GA'],
            [
                'company_id' => $companyId,
                'type' => 'Bus',
                'capacity' => 50,
                'availability' => 'available',
                'status' => 'active',
                'transport_mode_id' => $mode->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('fleet_units')->updateOrInsert(
            ['registration_number' => 'TEST-002-GA'],
            [
                'company_id' => $companyId,
                'type' => 'Minibus',
                'capacity' => 20,
                'availability' => 'available',
                'status' => 'active',
                'transport_mode_id' => $mode->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        foreach (['Libreville', 'Owendo', 'Ntoum'] as $city) {
            DB::table('places')->updateOrInsert(
                ['name' => $city],
                [
                    'place_type' => 'ville',
                    'city' => $city,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
