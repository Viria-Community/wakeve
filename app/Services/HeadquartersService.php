<?php

namespace App\Services;

use App\Models\Company;

class HeadquartersService
{
    public function create(Company $company): void
    {
        $company->agencies()->firstOrCreate(['headquarters_for' => $company->id], [
            'name' => 'Siège', 'address' => $company->address, 'city' => $company->city,
            'province' => $company->province, 'phone' => $company->phone, 'status' => 'active',
        ]);
    }
}
