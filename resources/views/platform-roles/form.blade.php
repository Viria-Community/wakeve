@extends('layouts.admin')
@section('title', 'Configurer un rôle PNMG')
@section('content')
@include('employees._styles')
@include('roles._styles')
<header class="role-page-head">
    <div>
        <h1>{{ $role->exists ? 'Modifier le rôle PNMG' : 'Créer un rôle PNMG' }}</h1>
        <p>Regroupez les droits nécessaires à une mission administrative précise. Les droits laissés désactivés restent interdits.</p>
    </div>
</header>
<form method="POST" action="{{ $role->exists ? route('platform-roles.update', $role) : route('platform-roles.store') }}" class="role-form-card">
    @csrf
    @if($role->exists) @method('PUT') @endif
    <div class="role-form-intro">
        @if($errors->any())<div class="employee-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="employee-field">
            <label for="name">Nom du rôle</label>
            <input id="name" name="name" value="{{ old('name', $role->name) }}" required maxlength="100" placeholder="Ex. Contrôleur documentaire">
        </div>
        <p class="role-help">Pour autoriser une validation ou une suspension, accordez aussi la permission de consultation du même module.</p>
    </div>
    @include('roles._permission-selector', [
        'groups' => \App\Services\PlatformPermissionCatalog::groups(),
        'labels' => \App\Services\PlatformPermissionCatalog::labels(),
        'selectedPermissions' => old('permissions', $role->permissions ?? []),
        'legend' => 'Permissions PNMG',
    ])
    <div class="role-form-footer"><a class="btn secondary" href="{{ route('platform-roles.index') }}">Annuler</a><button class="btn" type="submit">Enregistrer le rôle</button></div>
</form>
@endsection
