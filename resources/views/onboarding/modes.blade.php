@extends('layouts.onboarding')
@section('title', 'Mode de transport')
@section('step1', 'done')
@section('step2', 'done')
@section('step3', 'active')
@section('content')
<div class="page-intro"><h1>Mode de transport</h1><p>Sélectionnez l’unique mode de transport exploité par cette compagnie.</p></div>
<form method="POST" action="{{ route('registration.modes.store') }}">@csrf
<section class="card"><div class="card-body"><h2 class="section-title">Type d’activité de la compagnie</h2>
@php($labels=['road'=>'Transport routier','river'=>'Transport fluvial','maritime'=>'Transport maritime','rail'=>'Transport ferroviaire'])
<div><label for="transport_mode_id">Mode de transport *</label><select id="transport_mode_id" name="transport_mode_id" required><option value="">Sélectionner un mode</option>@foreach($transportModes as $mode)<option value="{{ $mode->id }}" @selected((string) old('transport_mode_id', $selected) === (string) $mode->id)>{{ $labels[$mode->label] ?? ucfirst($mode->label) }}</option>@endforeach</select>@error('transport_mode_id')<span class="field-error-message">{{ $message }}</span>@enderror</div>
<p class="availability-note">Une compagnie est associée à un seul mode de transport. Une autre activité doit faire l’objet d’un profil de compagnie distinct.</p>
</div><div class="card-actions"><a class="button secondary" href="{{ route('registration.manager') }}">Précédent</a><button class="button" type="submit" aria-label="Continuer vers les documents">Continuer</button></div></section></form>
@endsection
