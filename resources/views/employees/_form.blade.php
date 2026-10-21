@if($errors->any())
    <div class="employee-errors" role="alert">
        <strong>Le formulaire contient des erreurs.</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<section class="employee-form-section">
    <div class="employee-form-title">
        <h2>Informations professionnelles</h2>
        <p>Identité, fonction et rattachement organisationnel.</p>
    </div>
    <div class="employee-form-grid">
        @if(!$employee->exists && auth()->user()->isAdmin())
            <div class="employee-field full">
                <label for="company_id">Compagnie <span class="required">*</span></label>
                <select @class(['invalid' => $errors->has('company_id')]) id="company_id" name="company_id" required>
                    <option value="">Sélectionner une compagnie</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->legal_name }}</option>
                    @endforeach
                </select>
                @error('company_id')<span class="employee-error">{{ $message }}</span>@enderror
            </div>
        @endif
        <div class="employee-field">
            <label for="last_name">Nom <span class="required">*</span></label>
            <input @class(['invalid' => $errors->has('last_name')]) id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" maxlength="100" required>
            @error('last_name')<span class="employee-error">{{ $message }}</span>@enderror
        </div>
        <div class="employee-field">
            <label for="first_name">Prénom <span class="required">*</span></label>
            <input @class(['invalid' => $errors->has('first_name')]) id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" maxlength="100" required>
            @error('first_name')<span class="employee-error">{{ $message }}</span>@enderror
        </div>
        <div class="employee-field">
            <label for="internal_role">Fonction interne</label>
            <input @class(['invalid' => $errors->has('internal_role')]) id="internal_role" name="internal_role" value="{{ old('internal_role', $employee->internal_role) }}" maxlength="100" placeholder="Ex. Cheffe de guichet">
            @error('internal_role')<span class="employee-error">{{ $message }}</span>@enderror
        </div>
        <div class="employee-field">
            <label for="agency_id">Agence</label>
            <select @class(['invalid' => $errors->has('agency_id')]) id="agency_id" name="agency_id">
                <option value="">Siège / aucune agence</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" @selected((string) old('agency_id', $employee->agency_id) === (string) $agency->id)>{{ $agency->name }}@if(auth()->user()->isAdmin()) — {{ $agency->company->legal_name }}@endif</option>
                @endforeach
            </select>
            @error('agency_id')<span class="employee-error">{{ $message }}</span>@enderror
        </div>
    </div>
</section>

@if(!$employee->exists)
    <section class="employee-form-section">
        <div class="employee-form-title">
            <h2>Compte et accès obligatoires</h2>
            <p>Chaque employé de la plateforme possède un compte actif et un rôle.</p>
        </div>
        <div class="employee-form-grid">
            <div class="employee-field">
                <label for="account_email">Email de connexion <span class="required">*</span></label>
                <input @class(['invalid' => $errors->has('account_email')]) id="account_email" type="email" name="account_email" value="{{ old('account_email') }}" maxlength="150" required>
                @error('account_email')<span class="employee-error">{{ $message }}</span>@enderror
            </div>
            <div class="employee-field">
                <label for="account_role">Rôle du compte <span class="required">*</span></label>
                <select @class(['invalid' => $errors->has('account_role')]) id="account_role" name="account_role" required>
                    <option value="">Sélectionner</option>
                    @if(auth()->user()->isAdmin())<option value="company_manager" @selected(old('account_role') === 'company_manager')>Gestionnaire de compagnie</option>@endif
                    @if(in_array(auth()->user()->role, ['admin', 'company_manager'], true))<option value="agency_manager" @selected(old('account_role') === 'agency_manager')>Responsable d’agence</option>@endif
                    <option value="agent" @selected(old('account_role') === 'agent')>Agent</option>
                </select>
                @error('account_role')<span class="employee-error">{{ $message }}</span>@enderror
            </div>
            @if(auth()->user()->canManageRoles())
                <div class="employee-field full">
                    <label for="custom_role_id">Permissions du compte</label>
                    <select id="custom_role_id" name="custom_role_id">
                        <option value="">Permissions standard du rôle</option>
                        @foreach(\App\Models\CustomRole::with('company')->when(!auth()->user()->isAdmin(), fn ($query) => $query->where('company_id', auth()->user()->company_id))->orderBy('name')->get() as $customRole)
                            <option value="{{ $customRole->id }}" data-role="{{ $customRole->scope ? 'custom' : $customRole->base_role }}" data-company="{{ $customRole->company_id }}" @selected((string) old('custom_role_id') === (string) $customRole->id)>{{ $customRole->name }} — {{ $customRole->company->legal_name }}</option>
                        @endforeach
                    </select>
                    <small>Choisissez un rôle personnalisé compatible pour limiter les accès dès la création.</small>
                    @error('custom_role_id')<span class="employee-error">{{ $message }}</span>@enderror
                </div>
            @endif
            <div class="employee-field full role-explanation" id="role-explanation">Sélectionnez un rôle pour connaître le périmètre d’accès associé.</div>
            <div class="employee-field">
                <label for="password">Mot de passe initial <span class="required">*</span></label>
                <input @class(['invalid' => $errors->has('password')]) id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
                @error('password')<span class="employee-error">{{ $message }}</span>@enderror
            </div>
            <div class="employee-field">
                <label for="password_confirmation">Confirmation <span class="required">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="employee-field full account-note">Le compte est activé à la création. Sa désactivation rend l’employé inactif sur la plateforme tout en conservant son historique.</div>
        </div>
    </section>
@endif

<div class="employee-form-footer">
    <a class="btn secondary" href="{{ $employee->exists ? route('employees.show', $employee) : route('employees.index') }}">Annuler</a>
    <button class="btn" type="submit">{{ $submitLabel }}</button>
</div>

@if(!$employee->exists)
<script>
(() => {
    const role = document.getElementById('account_role');
    const company = document.getElementById('company_id');
    const custom = document.getElementById('custom_role_id');
    const explanation = document.getElementById('role-explanation');
    const ownCompany = {{ Illuminate\Support\Js::from(auth()->user()->company_id) }};
    const descriptions = {
        company_manager: 'Gestionnaire de compagnie : gère la compagnie, ses agences, ses employés, sa flotte et ses documents.',
        agency_manager: 'Responsable d’agence : agit dans le périmètre de son agence.',
        agent: 'Agent : dispose des accès attribués à son rôle.'
    };

    function refresh() {
        explanation.textContent = descriptions[role.value] || 'Sélectionnez un rôle pour connaître le périmètre d’accès associé.';
        if (!custom) return;
        Array.from(custom.options).slice(1).forEach(option => {
            option.disabled = (option.dataset.role !== 'custom' && option.dataset.role !== role.value)
                || option.dataset.company !== String(company ? company.value : ownCompany);
            option.hidden = option.disabled;
            if (option.disabled && option.selected) custom.value = '';
        });
    }

    role.addEventListener('change', refresh);
    company?.addEventListener('change', refresh);
    refresh();
})();
</script>
@endif
