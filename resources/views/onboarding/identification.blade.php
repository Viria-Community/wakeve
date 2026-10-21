@extends('layouts.onboarding')
@section('title', 'Inscription de votre compagnie')
@section('step1', 'active')

@section('content')
<div class="page-intro"><h1>Inscription de votre compagnie</h1><p>Renseignez les informations nécessaires pour soumettre votre demande d'enregistrement à la PNMG.</p></div>
<form method="POST" action="{{ route('registration.identification.store') }}">@csrf
<section class="card"><div class="card-body">
<h2 class="section-title">Informations sur la compagnie</h2>
<div class="grid">
    <div aria-hidden="true" style="position:absolute;left:-10000px"><label for="company_website">Site internet</label><input id="company_website" name="company_website" tabindex="-1" autocomplete="off"></div>
    <div><label for="legal_name">Raison sociale *</label><input id="legal_name" name="legal_name" value="{{ old('legal_name', $data['legal_name'] ?? '') }}" required></div>
    <div><label for="trade_name">Nom commercial</label><input id="trade_name" name="trade_name" value="{{ old('trade_name', $data['trade_name'] ?? '') }}"></div>
    <div><label for="rccm">Numéro RCCM *</label><input id="rccm" name="rccm" value="{{ old('rccm', $data['rccm'] ?? '') }}" required maxlength="50" placeholder="Ex. GA-LBV-01-2026-B12-00123">@error('rccm')<span class="field-error-message">{{ $message }}</span>@enderror</div>
    <div><label for="nif">Numéro NIF</label><input id="nif" name="nif" value="{{ old('nif', $data['nif'] ?? '') }}"></div>
@include('shared.geography-fields', ['location' => $data, 'fieldClass' => ''])
    <div><label for="phone">Téléphone *</label><input id="phone" name="phone" value="{{ old('phone', $data['phone'] ?? '') }}" required></div>
    <div class="full"><label for="company_email">Email de la compagnie *</label><input id="company_email" type="email" name="company_email" value="{{ old('company_email', $data['company_email'] ?? '') }}" required></div>
    <label class="check full"><input type="checkbox" name="terms" value="1" required><span>Je certifie l’exactitude des informations fournies, j’accepte les <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener">conditions générales</a> et j’ai pris connaissance de la <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">politique de confidentialité</a>.</span></label>
</div></div><div class="card-actions"><span></span><button class="button" type="submit" aria-label="Continuer vers l’étape Gestionnaire">Continuer</button></div></section></form>
<aside class="process-note">Une agence Siège sera créée automatiquement à partir des coordonnées de votre compagnie. Son adresse peut être différente de celles des agences secondaires.</aside>

@endsection
