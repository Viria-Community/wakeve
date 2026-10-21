@extends('layouts.public')

@section('title', 'Suivre une demande')

@push('styles')
<style>
    .tracking-intro { max-width: 690px; margin: 0 auto 26px; }
    .tracking-intro p { color: var(--muted); }
    .tracking-card { max-width: 690px; margin: auto; padding: clamp(22px, 5vw, 38px); border: 1px solid var(--border); border-radius: 12px; background: var(--surface); }
    .tracking-card h2 { margin-top: 0; }
    .tracking-form { display: grid; gap: 18px; }
    .tracking-form label { display: block; margin-bottom: 7px; font-weight: 700; }
    .tracking-form input { width: 100%; min-height: 46px; padding: 11px 13px; border: 1px solid var(--field-border); border-radius: 8px; font: inherit; }
    .tracking-form input:focus-visible { outline: var(--pnmg-focus); outline-offset: var(--pnmg-focus-offset); }
    .tracking-form small { display: block; margin-top: 5px; color: var(--muted); }
    .tracking-button { display: inline-flex; align-items: center; justify-content: center; min-height: 46px; width: max-content; padding: 10px 20px; border: 0; border-radius: 8px; background: var(--green); color: var(--surface); font: inherit; font-weight: 700; cursor: pointer; }
    .tracking-button:hover { background: var(--green-hover); }
    .tracking-button:focus-visible { outline: var(--pnmg-focus); outline-offset: var(--pnmg-focus-offset); }
    .tracking-error { padding: 13px 15px; border-left: 4px solid var(--danger); background: var(--danger-pale); color: var(--danger-text); }
    .tracking-result { margin-top: 26px; padding-top: 24px; border-top: 1px solid var(--border); }
    .tracking-result h2 { margin-bottom: 12px; }
    .tracking-badge { display: inline-flex; align-items: center; min-height: 30px; padding: 4px 11px; border-radius: 999px; font-size: 13px; font-weight: 800; }
    .tracking-badge.pending, .tracking-badge.validated { background: var(--gold-pale); color: var(--warning-text); }
    .tracking-badge.active { background: var(--pale); color: var(--green); }
    .tracking-badge.rejected, .tracking-badge.suspended { background: var(--danger-pale); color: var(--danger-text); }
    .tracking-result p { margin: 14px 0; }
    .tracking-result a { font-weight: 700; }
    @media(max-width:599.98px) { .tracking-form input { font-size: 16px; } .tracking-button { width: 100%; } }
</style>
@endpush

@section('content')
<div class="tracking-intro">
    <h1>Suivre une demande</h1>
    <p>Consultez l’état de votre dossier avec son numéro de référence et l’adresse e-mail du gestionnaire déclarée lors de l’inscription.</p>
</div>
<section class="tracking-card" aria-labelledby="tracking-title">
    <h2 id="tracking-title">Retrouver mon dossier</h2>
    @if(isset($lookupError))<p class="tracking-error" role="alert">{{ $lookupError }}</p>@endif
    @if($errors->any())<div class="tracking-error" role="alert">Vérifiez le numéro de dossier et l’adresse e-mail saisis.</div>@endif
    <form class="tracking-form" method="POST" action="{{ route('public.application-status.show') }}">
        @csrf
        <div><label for="reference">Numéro de dossier</label><input id="reference" name="reference" type="text" value="{{ old('reference') }}" placeholder="REF-GAB-2026-XXXXXXXX" autocomplete="off" maxlength="50" required><small>Ce numéro figure sur la confirmation de votre inscription.</small></div>
        <div><label for="email">E-mail du gestionnaire</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required></div>
        <button class="tracking-button" type="submit">Voir l’état du dossier</button>
    </form>
    @isset($company)
        @php
            $status = $company->status->value;
            $labels = ['pending' => 'En attente de validation', 'validated' => 'Validée', 'active' => 'Active', 'rejected' => 'Refusée', 'suspended' => 'Suspendue'];
            $messages = [
                'pending' => 'Votre dossier est en cours d’examen administratif.',
                'validated' => 'La demande a été validée. Consultez votre espace pour la suite.',
                'active' => 'La compagnie est active sur la plateforme.',
                'rejected' => 'Une correction est demandée. Connectez-vous pour consulter les détails et les pièces concernées.',
                'suspended' => 'L’accès opérationnel est suspendu. Connectez-vous pour consulter les informations disponibles.',
            ];
        @endphp
        <div class="tracking-result" role="status" aria-live="polite">
            <h2>{{ $company->legal_name }}</h2>
            <span class="tracking-badge {{ $status }}">{{ $labels[$status] ?? 'État à vérifier' }}</span>
            <p>{{ $messages[$status] ?? 'Connectez-vous pour consulter votre dossier.' }}</p>
            <a href="{{ route('login') }}">Accéder à mon espace sécurisé</a>
        </div>
    @endisset
</section>
@endsection
