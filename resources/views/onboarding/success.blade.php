@extends('layouts.onboarding')
@section('title', 'Demande soumise')
@section('step1', 'done')
@section('step2', 'done')
@section('step3', 'done')
@section('step4', 'done')
@section('step5', 'active')
@section('content')
<section class="card success-content"><div class="card-body">
    <div class="success-mark"><x-lucide-circle-check aria-hidden="true" /></div>
    <h1>Demande soumise avec succès !</h1>
    <p class="intro">Votre dossier pour <strong>{{ $company->legal_name }}</strong> a été transmis à l'administration. Conservez sa référence pour le suivi.</p>
    <div class="reference-box">
        <div style="color:#64748b;font-size:12px;font-weight:800;text-transform:uppercase">Numéro de référence</div>
        <div id="reference" class="reference-number" style="margin:8px 0;font-size:25px;font-weight:850;color:var(--dark);letter-spacing:1px">{{ $company->application_reference }}</div>
        <button class="button secondary" type="button" aria-label="Copier la référence du dossier" onclick="navigator.clipboard.writeText(document.getElementById('reference').textContent.trim());this.textContent='Copiée'">Copier</button>
    </div>
    <div class="success-actions">
        <a class="button secondary" href="{{ route('login') }}">Se connecter</a>
        <a class="button" href="{{ route('registration.summary', $company->application_reference) }}">Télécharger le PDF</a>
    </div>
    <p class="hint success-hint">Le compte du gestionnaire reste limité au suivi de la demande jusqu'à sa validation par l'administrateur.</p>
</div></section>
@push('styles')<style>.success-content{text-align:center;max-width:660px}.success-mark{width:70px;height:70px;margin:0 auto 20px;border-radius:50%;display:grid;place-items:center;background:var(--pale);color:var(--green)}.success-mark svg{width:36px;height:36px;stroke-width:1.8}.success-content h1{margin:0 0 8px;font-size:29px}.success-content .intro{margin:0 auto 24px;color:var(--muted);line-height:1.6}.reference-box{padding:20px;border:1px solid #b8ddcf;border-radius:10px;background:var(--pale)}.success-actions{display:flex;justify-content:center;flex-wrap:wrap;gap:12px;margin-top:22px}.success-hint{margin-top:22px}@media(max-width:700px){.success-content .card-body{padding:24px 16px}.reference-number{font-size:18px!important;overflow-wrap:anywhere;letter-spacing:.4px!important}.success-actions{flex-direction:column}.success-actions .button{width:100%}}</style>@endpush
@endsection
