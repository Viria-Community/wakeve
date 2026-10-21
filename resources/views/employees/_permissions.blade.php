@if($employee->user)
@php
    $roleLabels = \App\Services\PermissionCatalog::roles();
    $permissionLabels = \App\Services\PermissionCatalog::labels();
    $permissionGroups = collect(\App\Services\PermissionCatalog::groups());
    $currentPermissions = collect($employee->user->customRole?->permissions ?? []);
    $activeGroups = $permissionGroups->map(function ($group) use ($currentPermissions) {
        return $group + ['granted' => $currentPermissions->intersect($group['permissions'])->values()];
    })->filter(fn ($group) => $group['granted']->isNotEmpty());
@endphp
<section class="employee-card employee-permissions-card">
    <div class="employee-card-head employee-permission-head">
        <div><h2>Permissions du compte</h2><p>Accès métier effectivement appliqués à cet utilisateur.</p></div>
        <span class="employee-permission-badge">{{ $employee->user->customRole?->name ?? 'Rôle standard' }}</span>
    </div>
    <div class="employee-card-body">
        @if($employee->user->customRole)
            @if($activeGroups->isEmpty())
                <div class="employee-standard-access"><strong>Aucun accès métier</strong><span>Ce rôle personnalisé ne donne actuellement accès à aucun module.</span></div>
            @else
                <div class="employee-permission-modules">
                    @foreach($activeGroups as $group)
                        <section class="employee-permission-module">
                            <div><strong>{{ $group['label'] }}</strong><span>{{ $group['granted']->count() }} autorisation(s)</span></div>
                            <ul>@foreach($group['granted'] as $permission)<li>{{ $permissionLabels[$permission] ?? $permission }}</li>@endforeach</ul>
                        </section>
                    @endforeach
                </div>
            @endif
        @else
            <div class="employee-standard-access">
                <strong>Permissions standard : {{ $roleLabels[$employee->user->role] ?? $employee->user->role }}</strong>
                <span>Les droits par défaut de ce rôle sont utilisés tant qu’aucun rôle personnalisé n’est attribué.</span>
            </div>
        @endif

        @if(auth()->user()->canManageRoles() && !$employee->user->isAdmin() && $employee->user_id !== auth()->id() && (auth()->user()->isAdmin() || $employee->user->role !== 'company_manager'))
            <form class="employee-permission-form" method="POST" action="{{ route('employees.permissions', $employee) }}">
                @csrf
                @method('PUT')
                <div class="employee-field">
                    <label for="custom_role_id">Attribuer un rôle personnalisé</label>
                    <select id="custom_role_id" name="custom_role_id">
                        <option value="">Conserver les permissions standard du rôle</option>
                        @foreach(\App\Models\CustomRole::where('company_id', $employee->company_id)->where(fn ($q) => $q->whereNotNull('scope')->orWhere('base_role', $employee->user->role))->orderBy('name')->get() as $customRole)
                            <option value="{{ $customRole->id }}" @selected((string) old('custom_role_id', $employee->user->custom_role_id) === (string) $customRole->id)>{{ $customRole->name }}</option>
                        @endforeach
                    </select>
                    @error('custom_role_id')<span class="employee-error" role="alert">{{ $message }}</span>@enderror
                    <small>Seuls les rôles de cette compagnie compatibles avec le rattachement de l’employé sont proposés.</small>
                </div>
                <div class="employee-permission-form-actions">
                    <a href="{{ route('roles.create') }}">Créer un nouveau rôle</a>
                    <button class="btn" type="submit">Appliquer ce rôle</button>
                </div>
            </form>
        @endif
    </div>
</section>
@endif
