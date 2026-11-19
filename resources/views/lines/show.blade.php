@extends('layouts.app')
@section('title', $line->label.' - PNMG')

@push('styles')
<style>
    .line-detail-head{margin-bottom:28px}.line-detail-head h1{margin:6px 0 0;font-size:32px}.line-detail-sub{margin:8px 0 0;font-size:14px;color:#64748b}.line-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px #0f172a12;overflow:hidden;margin-bottom:24px}.line-card-header{padding:24px;border-bottom:1px solid #e2e8f0}.line-card-header h2{margin:0 0 8px;font-size:18px}.line-card-header .status-badge{padding:5px 10px;border-radius:6px;font-size:12px;font-weight:700}.line-card-header .status-badge.active{background:#dcfce7;color:#166534}.line-card-header .status-badge.inactive{background:#e5e7eb;color:#374151}.line-card-body{padding:24px}.line-card-body+.line-card-body{border-top:1px solid #e2e8f0}.line-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.line-meta-item label{display:block;margin-bottom:6px;font-size:12px;color:#64748b;font-weight:700}.line-meta-item div{font-size:15px}.line-meta-item div strong{font-weight:700}.stops-table{width:100%;border-collapse:collapse}.stops-table th,.stops-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #e2e8f0}.stops-table th{font-size:11px;color:#64748b;text-transform:uppercase;background:#f8fafc}.stops-table tr:last-child td{border-bottom:none}.btn-action{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none}.btn-action.primary{background:var(--green);color:#fff}.btn-action.primary:hover{background:#059669}.btn-action.secondary{background:#f1f5f9;color:#475569}.btn-action.secondary:hover{background:#e2e8f0}.btn-action.danger{background:#fee2e2;color:#b91c1c}.btn-action.danger:hover{background:#fecaca}.fares-table{width:100%;border-collapse:collapse}.fares-table th,.fares-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #e2e8f0}.fares-table th{font-size:11px;color:#64748b;text-transform:uppercase;background:#f8fafc}.fares-table tr:last-child td{border-bottom:none}.fare-amount{font-weight:700}.fare-class{color:#64748b;font-size:12px}
</style>
@endpush

@section('content')
<div class="line-detail-head">
    <div>
        <div class="breadcrumb">Offre de transport › Lignes › {{ $line->label }}</div>
        <h1>{{ $line->label }}</h1>
        <p class="line-detail-sub">{{ $line->company->legal_name }} • {{ $line->transportMode->label ?? '—' }}</p>
    </div>
    <div style="display:flex;gap:8px">
        @can('update', $line)<a class="btn-action secondary" href="{{ route('lines.edit',$line) }}">Modifier <x-lucide-pencil aria-hidden="true"/></a>@endcan
        @can('update', $line)<form method="POST" action="{{ route('lines.updateStatus', $line) }}" style="display:inline">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $line->isActive() ? 'inactive' : 'active' }}"><button class="btn-action {{ $line->isActive() ? 'secondary' : 'danger' }}" type="submit">{{ $line->isActive() ? 'Désactiver' : 'Activer' }} <x-lucide-toggle-left aria-hidden="true"/></button></form>@endcan
        <a class="btn-action secondary" href="{{ route('fares.index', $line) }}">Gérer les tarifs</a>
        <a class="btn-action primary" href="{{ route('lines.index') }}">Retour <x-lucide-arrow-left aria-hidden="true"/></a>
    </div>
</div>

<div class="line-card">
    <div class="line-card-header">
        <h2>Informations</h2>
        <span class="status-badge {{ $line->status }}">{{ $line->isActive() ? 'Active' : 'Inactive' }}</span>
    </div>
    <div class="line-card-body">
        <div class="line-meta">
            <div class="line-meta-item">
                <label>Compagnie</label>
                <div>{{ $line->company->legal_name }}</div>
            </div>
            <div class="line-meta-item">
                <label>Mode de transport</label>
                <div>{{ $line->transportMode->label ?? '—' }}</div>
            </div>
            <div class="line-meta-item">
                <label>Origine</label>
                <div><strong>{{ $line->originPlace->name ?? '—' }}</strong>{{ $line->originPlace->city ? ' ('.$line->originPlace->city.')' : '' }}</div>
            </div>
            <div class="line-meta-item">
                <label>Destination</label>
                <div><strong>{{ $line->destinationPlace->name ?? '—' }}</strong>{{ $line->destinationPlace->city ? ' ('.$line->destinationPlace->city.')' : '' }}</div>
            </div>
            <div class="line-meta-item">
                <label>Créé le</label>
                <div>{{ $line->created_at->locale('fr')->translatedFormat('d F Y à H\\hi') }}</div>
            </div>
            <div class="line-meta-item">
                <label>Dernière modification</label>
                <div>{{ $line->updated_at->locale('fr')->translatedFormat('d F Y à H\\hi') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="line-card">
    <div class="line-card-header">
        <h2>Points desservis</h2>
    </div>
    <div class="line-card-body">
        <table class="stops-table">
            <thead><tr><th>Ordre</th><th>Lieu</th><th>Ville</th></tr></thead>
            <tbody>
            @forelse($line->places as $place)
                <tr>
                    <td><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#E8F5F0;color:var(--green);font-weight:700;">{{ $place->pivot->stop_order }}</span></td>
                    <td>{{ $place->name }}</td>
                    <td>{{ $place->city ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="color:#64748b;text-align:center;padding:20px;">Aucun point desservi enregistré.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="line-card">
    <div class="line-card-header">
        <h2>Départs prévus</h2>
        <div style="font-size:12px;color:#64748b">{{ $line->departures->where('status','scheduled')->count() }} départ(s) programmé(s) • {{ $line->departures->where('status','cancelled')->count() }} départ(s) annulé(s)</div>
    </div>
    <div class="line-card-body">
        @forelse($line->departures->where('deleted_at',null)->sortBy('departure_date')->sortBy('departure_time') as $departure)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #e2e8f0">
                <div>
                    <div style="font-weight:700">{{ $departure->departure_date->locale('fr')->translatedFormat('l d F Y') }}</div>
                    <div style="font-size:12px;color:#64748b">{{ $departure->departure_time->format('H\\hi') }} — {{ $departure->fleetUnit->type }}{{ $departure->fleetUnit->registration_number ? ' ('.$departure->fleetUnit->registration_number.')' : '' }}</div>
                </div>
                <div style="margin-left:auto">
                    @if($departure->isScheduled())
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:6px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;"><x-lucide-check-circle class="w-3 h-3" /> Programmé</span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:6px;background:#fee2e2;color:#b91c1c;font-size:12px;font-weight:700;"><x-lucide-x-circle class="w-3 h-3" /> Annulé</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="color:#64748b;text-align:center;padding:20px;">Aucun départ enregistré pour cette ligne.</p>
        @endforelse
    </div>
    <div class="line-card-footer" style="padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc">
        <a class="btn-action primary" href="{{ route('departures.create') }}?line_id={{ $line->id }}">Programmer un départ <x-lucide-plus-circle aria-hidden="true"/></a>
    </div>
</div>

<div class="line-card">
    <div class="line-card-header">
        <h2>Tarifs</h2>
    </div>
    <div class="line-card-body">
        @forelse($line->fares as $fare)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #e2e8f0">
                <div class="fare-amount">{{ number_format($fare->amount, 2, ',', ' ').' €' }}</div>
                @if($fare->fare_class)<div class="fare-class">{{ $fare->fare_class }}</div>@endif
                <div style="margin-left:auto">
                    @if($fare->isActive())
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:6px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;"><x-lucide-check-circle class="w-3 h-3" /> Actif</span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:6px;background:#e5e7eb;color:#374151;font-size:12px;font-weight:700;"><x-lucide-minus-circle class="w-3 h-3" /> Inactif</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="color:#64748b;text-align:center;padding:20px;">Aucun tarif enregistré pour cette ligne.</p>
        @endforelse
    </div>
    <div class="line-card-footer" style="padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc">
        <a class="btn-action primary" href="{{ route('fares.create',$line) }}">Définir un tarif <x-lucide-plus-circle aria-hidden="true"/></a>
    </div>
</div>

<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-top:20px">
    <h2 style="margin:0 0 4px;font-size:18px">Tarifs de la ligne</h2>
    @can('update', $line)<a class="btn-action primary" href="{{ route('fares.grille', $line) }}" style="float:right">Définir les prix</a>@endcan
    <p style="margin:0 0 16px;color:#64748b;font-size:13px">Un prix par classe. Le prix se remplit automatiquement dans le formulaire.</p>
    @foreach(config('offre.classes') as $code => $libelle)
        @php($tarif = $line->fares->firstWhere('fare_class', $code))
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-top:1px solid #f1f5f9">
            <div>
                <strong>{{ $libelle }}</strong>
                <div style="color:#64748b;font-size:13px">{{ $tarif ? number_format((float) $tarif->amount, 0, ',', ' ').' FCFA' : 'Tarif non défini' }}</div>
            </div>
            @can('update', $line)
                @if($tarif)
                    <a class="btn-action secondary" href="{{ route('fares.edit', [$line, $tarif]) }}">Modifier</a>
                @else
                    <a class="btn-action primary" href="{{ route('fares.create', $line) }}?classe={{ $code }}">Définir le tarif</a>
                @endif
            @endcan
        </div>
    @endforeach
</div>

<section class="pnmg-card" style="margin-top:20px">
    <h2 style="margin:0 0 4px">Trajets et prix</h2>
    <p class="muted" style="margin:0 0 14px">Le trajet complet garde son tarif. Tout trajet partiel coûte 50 % du tarif de sa classe. Renseignez un âge pour appliquer la réduction.</p>
    <form method="GET" style="display:flex;gap:10px;align-items:end;margin-bottom:14px">
        <div><label for="age">Âge du passager</label><input id="age" type="number" name="age" min="0" max="120" value="{{ $age }}"></div>
        <button class="btn" type="submit">Calculer</button>
        <a class="btn-action secondary" href="{{ route('reductions.index') }}">Gérer les réductions</a>
    </form>
    <table>
        <thead><tr><th>Trajet</th>@foreach($classes as $libelle)<th>{{ $libelle }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($trajets as $t)
            <tr>
                <td>{{ $t['de'] }} → {{ $t['a'] }}</td>
                @foreach($classes as $code => $libelle)
                    <td>{{ $t['prix'][$code] !== null ? number_format($t['prix'][$code], 0, ',', ' ').' FCFA' : '—' }}</td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="4">Ajoutez au moins deux arrêts à la ligne pour voir les trajets.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
