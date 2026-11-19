@extends('layouts.app')
@section('title', 'Ajouter un tarif - PNMG')

@push('styles')
<style>
    .fare-form-head{margin-bottom:24px}.fare-form-head h1{margin:6px 0 0;font-size:32px}.fare-form-sub{margin:8px 0 0;font-size:14px;color:#64748b}.fare-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.form-section{padding:24px}.form-section+.form-section{border-top:1px solid #e2e8f0}.form-section-title{display:flex;gap:12px;align-items:flex-start;margin-bottom:20px}.form-section-number{width:30px;height:30px;display:grid;place-items:center;flex:0 0 auto;border-radius:8px;background:#E8F5F0;color:var(--green);font-weight:800}.form-section-title h2{margin:0;font-size:18px}.form-section-title p{margin:4px 0 0;color:#64748b;font-size:13px}.fare-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.form-field.full{grid-column:1/-1}.form-field label{display:block;margin-bottom:7px;font-size:13px;font-weight:700}.required{color:var(--danger)}.form-field input,.form-field select{width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px}.form-field input:focus,.form-field select:focus{outline:3px solid #E8F5F0;border-color:var(--green)}.field-help{display:block;margin-top:5px;color:#64748b;font-size:11px}.field-error-message{display:block;margin-top:5px;color:#b91c1c;font-size:12px}.form-errors{margin:0 24px 20px;padding:14px;border-radius:9px;background:#fef2f2;color:#991b1b}.form-errors strong{display:block}.form-errors ul{margin:7px 0 0;padding-left:20px}.form-footer{display:flex;justify-content:flex-end;gap:10px;padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc}.form-footer .btn{min-width:150px}.form-note{margin-right:auto;align-self:center;color:#64748b;font-size:12px}.line-info{background:#f8fafc;border-radius:8px;padding:12px}.line-info strong{display:block;font-size:14px}.line-info small{display:block;color:#64748b;font-size:12px}
    @media(max-width:760px){.fare-form-grid{grid-template-columns:1fr}.form-field.full{grid-column:auto}.form-footer{flex-direction:column-reverse}.form-footer .btn{width:100%}.form-note{margin:0;text-align:center}}
</style>
@endpush
@section('content')
<form method="POST" action="{{ route('fares.store', $line) }}">@csrf

@if($errors->any())
<div class="form-errors" role="alert"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="fare-form-head">
    <div>
        <div class="breadcrumb">Offre de transport › Lignes › {{ $line->label }} › Tarifs › Ajouter</div>
        <h1>Ajouter un tarif</h1>
        <p class="fare-form-sub">{{ $line->label }} • {{ $line->company->legal_name }}</p>
    </div>
</div>

<div class="fare-form-card">
    <div class="line-info">
        <strong>Ligne : {{ $line->label }}</strong>
        <small>{{ $line->originPlace->name ?? '—' }} → {{ $line->destinationPlace->name ?? '—' }}</small>
    </div>

    <section class="form-section">
        <div class="form-section-title"><span class="form-section-number">1</span><div><h2>Détails du tarif</h2><p>Montant et classe de tarification.</p></div></div>
        <div class="fare-form-grid">
            <div class="form-field"><label for="amount">Montant (FCFA) <span class="required">*</span></label><input @class(['field-error' => $errors->has('amount')]) id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required aria-invalid="{{ $errors->has('amount') ? 'true' : 'false' }}" placeholder="Ex. : 15.50">@error('amount')<span class="field-error-message">{{ $message }}</span>@enderror</div>
            <div class="form-field"><label for="fare_class">Classe <span class="required">*</span></label><select id="fare_class" name="fare_class" required><option value="economique" @selected(old('fare_class', request('classe', 'standard')) === 'economique')>Économique</option><option value="standard" @selected(old('fare_class', request('classe', 'standard')) === 'standard')>Standard</option><option value="premiere" @selected(old('fare_class', request('classe', 'standard')) === 'premiere')>Première</option></select></div>
        </div>
    </section>

    <div class="form-footer">
        <span class="form-note"><span class="required">*</span> Champs obligatoires</span>
        <a class="btn secondary" href="{{ route('fares.index',$line) }}">Annuler</a>
        <button class="btn" type="submit">Créer le tarif</button>
    </div>
</div>
</form>


<script>
document.addEventListener('DOMContentLoaded', function () {
    var grille = @json(config('offre.grille'));
    var classe = document.getElementById('fare_class');
    var montant = document.getElementById('amount') || document.querySelector('input[name="amount"]');
    if (!classe || !montant) { console.warn('PNMG : champ classe ou montant introuvable'); return; }
    if (montant.value === '' && grille[classe.value] !== undefined) { montant.value = grille[classe.value]; }
    classe.addEventListener('change', function () {
        if (grille[classe.value] !== undefined) { montant.value = grille[classe.value]; }
    });
});
</script>
@endsection
