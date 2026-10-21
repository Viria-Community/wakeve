<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;

class FreeRoleAccess
{
    public static function check(User $user, string $ability, mixed $subject, string $resource): bool
    {
        $action = $ability === 'viewAny' ? 'view' : $ability;
        if (! $user->company_id || ! $user->permits($resource.'.'.$action)) {
            return false;
        }
        $scope = $user->accessScope();
        $agency = $user->employee?->agency;
        if ($scope !== 'company' && (! $agency || $agency->company_id !== $user->company_id)) {
            return false;
        }
        if (! is_object($subject)) {
            return true;
        }
        $companyId = $subject instanceof Company ? $subject->id : $subject->company_id;
        if ($companyId !== $user->company_id) {
            return false;
        }
        if ($subject instanceof Employee && $action === 'update' && $subject->user && ($subject->user->isAdmin() || $subject->user->role === 'company_manager' || $subject->user_id === $user->id)) {
            return false;
        }
        if ($scope === 'company') {
            return true;
        }
        if ($subject instanceof Agency) {
            return $subject->id === $agency->id;
        }
        if ($subject instanceof Employee) {
            return $scope === 'self' ? $subject->user_id === $user->id : $subject->agency_id === $agency->id;
        }

        return false;
    }
}
