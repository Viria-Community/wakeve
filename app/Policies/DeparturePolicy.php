<?php

namespace App\Policies;

use App\Models\Departure;
use App\Models\User;

class DeparturePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['company_manager', 'agency_manager'], true);
    }

    public function view(User $user, Departure $departure): bool
    {
        return $user->isAdmin() || $user->company_id === $departure->line?->company_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['company_manager', 'agency_manager'], true) && $user->company_id !== null;
    }

    public function update(User $user, Departure $departure): bool
    {
        return in_array($user->role, ['company_manager', 'agency_manager'], true)
            && $user->company_id === $departure->line?->company_id;
    }
}
