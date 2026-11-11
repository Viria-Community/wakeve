@extends('layouts.app')
@section('title', 'Modifier une ligne - PNMG')

@push('styles')
<style>
    .line-form-head{margin-bottom:24px}.line-form-head h1{margin:6px 0;font-size:32px}.line-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.form-section{padding:24px}.form-section+.form-section{border-top:1px solid #e2e8f0}.form-section-title{display:flex;gap:12px;align-items:flex-start;margin-bottom:20px}.form-section-number{width:30px;height:30px;display:grid;place-items:center;flex:0 0 auto;border-radius:8px;background:#E8F5F0;color:var(--green);font-weight:800}.form-section-title h2{margin:0;font-size:18px}.form-section-title p{margin:4px 0 0;color:#64748b;font-size:13px}.line-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.form-field.full{grid-column:1/-1}.form-field label{display:block;margin-bottom:7px;font-size:13px;font-weight:700}.required{color:var(--danger)}.form-field input,.form-field select{width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px}.form-field input:focus,.form-field select:focus{outline:3px solid #E8F5F0;border-color:var(--green)}.field-help{display:block;margin-top:5px;color:#64748b;font-size:11px}.field-error-message{display:block;margin-top:5px;color:#b91c1c;font-size:12px}.form-errors{margin:0 24px 20px;padding:14px;border-radius:9px;background:#fef2f2;color:#991b1b}.form-errors strong{display:block}.form-errors ul{margin:7px 0 0;padding-left:20px}.form-footer{display:flex;justify-content:flex-end;gap:10px;padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc}.form-footer .btn{min-width:150px}.form-note{margin-right:auto;align-self:center;color:#64748b;font-size:12px}.stops-list{margin:18px 0 0;padding:0;list-style:none}.stops-list li{display:flex;gap:10px;align-items:center;padding:12px;background:#f8fafc;border-radius:8px;margin-bottom:8px}.stops-list .stop-order{width:32px;height:32px;display:grid;place-items:center;border-radius:6px;background:#e2e8f0;font-weight:700}.stops-list .stop-name{flex:1}.stops-list .btn-icon{width:32px;height:32px;display:grid;place-items:center;border-radius:6px;border:1px solid #e2e8f0;background:#fff;color:#64748b}.btn-icon:hover{border-color:#cbd5e1}.add-stop-btn{width:100%;text-align:left;padding:12px 16px;border:1px dashed #cbd5e1;border-radius:8px;background:#f8fafc;color:#64748b;font-size:14px}.btn-icon-danger{color:#b91c1c}.btn-icon-danger:hover{background:#fee2e2}.status-badge{padding:5px 10px;border-radius:6px;font-size:12px;font-weight:700}.status-badge.active{background:#dcfce7;color:#166534}.status-badge.inactive{background:#e5e7eb;color:#374151}
    @media(max-width:760px){.line-form-grid{grid-template-columns:1fr}.form-field.full{grid-column:auto}.form-footer{flex-direction:column-reverse}.form-footer .btn{width:100%}.form-note{margin:0;text-align:center}}
</style>
@endpush
@section('content')

@if($errors->any())
<div class="form-errors" role="alert"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">1</span><div><h2>Identification</h2><p>Informations permettant d\'identifier la ligne.</p></div></div>
    <div class="line-form-grid">
        <div class="form-field"><label for="label">Libellé <span class="required">*</span></label><input @class(['field-error' => $errors->has('label')]) id="label" name="label" value="{{ old('label',$line->label) }}" maxlength="150" required aria-invalid="{{ $errors->has('label') ? 'true' : 'false' }}">@error('label')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field"><label>Compagnie</label><input type="text" value="{{ $line->company->legal_name }}" disabled></div>
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">2</span><div><h2>Itinéraire</h2><p>Origine, destination et points desservis.</p></div></div>
    <div class="line-form-grid">
        <div class="form-field"><label for="transport_mode_id">Mode de transport <span class="required">*</span></label><select @class(['field-error' => $errors->has('transport_mode_id')]) id="transport_mode_id" name="transport_mode_id" required aria-invalid="{{ $errors->has('transport_mode_id') ? 'true' : 'false' }}"><option value="">Sélectionner un mode</option>@foreach($transportModes as $mode)<option value="{{ $mode->id }}" @selected((string) old('transport_mode_id') === (string) $mode->id)>{{ $mode->label }}</option>@endforeach</select>@error('transport_mode_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field full"><label for="origin_place_id">Origine <span class="required">*</span></label><select @class(['field-error' => $errors->has('origin_place_id')]) id="origin_place_id" name="origin_place_id" required aria-invalid="{{ $errors->has('origin_place_id') ? 'true' : 'false' }}"><option value="">Sélectionner une origine</option>@foreach($places as $place)<option value="{{ $place->id }}" @selected((string) old('origin_place_id', $line->origin_place_id) === (string) $place->id)>{{ $place->name }}{{ $place->city ? ' ('.$place->city.')' : '' }}</option>@endforeach</select>@error('origin_place_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
        <div class="form-field full"><label for="destination_place_id">Destination <span class="required">*</span></label><select @class(['field-error' => $errors->has('destination_place_id')]) id="destination_place_id" name="destination_place_id" required aria-invalid="{{ $errors->has('destination_place_id') ? 'true' : 'false' }}"><option value="">Sélectionner une destination</option>@foreach($places as $place)<option value="{{ $place->id }}" @selected((string) old('destination_place_id', $line->destination_place_id) === (string) $place->id)>{{ $place->name }}{{ $place->city ? ' ('.$place->city.')' : '' }}</option>@endforeach</select>@error('destination_place_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
    </div>
</section>

<section class="form-section">
    <div class="form-section-title"><span class="form-section-number">3</span><div><h2>Points desservis</h2><p>Ordre des arrêts entre origine et destination.</p></div></div>
    <div class="line-form-grid">
        <div class="form-field full">
            <ul class="stops-list" id="stopsList">
                @php($oldStops = old('places', []));@php($existingStops = $line->places->map(fn ($p) => ['place_id' => $p->id, 'stop_order' => $p->pivot->stop_order])->toArray());@php($stops = $oldStops ?: $existingStops);@php($stopIndex = 0)
                @foreach($stops as $stop)
                    <li data-stop-index="{{ $stopIndex }}">
                        <span class="stop-order">{{ $stopIndex }}</span>
                        <select class="stop-place-id" name="places[{{ $stopIndex }}][place_id]" required>
                            <option value="">Sélectionner un point</option>
                            @foreach($places as $place)
                                <option value="{{ $place->id }}" @selected((string) ($stop['place_id'] ?? '') === (string) $place->id)>{{ $place->name }}{{ $place->city ? ' ('.$place->city.')' : '' }}</option>
                            @endforeach
                        </select>
                        <span class="stop-name" style="display:none">{{ $places->firstWhere('id', $stop['place_id'] ?? null)?->name ?? '' }}</span>
                        @if($line->exists)
                            <button type="button" class="btn-icon btn-icon-danger btn-remove-stop" aria-label="Supprimer"><x-lucide-x /></button>
                        @endif
                    </li>
                    @php($stopIndex++)
                @endforeach
            </ul>
            @if($line->exists)
                <button type="button" class="add-stop-btn" id="addStopBtn"><x-lucide-plus class="inline" /> Ajouter un point desservi</button>
            @endif
            <input type="hidden" name="places_count" id="placesCount" value="{{ $stopIndex }}">
            @error('places')<span class="field-error-message">{{ $message }}</span>@enderror
            <p class="field-help">L\'origine et la destination doivent figurer dans la liste. Les ordres sont consécutifs à partir de 0.</p>
        </div>
    </div>
</section>

<div class="form-footer">
    <span class="form-note"><span class="required">*</span> Champs obligatoires</span>
    <a class="btn secondary" href="{{ route('lines.show',$line) }}">Annuler</a>
    <button class="btn" type="submit">Enregistrer les modifications</button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const stopsList = document.getElementById('stopsList');
    const addStopBtn = document.getElementById('addStopBtn');
    const placesCountInput = document.getElementById('placesCount');
    const places = {{ Illuminate\Support\Js::from($places->pluck('name', 'id')->toArray()) }};

    function updateStopNames() {
        stopsList.querySelectorAll('li').forEach(li => {
            const select = li.querySelector('.stop-place-id');
            const nameSpan = li.querySelector('.stop-name');
            if (select && nameSpan) {
                nameSpan.textContent = places[select.value] || '';
            }
        });
    }

    if (addStopBtn) {
        addStopBtn.addEventListener('click', () => {
            const index = parseInt(placesCountInput.value);
            const li = document.createElement('li');
            li.dataset.stopIndex = index;
            li.innerHTML = `
                <span class="stop-order">${index}</span>
                <select class="stop-place-id" name="places[${index}][place_id]" required>
                    <option value="">Sélectionner un point</option>
                    ${Object.entries(places).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
                </select>
                <span class="stop-name" style="display:none"></span>
                <button type="button" class="btn-icon btn-icon-danger btn-remove-stop" aria-label="Supprimer"><x-lucide-x /></button>
            `;
            stopsList.appendChild(li);
            placesCountInput.value = index + 1;
            updateStopNames();
        });
    }

    stopsList.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-stop')) {
            e.preventDefault();
            const li = e.target.closest('li');
            li.remove();
            stopsList.querySelectorAll('li').forEach((li, i) => {
                li.dataset.stopIndex = i;
                li.querySelector('.stop-order').textContent = i;
            });
        }
    });

    stopsList.addEventListener('change', (e) => {
        if (e.target.classList.contains('stop-place-id')) {
            updateStopNames();
        }
    });

    document.getElementById('transport_mode_id')?.addEventListener('change', () => {
        updateStopNames();
    });

    document.getElementById('origin_place_id')?.addEventListener('change', updateStopNames);
    document.getElementById('destination_place_id')?.addEventListener('change', updateStopNames);

    updateStopNames();
});
</script>
@endpush

@endsection
