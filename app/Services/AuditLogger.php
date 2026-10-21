<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Employee;
use App\Models\FleetDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(string $action, string $description, ?Model $subject = null, array $changedFields = [], ?int $userId = null): void
    {
        $user = Auth::user();

        AuditLog::create([
            'agency_id' => self::agencyId($subject, $user),
            'user_id' => $userId ?? $user?->id,
            'company_id' => self::companyId($subject, $user?->company_id),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'subject_label' => $subject ? self::label($subject) : null,
            'description' => $description,
            'changed_fields' => $changedFields ?: null,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
            'user_agent' => app()->runningInConsole() ? null : request()->userAgent(),
        ]);
    }

    private static function agencyId(?Model $subject, ?User $actor): ?int
    {
        if ($subject instanceof Agency) {
            return $subject->id;
        }
        if ($subject instanceof Employee) {
            return $subject->agency_id;
        }
        if ($subject instanceof User) {
            return $subject->employee()->value('agency_id');
        }

        return $subject === null ? $actor?->employee()->value('agency_id') : null;
    }

    private static function label(Model $subject): string
    {
        return (string) ($subject->legal_name ?? $subject->name ?? $subject->registration_number ?? $subject->email ?? class_basename($subject).' #'.$subject->getKey());
    }

    private static function companyId(?Model $subject, ?int $userCompanyId): ?int
    {
        if ($subject instanceof Company) {
            return $subject->getKey();
        }
        if ($subject?->getAttribute('company_id')) {
            return (int) $subject->getAttribute('company_id');
        }
        if ($subject instanceof FleetDocument) {
            return $subject->fleetUnit()->value('company_id');
        }

        return $userCompanyId;
    }
}
