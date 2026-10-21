@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Rôles et permissions - PNMG')
@section('content')
@include('employees._styles')
@include('roles._styles')
@php
    $permissionGroups = \App\Services\PermissionCatalog::groups();
    $permissionLabels = \App\Services\PermissionCatalog::labels();
    $scopeLabels = \App\Services\PermissionCatalog::scopes();
@endphp
<header class="role-page-head">
    <div>
        <h1>Rôles des compagnies</h1>
        <p>Utilisez un modèle du cahier des charges ou créez un rôle adapté à une mission. Le détail des permissions reste replié pour faciliter la lecture.</p>
    </div>
    <a class="btn" href="{{ route('roles.create') }}">Nouveau rôle</a>
</header>

<section class="role-section" aria-labelledby="preset-role-title">
    <div class="role-section-head">
        <div><h2 id="preset-role-title">Modèles du cahier des charges</h2><p>Ces modèles protégés servent de point de départ et ne sont pas modifiés directement.</p></div>
        <span class="role-section-count">{{ $presets->count() }} modèle(s)</span>
    </div>
    <div class="role-grid">
        @foreach($presets as $preset)
            <article class="role-card role-preset">
                <div class="role-card-head">
                    <span class="role-card-icon" aria-hidden="true"><x-lucide-shield-check/></span>
                    <div class="role-card-title"><h3>{{ $preset->name }}</h3><p>{{ $preset->description }}</p></div>
                </div>
                <div class="role-badges">
                    <span class="role-badge">Modèle protégé</span>
                    <span class="role-badge scope">{{ $preset->scope === 'national' ? 'Administration nationale' : ($scopeLabels[$preset->scope] ?? $preset->scope) }}</span>
                </div>
                @if($preset->code === 'admin')
                    <div class="role-system-note"><strong>Accès système complet</strong><span>Ce rôle national est géré par la plateforme et ne se configure pas depuis les rôles de compagnie.</span></div>
                @else
                    @include('roles._permission-summary', [
                        'permissions' => $preset->permissions,
                        'groups' => $permissionGroups,
                        'labels' => $permissionLabels,
                    ])
                @endif
                <footer class="role-card-footer">
                    @if($preset->code !== 'admin')
                        <a class="btn secondary" href="{{ route('roles.create', ['preset' => $preset->id]) }}">Utiliser ce modèle</a>
                    @else
                        <span class="muted">Réservé à l’administration</span>
                    @endif
                </footer>
            </article>
        @endforeach
    </div>
</section>

<section class="role-section" aria-labelledby="custom-role-title">
    <div class="role-section-head">
        <div><h2 id="custom-role-title">Rôles personnalisés</h2><p>Rôles réellement attribuables aux comptes des employés.</p></div>
        <span class="role-section-count">{{ $roles->total() }} rôle(s)</span>
    </div>
    @if($roles->isEmpty())
        <div class="role-empty">Aucun rôle personnalisé. Créez votre premier rôle, puis attribuez-le depuis la fiche d’un employé disposant d’un compte.</div>
    @else
        <div class="role-grid">
            @foreach($roles as $role)
                <article class="role-card">
                    <div class="role-card-head">
                        <span class="role-card-icon" aria-hidden="true"><x-lucide-shield/></span>
                        <div class="role-card-title"><h3>{{ $role->name }}</h3><p>{{ $role->company->legal_name }}</p></div>
                    </div>
                    <div class="role-badges">
                        <span class="role-badge">{{ $role->users_count }} utilisateur(s)</span>
                        <span class="role-badge scope">{{ $role->scope ? ($scopeLabels[$role->scope] ?? $role->scope) : (\App\Services\PermissionCatalog::roles()[$role->base_role] ?? $role->base_role) }}</span>
                    </div>
                    @include('roles._permission-summary', [
                        'permissions' => $role->permissions,
                        'groups' => $permissionGroups,
                        'labels' => $permissionLabels,
                    ])
                    <footer class="role-card-footer"><a class="btn secondary" href="{{ route('roles.edit', $role) }}">Modifier le rôle</a></footer>
                </article>
            @endforeach
        </div>
        <div class="role-pagination">{{ $roles->links() }}</div>
    @endif
</section>
@endsection
