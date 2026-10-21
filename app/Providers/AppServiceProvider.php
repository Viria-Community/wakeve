<?php

namespace App\Providers;

use App\Models\Agency;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\CustomRole;
use App\Models\Employee;
use App\Models\FleetDocument;
use App\Models\FleetUnit;
use App\Models\PlatformRole;
use App\Models\User;
use App\Observers\AuditableObserver;
use App\Services\AuditLogger;
use App\Services\FreeRoleAccess;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::defaultView('pagination.pnmg');
        CustomRole::observe(AuditableObserver::class);
        PlatformRole::observe(AuditableObserver::class);
        Gate::before(function (User $user, string $ability, array $arguments) {
            if (! $user->custom_role_id || $user->isAdmin()) {
                return null;
            }
            $subject = $arguments[0] ?? null;
            $class = is_object($subject) ? $subject::class : $subject;
            $resource = [Company::class => 'companies', Agency::class => 'agencies', Employee::class => 'employees', FleetUnit::class => 'fleet'][$class ?? ''] ?? null;
            if ($resource) {
                if ($user->hasFreeRole()) {
                    return FreeRoleAccess::check($user, $ability, $subject, $resource);
                }
                $action = $ability === 'viewAny' ? 'view' : $ability;
                if (! $user->permits($resource.'.'.$action)) {
                    return false;
                }
            }

            return null;
        });
        foreach ([Company::class, Agency::class, Employee::class, FleetUnit::class, CompanyDocument::class, FleetDocument::class, User::class] as $model) {
            $model::observe(AuditableObserver::class);
        }
        Event::listen(Login::class, fn (Login $event) => AuditLogger::record('connexion', 'Connexion à la plateforme', null, [], $event->user->id));
        Event::listen(Logout::class, fn (Logout $event) => $event->user && AuditLogger::record('deconnexion', 'Déconnexion de la plateforme', null, [], $event->user->id));
    }
}
