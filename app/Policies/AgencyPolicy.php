<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;

class AgencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->company_id !== null;
    }

    public function view(User $user, Agency $agency): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->company_id !== $agency->company_id) {
            return false;
        }

        return in_array($user->role, ['company_manager'], true)
            || $user->employee?->agency_id === $agency->getKey();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role === 'company_manager';
    }

    public function update(User $user, Agency $agency): bool
    {
        return $user->isAdmin()
            || ($user->role === 'company_manager' && $user->company_id === $agency->company_id)
            || ($user->role === 'agency_manager' && $user->employee?->agency_id === $agency->getKey());
    }

    public function delete(User $user, Agency $agency): bool
    {
        return false;
    }
}
