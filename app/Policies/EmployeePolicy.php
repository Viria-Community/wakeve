<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function manageAccount(User $user, Employee $employee): bool
    {
        if ($employee->user && ($employee->user->isAdmin() || $employee->user_id === $user->id || $employee->user->company_id !== $employee->company_id)) {
            return false;
        }

        return $user->isAdmin() || ($user->canManageRoles() && $user->company_id === $employee->company_id && $employee->user?->role !== 'company_manager');
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->company_id !== null;
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->company_id !== $employee->company_id) {
            return false;
        }

        return $user->role === 'company_manager'
            || ($user->role === 'agency_manager' && $user->employee?->agency_id === $employee->agency_id)
            || $user->id === $employee->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['company_manager', 'agency_manager'], true);
    }

    public function update(User $user, Employee $employee): bool
    {
        if (! $user->isAdmin() && $employee->user && ($employee->user->isAdmin() || ($user->role === 'agency_manager' && $employee->user->role !== 'agent'))) {
            return false;
        }

        return $user->isAdmin()
            || ($user->role === 'company_manager' && $user->company_id === $employee->company_id)
            || ($user->role === 'agency_manager' && $user->employee?->agency_id === $employee->agency_id);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return false;
    }
}
