@push('styles')
<style>
    .company-form-head{margin-bottom:24px}.company-form-head h1{margin:6px 0;font-size:32px}.company-form-head p,.form-breadcrumb{margin:0;color:#64748b}.form-breadcrumb{font-size:12px}.company-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.form-section{padding:24px}.form-section+.form-section{border-top:1px solid #e2e8f0}.form-section-title{display:flex;gap:12px;align-items:flex-start;margin-bottom:20px}.form-section-number{width:30px;height:30px;display:grid;place-items:center;flex:0 0 auto;border-radius:8px;background:#E8F5F0;color:var(--green);font-weight:800}.form-section-title h2{margin:0;font-size:18px}.form-section-title p{margin:4px 0 0;color:#64748b;font-size:13px}.company-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.form-field.full{grid-column:1/-1}.form-field label,.mode-label{display:block;margin-bottom:7px;font-size:13px;font-weight:700}.required{color:var(--danger)}.form-field input,.form-field select{width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px}.form-field input:focus,.form-field select:focus{outline:3px solid #E8F5F0;border-color:var(--green)}.form-field input.field-error,.form-field select.field-error{border-color:var(--danger)}.field-help{display:block;margin-top:5px;color:#64748b;font-size:11px}.field-error-message{display:block;margin-top:5px;color:#b91c1c;font-size:12px}.form-errors{margin:0 24px 20px;padding:14px;border-radius:9px;background:#fef2f2;color:#991b1b}.form-errors strong{display:block}.form-errors ul{margin:7px 0 0;padding-left:20px}.form-footer{display:flex;justify-content:flex-end;gap:10px;padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc}.form-footer .btn{min-width:150px}.form-note{margin-right:auto;align-self:center;color:#64748b;font-size:12px}
    @media(max-width:760px){.company-form-grid{grid-template-columns:1fr}.form-field.full{grid-column:auto}.form-footer{flex-direction:column-reverse}.form-footer .btn{width:100%}.form-note{margin:0;text-align:center}}
</style>
@endpush

@if($errors->any())
<div class="form-errors" role="alert"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">1</span><div><h2>Identité juridique</h2><p>Informations officielles permettant d’identifier l’opérateur.</p></div></div>
    <div class="company-form-grid">
        <div class="form-field"><label for="legal_name">Raison sociale <span class="required">*</span></label><input @class(['field-error' => $errors->has('legal_name')]) id="legal_name" name="legal_name" value="{{ old('legal_name',$company->legal_name) }}" maxlength="150" required aria-invalid="{{ $errors->has('legal_name') ? 'true' : 'false' }}">@error('legal_name')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field"><label for="trade_name">Nom commercial</label><input @class(['field-error' => $errors->has('trade_name')]) id="trade_name" name="trade_name" value="{{ old('trade_name',$company->trade_name) }}" maxlength="150">@error('trade_name')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field"><label for="rccm">RCCM <span class="required">*</span></label><input @class(['field-error' => $errors->has('rccm')]) id="rccm" name="rccm" value="{{ old('rccm',$company->rccm) }}" maxlength="50" placeholder="Ex. GA-LBV-01-2026-B12-00123" required aria-invalid="{{ $errors->has('rccm') ? 'true' : 'false' }}">@error('rccm')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field"><label for="nif">NIF</label><input @class(['field-error' => $errors->has('nif')]) id="nif" name="nif" value="{{ old('nif',$company->nif) }}" maxlength="50">@error('nif')<span class="field-error-message">{{ $message }}</span>@enderror</div>
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">2</span><div><h2>Coordonnées</h2><p>Contacts administratifs et localisation du siège.</p></div></div>
    <div class="company-form-grid">
        <div class="form-field"><label for="email">Adresse email <span class="required">*</span></label><input @class(['field-error' => $errors->has('email')]) id="email" type="email" name="email" value="{{ old('email',$company->email) }}" maxlength="150" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">@error('email')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field"><label for="phone">Téléphone <span class="required">*</span></label><input @class(['field-error' => $errors->has('phone')]) id="phone" type="tel" name="phone" value="{{ old('phone',$company->phone) }}" maxlength="30" required aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}">@error('phone')<span class="field-error-message">{{ $message }}</span>@enderror</div>
@include('shared.geography-fields', ['location' => $company->toArray(), 'fieldClass' => 'form-field'])
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">3</span><div><h2>Type d’activité</h2><p>Une compagnie est associée à un seul mode de transport.</p></div></div>
    @php($modeLabels=['road'=>'Transport routier','river'=>'Transport fluvial','maritime'=>'Transport maritime','rail'=>'Transport ferroviaire'])
    @php($selectedMode=old('transport_mode_id',$company->transportModes->first()?->id))
    @php($selectedMode=is_scalar($selectedMode)?$selectedMode:null)
    <div class="form-field full"><label for="transport_mode_id">Mode de transport <span class="required">*</span></label><select @class(['field-error' => $errors->has('transport_mode_id')]) id="transport_mode_id" name="transport_mode_id" required aria-invalid="{{ $errors->has('transport_mode_id') ? 'true' : 'false' }}"><option value="">Sélectionner un mode</option>@foreach($transportModes as $mode)<option value="{{ $mode->id }}" @selected((string) $selectedMode === (string) $mode->id)>{{ $modeLabels[$mode->label] ?? ucfirst($mode->label) }}</option>@endforeach</select>@error('transport_mode_id')<span class="field-error-message">{{ $message }}</span>@enderror<span class="field-help">Pour une autre activité, créez un profil de compagnie distinct.</span></div>
</section>

<div class="form-footer">
    <span class="form-note"><span class="required">*</span> Champs obligatoires</span>
    <a class="btn secondary" href="{{ $company->exists ? route('companies.show', $company) : route('companies.index') }}">Annuler</a>
    <button class="btn" type="submit">{{ $submitLabel }}</button>
</div>
