<aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="pnmgSidebar" aria-labelledby="pnmgSidebarLabel">
    <div class="sidebar-mobile-head">
        <strong id="pnmgSidebarLabel">Navigation PNMG</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#pnmgSidebar" aria-label="Fermer"></button>
    </div>
    <a class="brand" href="{{ route('home') }}" aria-label="Ouvrir le site public">
        <img src="{{ asset('images/pnmg/logo-horizontal-inverse.png') }}" alt="PNMG — Plateforme Nationale de Mobilité du Gabon" width="188" height="40">
    </a>
    <nav class="side-nav" aria-label="Navigation principale">
        <a @class(['active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}"><x-lucide-layout-dashboard class="nav-icon" aria-hidden="true"/>Tableau de bord</a>

        @if(auth()->user()->isPlatformMember() || in_array(auth()->user()->company?->status, [\App\Enums\CompanyStatus::Pending, \App\Enums\CompanyStatus::Validated, \App\Enums\CompanyStatus::Active], true))
            <section class="nav-group" aria-labelledby="nav-management-title">
                <h2 class="nav-group-title" id="nav-management-title">Gestion</h2>
                <div class="nav-group-links">
                    @can('viewAny', \App\Models\Company::class)
                        <a @class(['active' => request()->routeIs('companies.*', 'company-documents.*')]) href="{{ auth()->user()->isPlatformMember() ? route('companies.index') : route('companies.show', auth()->user()->company_id) }}"><x-lucide-building-2 class="nav-icon" aria-hidden="true"/>Compagnies</a>
                    @endcan
                    @can('viewAny', \App\Models\Agency::class)
                        <a @class(['active' => request()->routeIs('agencies.*')]) href="{{ route('agencies.index') }}"><x-lucide-map-pinned class="nav-icon" aria-hidden="true"/>Agences</a>
                    @endcan
                    @can('viewAny', \App\Models\Employee::class)
                        <a @class(['active' => request()->routeIs('employees.*')]) href="{{ route('employees.index') }}"><x-lucide-users-round class="nav-icon" aria-hidden="true"/>Employés</a>
                    @endcan
                    @can('viewAny', \App\Models\FleetUnit::class)
                        <a @class(['active' => request()->routeIs('fleet-units.*', 'fleet-documents.*')]) href="{{ route('fleet-units.index') }}"><x-lucide-bus-front class="nav-icon" aria-hidden="true"/>Flotte</a>
                    @endcan
                    @can('viewAny', \App\Models\Line::class)
<details class="offre-menu" @if(request()->routeIs('dashboard-offre.*', 'lines.*', 'departures.*', 'search.*')) open @endif>
    <summary><x-lucide-route class="nav-icon" aria-hidden="true"/>Offre de transport</summary>
    <a @class(['active' => request()->routeIs('dashboard-offre.*')]) href="{{ route('dashboard-offre.index') }}">Tableau de bord</a>
    <a @class(['active' => request()->routeIs('lines.*')]) href="{{ route('lines.index') }}">Lignes</a>
    <a @class(['active' => request()->routeIs('departures.*')]) href="{{ route('departures.index') }}">Départs</a>
    <a @class(['active' => request()->routeIs('search.*')]) href="{{ route('search.index') }}">Recherche</a>
            <a href="{{ route('reductions.index') }}">Réductions</a>
</details>
<style>.offre-menu summary{list-style:none;cursor:pointer;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px}.offre-menu summary::-webkit-details-marker{display:none}.offre-menu a{display:block;padding:8px 12px 8px 44px;border-radius:8px}</style>
@endcan
                </div>
            </section>

            @if(auth()->user()->permits('companies.view') || auth()->user()->permits('fleet.view') || auth()->user()->permitsPlatform('documents.view'))
                <section class="nav-group" aria-labelledby="nav-documents-title">
                    <h2 class="nav-group-title" id="nav-documents-title">Documents</h2>
                    <div class="nav-group-links">
                        <a @class(['active' => request()->routeIs('documents.*') && !request()->filled('status') && !request()->filled('review')]) href="{{ route('documents.index') }}"><x-lucide-files class="nav-icon" aria-hidden="true"/>Tous les documents</a>
                        <a @class(['active' => request()->routeIs('documents.*') && request('status') === 'to_renew']) href="{{ route('documents.index', ['status' => 'to_renew']) }}"><x-lucide-calendar-clock class="nav-icon" aria-hidden="true"/>À renouveler</a>
                        <a @class(['active' => request()->routeIs('documents.*') && request('review') === 'pending']) href="{{ route('documents.index', ['review' => 'pending']) }}"><x-lucide-clipboard-check class="nav-icon" aria-hidden="true"/>À contrôler</a>
                        <a @class(['active' => request()->routeIs('documents.*') && request('review') === 'approved']) href="{{ route('documents.index', ['review' => 'approved']) }}"><x-lucide-badge-check class="nav-icon" aria-hidden="true"/>Validés</a>
                        <a @class(['active' => request()->routeIs('documents.*') && request('review') === 'rejected']) href="{{ route('documents.index', ['review' => 'rejected']) }}"><x-lucide-badge-x class="nav-icon" aria-hidden="true"/>Refusés</a>
                    </div>
                </section>
            @endif
        @endif

        @if(auth()->user()->isPlatformMember() || auth()->user()->canManageRoles() || auth()->user()->permitsPlatform('audit.view'))
            <section class="nav-group" aria-labelledby="nav-administration-title">
                <h2 class="nav-group-title" id="nav-administration-title">Administration</h2>
                <div class="nav-group-links">
                    @if(auth()->user()->isAdmin())
                        <a @class(['active' => request()->routeIs('platform-staff.*')]) href="{{ route('platform-staff.index') }}"><x-lucide-user-cog class="nav-icon" aria-hidden="true"/>Personnel PNMG</a>
                        <a @class(['active' => request()->routeIs('platform-roles.*')]) href="{{ route('platform-roles.index') }}"><x-lucide-shield-check class="nav-icon" aria-hidden="true"/>Rôles PNMG</a>
                    @endif
                    @if(auth()->user()->canManageRoles())
                        <a @class(['active' => request()->routeIs('roles.*')]) href="{{ route('roles.index') }}"><x-lucide-shield class="nav-icon" aria-hidden="true"/>Rôles compagnie</a>
                    @endif
                    @if(auth()->user()->permitsPlatform('audit.view'))
                        <a @class(['active' => request()->routeIs('audit-logs.*')]) href="{{ route('audit-logs.index') }}"><x-lucide-scroll-text class="nav-icon" aria-hidden="true"/>Journal d’activité</a>
                    @endif
                </div>
            </section>
        @endif

        @if(auth()->user()->role === 'company_manager' && auth()->user()->company?->status !== \App\Enums\CompanyStatus::Active)
            <a @class(['active' => request()->routeIs('application.status')]) href="{{ route('application.status') }}"><x-lucide-clock-3 class="nav-icon" aria-hidden="true"/>Suivi de la demande</a>
        @endif
        @if((auth()->user()->role === 'agency_manager' || auth()->user()->hasFreeRole()) && auth()->user()->permits('activity.view'))
            <a @class(['active' => request()->routeIs('agency.activity')]) href="{{ route('agency.activity') }}"><x-lucide-history class="nav-icon" aria-hidden="true"/>Mon journal</a>
        @endif
    </nav>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="sidebar-logout" type="submit">
                <x-lucide-log-out class="nav-icon" aria-hidden="true"/>
                <span>Se déconnecter</span>
            </button>
        </form>
    </div>
</aside>
