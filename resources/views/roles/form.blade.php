@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Personnaliser un rôle - PNMG')
@section('content')
@include('employees._styles')
@include('roles._styles')
@php
    $preset = !$role->exists ? $presets->firstWhere('id', (int) request('preset')) : null;
    $initialScope = $role->scope ?? ($role->exists ? ['company_manager' => 'company', 'agency_manager' => 'agency', 'agent' => 'self'][$role->base_role] : ($preset?->scope ?? 'company'));
    $selectedPermissions = old('permissions', $errors->any() ? [] : ($role->permissions ?? $preset?->permissions ?? []));
@endphp
<header class="role-page-head">
    <div>
        <h1>{{ $role->exists ? 'Modifier le rôle' : 'Créer un rôle personnalisé' }}</h1>
        <p>Définissez d’abord le périmètre, puis accordez les actions nécessaires module par module.</p>
    </div>
</header>
<form method="POST" action="{{ $role->exists ? route('roles.update', $role) : route('roles.store') }}" class="role-form-card">
    @csrf
    @if($role->exists) @method('PUT') @endif
    <div class="role-form-intro">
        @if($errors->any())<div class="employee-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="employee-form-grid">
            <div class="employee-field"><label for="name">Nom du rôle</label><input id="name" name="name" value="{{ old('name', $role->name ?? $preset?->name) }}" maxlength="100" required placeholder="Ex. Responsable en consultation"></div>
            @if(auth()->user()->isAdmin() && !$role->exists)
                <div class="employee-field"><label for="company_id">Compagnie</label><select id="company_id" name="company_id" required><option value="">Sélectionner</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->legal_name }}</option>@endforeach</select></div>
            @endif
            <div class="employee-field"><label for="scope">Périmètre d’accès</label><select id="scope" name="scope" required>@foreach(\App\Services\PermissionCatalog::scopes() as $key => $label)<option value="{{ $key }}" @selected(old('scope', $initialScope) === $key)>{{ $label }}</option>@endforeach</select></div>
        </div>
        <p class="role-help">Le périmètre « compagnie » couvre tous les modules sélectionnés. Le périmètre « agence » limite les accès à l’agence de rattachement. La fiche personnelle autorise seulement la consultation de sa propre fiche et de son agence.</p>
    </div>
    @include('roles._permission-selector', [
        'groups' => \App\Services\PermissionCatalog::groups(),
        'labels' => \App\Services\PermissionCatalog::labels(),
        'selectedPermissions' => $selectedPermissions,
        'legend' => 'Actions autorisées',
    ])
    <div class="role-form-footer"><a class="btn secondary" href="{{ route('roles.index') }}">Annuler</a><button class="btn" type="submit">Enregistrer le rôle</button></div>
</form>
<script>
(() => {
    const allowed = {{ Illuminate\Support\Js::from(collect(\App\Services\PermissionCatalog::scopes())->mapWithKeys(fn ($label, $key) => [$key => \App\Services\PermissionCatalog::forScope($key)])) }};
    const scope = document.getElementById('scope');
    const selector = document.querySelector('[data-permission-selector]');
    const refresh = () => {
        document.querySelectorAll('[data-permission]').forEach((label) => {
            const input = label.querySelector('input');
            const enabled = (allowed[scope.value] || []).includes(label.dataset.permission);
            label.hidden = !enabled;
            input.disabled = !enabled;
            if (!enabled) input.checked = false;
        });
        selector?.dispatchEvent(new Event('permissions:updated'));
    };
    scope.addEventListener('change', refresh);
    refresh();
})();
</script>
@endsection
