@extends('layouts.app')
@section('title', 'Départ - PNMG')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:24px;flex-wrap:wrap">
    <div>
        <a href="{{ route('departures.index') }}">← Retour aux départs</a>
        <h1 style="margin:6px 0 0;font-size:28px">Départ du {{ \Illuminate\Support\Carbon::parse($departure->departure_date)->format('d/m/Y') }} à {{ \Illuminate\Support\Carbon::parse($departure->departure_time)->format('H:i') }}</h1>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @can('update', $departure)
            <a class="btn-action secondary" href="{{ route('departures.edit', $departure) }}">Modifier</a>
            @if($departure->isScheduled())
                <form method="POST" action="{{ route('departures.cancel', $departure) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button class="btn-action danger" type="submit">Annuler le départ</button>
                </form>
            @endif
        @endcan
    </div>
</div>

<table class="table">
    <tbody>
        <tr><th>Ligne</th><td>{{ $departure->line->label ?? '—' }}</td></tr>
        <tr><th>Origine</th><td>{{ $departure->line->originPlace->name ?? '—' }}</td></tr>
        <tr><th>Destination</th><td>{{ $departure->line->destinationPlace->name ?? '—' }}</td></tr>
        <tr><th>Véhicule</th><td>{{ $departure->fleetUnit->type ?? '—' }} {{ $departure->fleetUnit->registration_number ?? '' }}</td></tr>
        <tr><th>Capacité</th><td>{{ $departure->fleetUnit->capacity ?? '—' }} place(s)</td></tr>
        <tr><th>État</th><td>{{ $departure->isScheduled() ? 'Programmé' : 'Annulé' }}</td></tr>
    </tbody>
</table>
@endsection
