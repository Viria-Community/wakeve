@if($errors->any())<div class="fleet-errors"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="fleet-form-body">
<section class="form-section"><div class="form-section-head"><span>01</span><div><h2>Identification de l’unité</h2><p>Informations permettant d’identifier le véhicule ou l’embarcation.</p></div></div><div class="fleet-form-grid">
@if(!$fleetUnit->exists && auth()->user()->isAdmin())<div class="fleet-field full"><label for="company_id">Compagnie <span class="required">*</span></label><select id="company_id" name="company_id" required><option value="">Sélectionner une compagnie</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string)old('company_id')===(string)$company->id)>{{ $company->legal_name }}</option>@endforeach</select>@error('company_id')<span class="fleet-error">{{ $message }}</span>@enderror</div>@endif
@php
    $modeCompanies = $fleetUnit->exists ? collect([$fleetUnit->company->load('transportModes')]) : (auth()->user()->isAdmin() ? $companies : collect([auth()->user()->company->load('transportModes')]));
    $modeOptions = $modeCompanies->mapWithKeys(fn ($c) => [$c->id => $c->transportModes->map(fn ($m) => ['id'=>$m->id,'label'=>config('fleet.labels.'.$m->label,$m->label),'types'=>config('fleet.types.'.$m->label,[])])->values()]);
@endphp
<div class="fleet-field"><label for="transport_mode_id">Mode de transport *</label><select id="transport_mode_id" name="transport_mode_id" required><option value="">Sélectionner un mode</option></select><small>Seules les activités déclarées de la compagnie sont disponibles.</small>@error('transport_mode_id')<span class="fleet-error">{{ $message }}</span>@enderror</div>
<div class="fleet-field"><label for="type">Type d’unité *</label><select id="type" name="type" required><option value="">Sélectionner un type compatible</option></select>@error('type')<span class="fleet-error">{{ $message }}</span>@enderror</div>
<script>
(() => {
    const companies = {{ Illuminate\Support\Js::from($modeOptions) }};
    const company = document.getElementById('company_id');
    const mode = document.getElementById('transport_mode_id');
    const type = document.getElementById('type');
    const initialCompany = {{ Illuminate\Support\Js::from($fleetUnit->company_id ?? auth()->user()->company_id) }};
    const initialMode = {{ Illuminate\Support\Js::from(old('transport_mode_id',$fleetUnit->transport_mode_id)) }};
    const initialType = {{ Illuminate\Support\Js::from(old('type',$fleetUnit->type)) }};
    const modes = () => companies[company ? company.value : initialCompany] || [];
    const refreshTypes = (selected = '') => {
        type.replaceChildren(new Option('Sélectionner un type compatible', ''));
        const selectedMode = modes().find(item => String(item.id) === mode.value);
        (selectedMode?.types || []).forEach(label => type.add(new Option(label,label,false,label === selected)));
    };
    const refreshModes = (selected = '') => {
        mode.replaceChildren(new Option('Sélectionner un mode', ''));
        modes().forEach(item => mode.add(new Option(item.label,item.id,false,String(item.id) === String(selected))));
        if (modes().length === 1) mode.value = modes()[0].id;
        refreshTypes();
    };
    company?.addEventListener('change', () => refreshModes());
    mode.addEventListener('change', () => refreshTypes());
    refreshModes(initialMode); refreshTypes(initialType);
})();
</script>
<div class="fleet-field"><label for="registration_number">Immatriculation ou référence</label><input id="registration_number" name="registration_number" value="{{ old('registration_number',$fleetUnit->registration_number) }}" maxlength="50" placeholder="Ex. : GA-123-AA"><small>Cette valeur doit être unique lorsqu’elle est renseignée.</small>@error('registration_number')<span class="fleet-error">{{ $message }}</span>@enderror</div>
</div></section>
<section class="form-section"><div class="form-section-head"><span>02</span><div><h2>Caractéristiques opérationnelles</h2><p>Capacité et situation actuelle de l’unité.</p></div></div><div class="fleet-form-grid">
<div class="fleet-field"><label for="capacity">Capacité en places <span class="required">*</span></label><input id="capacity" type="number" min="1" max="65535" name="capacity" value="{{ old('capacity',$fleetUnit->capacity) }}" placeholder="Ex. : 45" required>@error('capacity')<span class="fleet-error">{{ $message }}</span>@enderror</div>
<div class="fleet-field"><label for="availability">Disponibilité</label><select id="availability" name="availability"><option value="available" @selected(old('availability',$fleetUnit->availability ?? 'available')==='available')>Disponible</option><option value="unavailable" @selected(old('availability',$fleetUnit->availability)==='unavailable')>Indisponible</option></select><small>Indique si l’unité peut actuellement être exploitée.</small></div>
@if($fleetUnit->exists)<div class="fleet-field"><label for="status">État dans le registre</label><select id="status" name="status"><option value="active" @selected(old('status',$fleetUnit->status)==='active')>Active</option><option value="inactive" @selected(old('status',$fleetUnit->status)==='inactive')>Inactive</option></select><small>Une unité inactive reste dans l’historique, mais n’est plus opérationnelle.</small></div>@endif
</div></section></div>
<div class="fleet-form-footer"><a class="btn secondary" href="{{ $fleetUnit->exists?route('fleet-units.show',$fleetUnit):route('fleet-units.index') }}">Annuler</a><button class="btn">{{ $submitLabel }}</button></div>
