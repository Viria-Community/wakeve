@extends('layouts.onboarding')
@section('title', 'Gestionnaire principal')
@section('step1', 'done')
@section('step2', 'active')
@push('styles')
<style>
    .password-field { position: relative; }
    .password-field input { padding-right: 94px; }
    .password-toggle { position: absolute; top: 3px; right: 4px; min-height: 40px; padding: 0 10px; border: 0; border-radius: 5px; background: transparent; color: var(--green); font: inherit; font-size: 12px; font-weight: 700; cursor: pointer; }
    .password-toggle:hover { background: var(--pale); }
</style>
@endpush
@section('content')
<div class="page-intro"><h1>Gestionnaire principal</h1><p>Ces informations créent votre compte de connexion. L’e-mail de connexion peut être différent de celui de la compagnie.</p></div>
<form method="POST" action="{{ route('registration.manager.store') }}">@csrf
<section class="card"><div class="card-body"><div class="grid">
    <div class="full"><h2 class="section-title">Compte du gestionnaire</h2></div>
    <div class="full"><label for="manager_name">Nom complet *</label><input id="manager_name" name="manager_name" value="{{ old('manager_name', $data['manager_name'] ?? '') }}" required></div>
    <div class="full"><label for="manager_email">Email de connexion *</label><input id="manager_email" type="email" name="manager_email" value="{{ old('manager_email', $data['manager_email'] ?? '') }}" required></div>
    <div>
        <label for="password">Mot de passe *</label>
        <div class="password-field">
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            <button class="password-toggle" type="button" aria-controls="password" aria-label="Afficher le mot de passe" data-label="le mot de passe" hidden>Afficher</button>
        </div>
    </div>
    <div>
        <label for="password_confirmation">Confirmer le mot de passe *</label>
        <div class="password-field">
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
            <button class="password-toggle" type="button" aria-controls="password_confirmation" aria-label="Afficher la confirmation du mot de passe" data-label="la confirmation du mot de passe" hidden>Afficher</button>
        </div>
    </div>
</div></div><div class="card-actions"><a class="button secondary" href="{{ route('registration.identification') }}" aria-label="Retour aux informations de la compagnie">Précédent</a><button class="button" aria-label="Continuer vers les activités">Continuer</button></div></section></form>
<script>
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        const input = document.getElementById(toggle.getAttribute('aria-controls'));
        toggle.hidden = false;
        toggle.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            toggle.textContent = show ? 'Masquer' : 'Afficher';
            toggle.setAttribute('aria-label', `${show ? 'Masquer' : 'Afficher'} ${toggle.dataset.label}`);
        });
    });
</script>
@endsection
