<?php

namespace App\Policies;

use App\Models\Fare;
use App\Models\User;

class FarePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role === 'company_manager';
    }

    public function view(User $user, Fare $fare): bool
    {
        return $user->isAdmin() || $user->company_id === $fare->line?->company_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'company_manager' && $user->company_id !== null;
    }

    public function update(User $user, Fare $fare): bool
    {
        return $user->role === 'company_manager'
            && $user->company_id === $fare->line?->company_id;
    }
}
