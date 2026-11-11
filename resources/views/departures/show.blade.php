@extends('layouts.app')
@section('title', 'Départ - PNMG')

@push('styles')
<style>
    .departure-detail-head{margin-bottom:28px}.departure-detail-head h1{margin:6px 0 0;font-size:32px}.departure-detail-sub{margin:8px 0 0;font-size:14px;color:#64748b}.departure-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px #0f172a12;overflow:hidden;margin-bottom:24px}.departure-card-header{padding:24px;border-bottom:1px solid #e2e8f0}.departure-card-header h2{margin:0 0 8px;font-size:18px}.departure-card-header .status-badge{padding:5px 10px;border-radius:6px;font-size:12px;font-weight:700}.departure-card-header .status-badge.scheduled{background:#dcfce7;color:#166534}.departure-card-header .status-badge.cancelled{background:#fee2e2;color:#b91c1c}.departure-card-body{padding:24px}.departure-card-body+.departure-card-body{border-top:1px solid #e2e8f0}.departure-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.departure-meta-item label{display:block;margin-bottom:6px;font-size:12px;color:#64748b;font-weight:700}.departure-meta-item div{font-size:15px}.departure-meta-item div strong{font-weight:700}.btn-action{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none}.btn-action.primary{background:var(--green);color:#fff}.btn-action.primary:hover{background:#059669}.btn-action.secondary{background:#f1f5f9;color:#475569}.btn-action.secondary:hover{background:#e2e8f0}.btn-action.danger{background:#fee2e2;color:#b91c1c}.btn-action.danger:hover{background:#fecaca}
</style>
@endpush

@section('content')
<div class="departure-detail-head">
    <div>
        <div class="breadcrumb">Offre de transport › Départs › {{ $departure->departure_date->locale('fr')->translatedFormat('d F Y') }}</div>
        <h1>{{ $departure->departure_date->locale('fr')->translatedFormat('l d F Y') }} — {{ $departure->departure_time->format('H\\hi') }}</h1>
        <p class="departure-detail-sub">{{ $departure->line->label }} • {{ $departure->line->company->legal_name }}</p>
    </div>
    <div style="display:flex;gap:8px">
        @can('update', $departure)<a class="btn-action secondary" href="{{ route('departures.edit',$departure) }}">Modifier <x-lucide-pencil aria-hidden="true"/></a>@endcan
        @if($departure->isScheduled())@can('update', $departure)<form method="POST" action="{{ route('departures.cancel',$departure) }}" style="display:inline"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button class="btn-action danger" type="submit">Annuler <x-lucide-x-circle aria-hidden="true"/></button></form>@endcan@endif
        <a class="btn-action primary" href="{{ route('departures.index') }}">Retour <x-lucide-arrow-left aria-hidden="true"/></a>
    </div>
</div>

<div class="departure-card">
    <div class="departure-card-header">
        <h2>Informations</h2>
        <span class="status-badge {{ $departure->status }}">{{ $departure->isScheduled() ? 'Programmé' : 'Annulé' }}</span>
    </div>
    <div class="departure-card-body">
        <div class="departure-meta">
            <div class="departure-meta-item">
                <label>Ligne</label>
                <div><a href="{{ route('lines.show',$departure->line) }}">{{ $departure->line->label }}</a></div>
            </div>
            <div class="departure-meta-item">
                <label>Compagnie</label>
                <div>{{ $departure->line->company->legal_name }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Origine</label>
                <div>{{ $departure->line->originPlace->name ?? '—' }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Destination</label>
                <div>{{ $departure->line->destinationPlace->name ?? '—' }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Date</label>
                <div>{{ $departure->departure_date->locale('fr')->translatedFormat('l d F Y') }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Heure</label>
                <div>{{ $departure->departure_time->format('H\\hi') }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Véhicule</label>
                <div>{{ $departure->fleetUnit->type }}{{ $departure->fleetUnit->registration_number ? ' ('.$departure->fleetUnit->registration_number.')' : '' }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Capacité</label>
                <div>{{ $departure->fleetUnit->capacity }} places</div>
            </div>
            <div class="departure-meta-item">
                <label>Agence</label>
                <div>{{ $departure->agency ? $departure->agency->name : 'Aucune' }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Créé le</label>
                <div>{{ $departure->created_at->locale('fr')->translatedFormat('d F Y à H\\hi') }}</div>
            </div>
            <div class="departure-meta-item">
                <label>Dernière modification</label>
                <div>{{ $departure->updated_at->locale('fr')->translatedFormat('d F Y à H\\hi') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="departure-card">
    <div class="departure-card-header">
        <h2>Itinéraire</h2>
    </div>
    <div class="departure-card-body">
        <div style="padding:12px 0">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                <div style="width:24px;height:24px;border-radius:50%;background:#dcfce7;color:#166534;display:grid;place-items:center;font-weight:700;">O</div>
                <strong>{{ $departure->line->originPlace->name ?? '—' }}</strong>
                <span style="color:#64748b;font-size:12px">{{ $departure->line->originPlace->city ?: '' }}</span>
            </div>
            @forelse($departure->line->places as $place)
                @if(!$place->is($departure->line->originPlace) && !$place->is($departure->line->destinationPlace))
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                        <div style="width:24px;height:24px;border-radius:50%;border:1px solid #cbd5e1;color:#64748b;display:grid;place-items:center;font-weight:700;">{{ $place->pivot->stop_order }}</div>
                        <strong>{{ $place->name }}</strong>
                        <span style="color:#64748b;font-size:12px">{{ $place->city ?: '' }}</span>
                    </div>
                @endif
            @empty
            @endforelse
            <div style="display:flex;align-items:center;gap:8px">
                <div style="width:24px;height:24px;border-radius:50%;background:#fee2e2;color:#b91c1c;display:grid;place-items:center;font-weight:700;">D</div>
                <strong>{{ $departure->line->destinationPlace->name ?? '—' }}</strong>
                <span style="color:#64748b;font-size:12px">{{ $departure->line->destinationPlace->city ?: '' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
