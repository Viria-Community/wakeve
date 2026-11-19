@extends('layouts.app')
@section('title', 'Réductions par âge - PNMG')

@section('content')
<h1>Réductions par âge</h1>
<p class="muted">Réduction appliquée à tous les trajets et à toutes les classes, selon l'âge du passager.</p>

@if(session('status'))<div class="flash">{{ session('status') }}</div>@endif
@if($errors->any())
<div style="background:#fde8e8;color:#b42318;padding:12px;border-radius:12px;margin-bottom:16px">
    @foreach($errors->all() as $erreur)<div>{{ $erreur }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('reductions.store') }}" class="pnmg-card" style="margin:18px 0;display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;align-items:end">
    @csrf
    <div><label for="label">Libellé</label><input id="label" type="text" name="label" value="{{ old('label') }}" placeholder="Enfant" required></div>
    <div><label for="min_age">Âge minimum</label><input id="min_age" type="number" name="min_age" min="0" max="120" value="{{ old('min_age') }}" required></div>
    <div><label for="max_age">Âge maximum (facultatif)</label><input id="max_age" type="number" name="max_age" min="0" max="120" value="{{ old('max_age') }}"></div>
    <div><label for="percent">Réduction (%)</label><input id="percent" type="number" name="percent" min="1" max="100" value="{{ old('percent') }}" required></div>
    <div><button class="btn" type="submit">Ajouter la réduction</button></div>
</form>

<div class="billets">
@forelse($reductions as $reduction)
    <article class="billet">
        <div class="billet-tete">
            <div>
                <div class="billet-titre">{{ $reduction->label }} · {{ $reduction->percent }} %</div>
                <div class="billet-sous">
                    De {{ $reduction->min_age }} ans
                    @if($reduction->max_age !== null) à {{ $reduction->max_age }} ans @else et plus @endif
                </div>
            </div>
            <span class="status-label {{ $reduction->status === 'active' ? 'status-active' : 'status-inactive' }}">{{ $reduction->status === 'active' ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="billet-actions">
            <form method="POST" action="{{ route('reductions.toggle', $reduction) }}" style="display:inline">
                @csrf
                @method('PATCH')
                <button class="btn-action secondary" type="submit">{{ $reduction->status === 'active' ? 'Désactiver' : 'Activer' }}</button>
            </form>
        </div>
    </article>
@empty
    <div class="billet billet-vide">Aucune réduction définie pour votre compagnie.</div>
@endforelse
</div>
@endsection
