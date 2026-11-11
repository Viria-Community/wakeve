@extends('layouts.app')
@section('title', 'Modifier un départ - PNMG')

@push('styles')
<style>
    .departure-form-head{margin-bottom:24px}.departure-form-head h1{margin:6px 0;font-size:32px}.departure-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.form-section{padding:24px}.form-section+.form-section{border-top:1px solid #e2e8f0}.form-section-title{display:flex;gap:12px;align-items:flex-start;margin-bottom:20px}.form-section-number{width:30px;height:30px;display:grid;place-items:center;flex:0 0 auto;border-radius:8px;background:#E8F5F0;color:var(--green);font-weight:800}.form-section-title h2{margin:0;font-size:18px}.form-section-title p{margin:4px 0 0;color:#64748b;font-size:13px}.departure-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.form-field.full{grid-column:1/-1}.form-field label{display:block;margin-bottom:7px;font-size:13px;font-weight:700}.required{color:var(--danger)}.form-field input,.form-field select{width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px}.form-field input:focus,.form-field select:focus{outline:3px solid #E8F5F0;border-color:var(--green)}.field-help{display:block;margin-top:5px;color:#64748b;font-size:11px}.field-error-message{display:block;margin-top:5px;color:#b91c1c;font-size:12px}.form-errors{margin:0 24px 20px;padding:14px;border-radius:9px;background:#fef2f2;color:#991b1b}.form-errors strong{display:block}.form-errors ul{margin:7px 0 0;padding-left:20px}.form-footer{display:flex;justify-content:flex-end;gap:10px;padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc}.form-footer .btn{min-width:150px}.form-note{margin-right:auto;align-self:center;color:#64748b;font-size:12px}.fleet-unit-card{background:#f8fafc;border-radius:8px;padding:12px}.fleet-unit-card strong{display:block;font-size:14px}.fleet-unit-card small{display:block;color:#64748b;font-size:12px}
    @media(max-width:760px){.departure-form-grid{grid-template-columns:1fr}.form-field.full{grid-column:auto}.form-footer{flex-direction:column-reverse}.form-footer .btn{width:100%}.form-note{margin:0;text-align:center}}
</style>
@endpush

@if($errors->any())
<div class="form-errors" role="alert"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">1</span><div><h2>Ligne</h2><p>Sélectionnez la ligne concernée.</p></div></div>
    <div class="departure-form-grid">
        <div class="form-field full"><label for="line_id">Ligne <span class="required">*</span></label><select @class(['field-error' => $errors->has('line_id')]) id="line_id" name="line_id" required aria-invalid="{{ $errors->has('line_id') ? 'true' : 'false' }}"><option value="">Sélectionner une ligne</option>@foreach($lines as $line)<option value="{{ $line->id }}" @selected((string) old('line_id', $departure->line_id) === (string) $line->id)>{{ $line->label }} ({{ $line->originPlace->name ?? '—' }} → {{ $line->destinationPlace->name ?? '—' }})</option>@endforeach</select>@error('line_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">2</span><div><h2>Programmation</h2><p>Date et heure du départ.</p></div></div>
    <div class="departure-form-grid">
        <div class="form-field"><label for="departure_date">Date <span class="required">*</span></label><input @class(['field-error' => $errors->has('departure_date')]) id="departure_date" type="date" name="departure_date" value="{{ old('departure_date', $departure->departure_date?->format('Y-m-d')) }}" required aria-invalid="{{ $errors->has('departure_date') ? 'true' : 'false' }}">@error('departure_date')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field"><label for="departure_time">Heure <span class="required">*</span></label><input @class(['field-error' => $errors->has('departure_time')]) id="departure_time" type="time" name="departure_time" value="{{ old('departure_time', $departure->departure_time?->format('H:i')) }}" required aria-invalid="{{ $errors->has('departure_time') ? 'true' : 'false' }}">@error('departure_time')<span class="field-error-message">{{ $message }}</span>@enderror</div>
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">3</span><div><h2>Véhicule</h2><p>Unité de flotte affectée au départ.</p></div></div>
    <div class="departure-form-grid">
        <div class="form-field full"><label for="fleet_unit_id">Véhicule <span class="required">*</span></label><select @class(['field-error' => $errors->has('fleet_unit_id')]) id="fleet_unit_id" name="fleet_unit_id" required aria-invalid="{{ $errors->has('fleet_unit_id') ? 'true' : 'false' }}"><option value="">Sélectionner un véhicule</option>@foreach($fleetUnits as $unit)<option value="{{ $unit->id }}" data-capacity="{{ $unit->capacity }}" @selected((string) old('fleet_unit_id', $departure->fleet_unit_id) === (string) $unit->id)>{{ $unit->type }}{{ $unit->registration_number ? ' ('.$unit->registration_number.')' : '' }} — {{ $unit->capacity }} places</option>@endforeach</select>@error('fleet_unit_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field full">
            <div id="fleetUnitInfo" style="display:none" class="fleet-unit-card">
                <strong id="fleetUnitInfoLabel">Véhicule sélectionné</strong>
                <small id="fleetUnitInfoCapacity">Capacité : —</small>
            </div>
            <p class="field-help">Capacité affichée pour information. La capacité réelle est lue sur l\'unité de flotte.</p>
        </div>
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">4</span><div><h2>Agence (facultatif)</h2><p>L\'agence responsable de l\'exploitation.</p></div></div>
    <div class="departure-form-grid">
        <div class="form-field full"><label for="agency_id">Agence</label><select @class(['field-error' => $errors->has('agency_id')]) id="agency_id" name="agency_id"><option value="">Aucune agence</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected((string) old('agency_id', $departure->agency_id) === (string) $agency->id)>{{ $agency->name }}</option>@endforeach</select>@error('agency_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
    </div>
</section>

<div class="form-footer">
    <span class="form-note"><span class="required">*</span> Champs obligatoires</span>
    <a class="btn secondary" href="{{ route('departures.show',$departure) }}">Annuler</a>
    <button class="btn" type="submit">Enregistrer les modifications</button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fleetUnitSelect = document.getElementById('fleet_unit_id');
    const fleetUnitInfo = document.getElementById('fleetUnitInfo');
    const fleetUnitInfoLabel = document.getElementById('fleetUnitInfoLabel');
    const fleetUnitInfoCapacity = document.getElementById('fleetUnitInfoCapacity');

    fleetUnitSelect?.addEventListener('change', () => {
        const option = fleetUnitSelect.options[fleetUnitSelect.selectedIndex];
        if (option && option.dataset.capacity) {
            fleetUnitInfoLabel.textContent = 'Véhicule : '+fleetUnitSelect.options[fleetUnitSelect.selectedIndex].text.split(' —')[0];
            fleetUnitInfoCapacity.textContent = 'Capacité : '+option.dataset.capacity+' places';
            fleetUnitInfo.style.display = 'block';
        } else {
            fleetUnitInfo.style.display = 'none';
        }
    });

    const initialOption = fleetUnitSelect?.options[fleetUnitSelect.selectedIndex];
    if (initialOption && initialOption.dataset.capacity) {
        fleetUnitInfoLabel.textContent = 'Véhicule : '+initialOption.text.split(' —')[0];
        fleetUnitInfoCapacity.textContent = 'Capacité : '+initialOption.dataset.capacity+' places';
        fleetUnitInfo.style.display = 'block';
    }
});
</script>
@endpush

@endsection
