<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permitsPlatform('companies.view') || $user->company_id !== null;
    }

    public function view(User $user, Company $company): bool
    {
        return $this->canAccessCompany($user, $company);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->isAdmin()
            || ($user->role === 'company_manager' && $this->canAccessCompany($user, $company));
    }

    public function delete(User $user, Company $company): bool
    {
        return false;
    }

    public function changeStatus(User $user, Company $company): bool
    {
        return $user->isAdmin() || $user->permitsPlatform('companies.validate') || $user->permitsPlatform('companies.suspend');
    }

    private function canAccessCompany(User $user, Company $company): bool
    {
        return $user->permitsPlatform('companies.view') || $user->company_id === $company->getKey();
    }
}
