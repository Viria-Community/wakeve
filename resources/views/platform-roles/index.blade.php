@extends('layouts.admin')
@section('title', 'Rôles PNMG - Administration')
@section('content')
@include('employees._styles')
@include('roles._styles')
@php
    $permissionGroups = \App\Services\PlatformPermissionCatalog::groups();
    $permissionLabels = \App\Services\PlatformPermissionCatalog::labels();
@endphp
<header class="role-page-head">
    <div>
        <h1>Rôles du personnel PNMG</h1>
        <p>Chaque rôle rassemble les accès attribuables aux agents de la plateforme. Ouvrez le détail uniquement lorsque vous souhaitez vérifier une permission précise.</p>
    </div>
    <a class="btn" href="{{ route('platform-roles.create') }}">Nouveau rôle</a>
</header>

<section class="role-section" aria-labelledby="platform-role-list-title">
    <div class="role-section-head">
        <div><h2 id="platform-role-list-title">Rôles disponibles</h2><p>Les changements s’appliquent à tous les utilisateurs rattachés au rôle.</p></div>
        <span class="role-section-count">{{ $roles->total() }} rôle(s)</span>
    </div>
    @if($roles->isEmpty())
        <div class="role-empty">Aucun rôle PNMG n’est disponible. Créez le premier rôle pour pouvoir attribuer des accès au personnel.</div>
    @else
        <div class="role-grid">
            @foreach($roles as $role)
                <article class="role-card">
                    <div class="role-card-head">
                        <span class="role-card-icon" aria-hidden="true"><x-lucide-user-cog/></span>
                        <div class="role-card-title"><h3>{{ $role->name }}</h3><p>Rôle interne de l’administration PNMG</p></div>
                    </div>
                    <div class="role-badges">
                        <span class="role-badge">{{ $role->users_count }} utilisateur(s)</span>
                        <span class="role-badge scope">Accès national</span>
                    </div>
                    @include('roles._permission-summary', [
                        'permissions' => $role->permissions,
                        'groups' => $permissionGroups,
                        'labels' => $permissionLabels,
                        'emptyText' => 'Ce rôle ne donne actuellement accès à aucun module métier.',
                    ])
                    <footer class="role-card-footer"><a class="btn secondary" href="{{ route('platform-roles.edit', $role) }}">Modifier le rôle</a></footer>
                </article>
            @endforeach
        </div>
        <div class="role-pagination">{{ $roles->links() }}</div>
    @endif
</section>
@endsection
