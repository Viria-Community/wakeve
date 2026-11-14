<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OffreDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $companyId = DB::table('companies')->where('rccm', 'RCCM-TEST-0001')->value('id');
        $modeId = DB::table('company_transport_mode')->where('company_id', $companyId)->value('transport_mode_id');
        $agencyId = DB::table('agencies')->where('company_id', $companyId)->value('id');
        $busId = DB::table('fleet_units')->where('registration_number', 'TEST-001-GA')->value('id');

        $place = fn (string $name) => DB::table('places')->where('name', $name)->value('id');
        $libreville = $place('Libreville');
        $owendo = $place('Owendo');
        $ntoum = $place('Ntoum');

        $lines = [
            ['Libreville - Owendo', $libreville, $owendo, [$libreville, $owendo]],
            ['Libreville - Ntoum', $libreville, $ntoum, [$libreville, $ntoum]],
            ['Owendo - Ntoum (via Libreville)', $owendo, $ntoum, [$owendo, $libreville, $ntoum]],
        ];

        foreach ($lines as [$label, $originId, $destinationId, $stops]) {
            DB::table('lines')->updateOrInsert(
                ['label' => $label, 'company_id' => $companyId],
                ['transport_mode_id' => $modeId, 'origin_place_id' => $originId, 'destination_place_id' => $destinationId, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );

            $lineId = DB::table('lines')->where('label', $label)->where('company_id', $companyId)->value('id');

            DB::table('line_places')->where('line_id', $lineId)->delete();
            foreach ($stops as $order => $placeId) {
                DB::table('line_places')->insert(['line_id' => $lineId, 'place_id' => $placeId, 'stop_order' => $order]);
            }

            DB::table('fares')->updateOrInsert(
                ['line_id' => $lineId, 'fare_class' => 'standard'],
                ['amount' => 1500, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );

            foreach ([1, 2, 3] as $day) {
                DB::table('departures')->updateOrInsert(
                    ['line_id' => $lineId, 'departure_date' => now()->addDays($day)->toDateString(), 'departure_time' => '08:00:00'],
                    ['fleet_unit_id' => $busId, 'agency_id' => $agencyId, 'status' => 'scheduled', 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
