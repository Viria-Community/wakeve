<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\User;
use App\Notifications\NewCompanyApplicationNotification;
use Illuminate\Support\Facades\Notification;

class CompanyObserver
{
    public function created(Company $company): void
    {
        if ($company->status?->value !== 'pending') {
            return;
        }

        $destinataires = User::query()
            ->whereIn('role', ['admin', 'platform_staff'])
            ->get()
            ->filter(fn (User $user) => $user->isAdmin() || $user->permitsPlatform('companies.validate'));

        if ($destinataires->isNotEmpty()) {
            Notification::send($destinataires, new NewCompanyApplicationNotification($company));
        }
    }
}
