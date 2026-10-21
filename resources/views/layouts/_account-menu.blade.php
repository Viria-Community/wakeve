@php
    $accountUser = auth()->user();
    $accountRole = $accountUser->customRole?->name
        ?? match ($accountUser->role) {
            'admin' => 'Administrateur de la plateforme',
            'platform_staff' => $accountUser->platformRole?->name ?? 'Personnel PNMG',
            'company_manager' => 'Gestionnaire de compagnie',
            'agency_manager' => 'Responsable d’agence',
            default => 'Agent de compagnie',
        };
    $accountInitials = collect(preg_split('/\s+/u', trim($accountUser->name)) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $accountPhone = $accountUser->company?->phone;
@endphp
<div class="dropdown account-menu">
    <button class="account-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Ouvrir le menu du profil">
        <span class="account-avatar" aria-hidden="true">{{ $accountInitials ?: 'PN' }}</span>
        <span class="account-trigger-copy"><strong>{{ $accountUser->name }}</strong><small>{{ $accountRole }}</small></span>
        <x-lucide-chevron-down class="account-chevron" aria-hidden="true"/>
    </button>
    <div class="dropdown-menu dropdown-menu-end account-dropdown">
        <div class="account-dropdown-head">
            <span class="account-avatar large" aria-hidden="true">{{ $accountInitials ?: 'PN' }}</span>
            <div><strong>{{ $accountUser->name }}</strong><small>{{ $accountRole }}</small></div>
        </div>
        <dl class="account-details">
            <div><dt>Adresse email</dt><dd>{{ $accountUser->email }}</dd></div>
            @if($accountPhone)<div><dt>Téléphone</dt><dd>{{ $accountPhone }}</dd></div>@endif
            @if($accountUser->company)<div><dt>Compagnie</dt><dd>{{ $accountUser->company->legal_name }}</dd></div>@endif
        </dl>
        <div class="account-actions">
            <a class="account-profile-link" href="{{ route('profile.edit') }}"><x-lucide-user-round aria-hidden="true"/>Voir mon profil</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="account-logout" type="submit"><x-lucide-log-out aria-hidden="true"/>Se déconnecter</button></form>
        </div>
    </div>
</div>
