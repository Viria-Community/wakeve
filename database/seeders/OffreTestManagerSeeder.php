<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OffreTestManagerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $companyId = DB::table('companies')->where('rccm', 'RCCM-TEST-0001')->value('id');
        $agencyId = DB::table('agencies')->where('company_id', $companyId)->value('id');

        DB::table('users')->updateOrInsert(
            ['email' => 'manager@transtest.test'],
            [
                'name' => 'Gestionnaire Test',
                'password' => Hash::make('password123'),
                'role' => 'company_manager',
                'company_id' => $companyId,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $userId = DB::table('users')->where('email', 'manager@transtest.test')->value('id');

        DB::table('employees')->updateOrInsert(
            ['user_id' => $userId],
            [
                'company_id' => $companyId,
                'agency_id' => $agencyId,
                'last_name' => 'Test',
                'first_name' => 'Gestionnaire',
                'internal_role' => 'Gestionnaire',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
