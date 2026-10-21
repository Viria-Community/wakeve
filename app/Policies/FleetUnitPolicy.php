<?php

namespace App\Policies;

use App\Models\FleetUnit;
use App\Models\User;

class FleetUnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->company_id !== null;
    }

    public function view(User $user, FleetUnit $fleetUnit): bool
    {
        return $user->isAdmin() || $user->company_id === $fleetUnit->company_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role === 'company_manager';
    }

    public function update(User $user, FleetUnit $fleetUnit): bool
    {
        return $user->isAdmin()
            || ($user->role === 'company_manager' && $user->company_id === $fleetUnit->company_id);
    }

    public function delete(User $user, FleetUnit $fleetUnit): bool
    {
        return false;
    }
}
