<?php

namespace App\Policies;

use App\Models\Line;
use App\Models\User;

class LinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role === 'company_manager';
    }

    public function view(User $user, Line $line): bool
    {
        return $user->isAdmin()
            || ($user->role === 'company_manager' && $user->company_id === $line->company_id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'company_manager' && $user->company_id !== null;
    }

    public function update(User $user, Line $line): bool
    {
        return $user->role === 'company_manager' && $user->company_id === $line->company_id;
    }
}
