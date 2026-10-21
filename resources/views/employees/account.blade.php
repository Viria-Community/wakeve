@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Compte employé - PNMG')
@section('content')
@include('employees._styles')
<div class="employee-head"><div><h1>{{ $employee->user ? 'Gérer l’employé et ses accès' : 'Régulariser la fiche' }}</h1><p>{{ $employee->fullName() }} — {{ $employee->company->legal_name }}</p></div></div>
<form class="employee-card" method="POST" action="{{ route('employees.account.update', $employee) }}">@csrf @method('PUT')
<input type="hidden" name="account_exists" value="{{ $employee->user_id ? 1 : 0 }}">
<div class="employee-card-body">
@if($errors->any())<div class="employee-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="employee-form-grid">
<div class="employee-field"><label for="email">Email de connexion</label><input id="email" type="email" name="email" required maxlength="150" value="{{ old('email', $employee->user?->email) }}"></div>
<div class="employee-field"><label for="account-status">État de l’employé</label><select id="account-status" name="status"><option value="active" @selected(old('status', $employee->user?->status) === 'active')>Actif</option><option value="inactive" @selected(old('status', $employee->user?->status ?? 'inactive') === 'inactive')>Désactivé</option></select></div>
<div class="employee-field"><label for="role">Rôle standard</label><select id="role" name="role" required>
@foreach(\App\Services\PermissionCatalog::roles() as $key => $label) @if(auth()->user()->isAdmin() || $key !== 'company_manager')<option value="{{ $key }}" @selected(old('role', $employee->user?->role ?? 'agent') === $key)>{{ $label }}</option>@endif @endforeach
</select></div>
<div class="employee-field"><label for="account-custom-role">Permissions personnalisées</label><select id="account-custom-role" name="custom_role_id"><option value="">Permissions standard</option>@foreach($roles as $customRole)<option value="{{ $customRole->id }}" data-base="{{ $customRole->scope ? 'custom' : $customRole->base_role }}" @selected((string)old('custom_role_id', $employee->user?->custom_role_id) === (string)$customRole->id)>{{ $customRole->name }}</option>@endforeach</select></div>
@unless($employee->user)
<div class="employee-field"><label for="password">Mot de passe initial</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><small>Au moins 8 caractères, une majuscule, une minuscule et un chiffre.</small></div>
<div class="employee-field"><label for="password_confirmation">Confirmer le mot de passe</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
<div><button type="button" class="btn secondary" id="show-password" aria-pressed="false">Afficher les mots de passe</button></div>
@endunless
</div><p>L’état de l’employé est celui de son compte. La désactivation bloque sa connexion tout en conservant sa fiche et son historique. Un rôle personnalisé peut limiter ses permissions.</p>
<div class="employee-form-footer"><a class="btn secondary" href="{{ route('employees.show', $employee) }}">Annuler</a><button class="btn" type="submit">{{ $employee->user ? 'Enregistrer' : 'Créer le compte actif' }}</button></div>
</div></form>
<script>(() => {
const role = document.getElementById('role'), custom = document.getElementById('account-custom-role');
function refresh() { for (const option of Array.from(custom.options).slice(1)) { option.disabled = option.dataset.base !== 'custom' && option.dataset.base !== role.value; option.hidden = option.disabled; if (option.disabled && option.selected) custom.value = ''; } }
role.addEventListener('change', refresh); refresh();
const toggle = document.getElementById('show-password');
toggle?.addEventListener('click', () => { const show = toggle.getAttribute('aria-pressed') !== 'true'; for (const id of ['password','password_confirmation']) document.getElementById(id).type = show ? 'text' : 'password'; toggle.setAttribute('aria-pressed', String(show)); toggle.textContent = show ? 'Masquer les mots de passe' : 'Afficher les mots de passe'; });
})();</script>
@endsection
