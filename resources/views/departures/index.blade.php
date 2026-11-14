@extends(auth()->user()->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Départs - PNMG')

@push('styles')
<style>
    .departures-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:28px}.departures-head h1{margin:4px 0 6px;font-size:32px}.departure-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.departure-filters{display:grid;grid-template-columns:repeat(4,1fr) auto;gap:10px;padding:18px;border-bottom:1px solid #e2e8f0}.departure-filters input,.departure-filters select{min-width:0;padding:11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.departure-table-wrap{overflow-x:auto}.departure-table{width:100%;border-collapse:collapse}.departure-table th,.departure-table td{padding:15px 18px;text-align:left;border-bottom:1px solid #e2e8f0}.departure-table th{font-size:11px;color:#64748b;text-transform:uppercase;background:#f8fafc}.departure-date{display:flex;flex-direction:column}.departure-date strong{font-size:16px}.departure-date small{font-size:12px;color:#64748b}.departure-line{display:flex;flex-direction:column}.departure-line strong{font-size:14px}.departure-line small{font-size:11px;color:#64748b}.departure-capacity{font-weight:700}.departure-btn{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none}.departure-btn.cancel{background:#fee2e2;color:#b91c1c}.departure-btn.cancel:hover{background:#fecaca}.departure-pagination{padding:18px}.empty-row{text-align:center;color:#64748b;padding:35px!important}
</style>
@endpush

@section('content')
<div class="departures-head">
    <div>
        <div class="breadcrumb">Offre de transport › Départs</div>
        <h1>Départs à venir</h1>
        <div class="muted">Programmation des départs pour les lignes actives.</div>
    </div>
    @can('create', App\Models\Departure::class)<a class="btn" href="{{ route('departures.create') }}">Nouveau départ</a>@endcan
</div>

<div class="departure-panel">
    <form class="departure-filters" method="GET" action="{{ route('departures.index') }}">
        <select name="line_id" aria-label="Ligne"><option value="">Toutes les lignes</option>@foreach($lines as $line)<option value="{{ $line->id }}" @selected((string)request('line_id')===(string)$line->id)>{{ $line->label }} ({{ $line->originPlace->name ?? '—' }} → {{ $line->destinationPlace->name ?? '—' }})</option>@endforeach</select>
        @if($agencies->isNotEmpty())<select name="agency_id" aria-label="Agence"><option value="">Toutes les agences</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected((string)request('agency_id')===(string)$agency->id)>{{ $agency->name }}</option>@endforeach</select>@endif
        <select name="status" aria-label="Statut"><option value="">Tous les statuts</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ['scheduled'=>'Programmé','cancelled'=>'Annulé'][$status] }}</option>@endforeach</select>
        <button class="btn" type="submit">Filtrer</button>
    </form>
    <div class="departure-table-wrap">
        <table class="departure-table">
            <thead><tr><th>Date</th><th>Heure</th><th>Ligne</th><th>Véhicule</th><th>Capacité</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($departures as $departure)
                <tr>
                    <td>
                        <div class="departure-date">
                            <strong>{{ $departure->departure_date->locale('fr')->translatedFormat('l d F Y') }}</strong>
                            <small>{{ $departure->departure_date->format('d/m/Y') }}</small>
                        </div>
                    </td>
                    <td><strong>{{ $departure->departure_time->format('H\\hi') }}</strong></td>
                    <td>
                        <div class="departure-line">
                            <strong>{{ $departure->line->label }}</strong>
                            <small>{{ $departure->line->originPlace->name ?? '—' }} → {{ $departure->line->destinationPlace->name ?? '—' }}</small>
                        </div>
                    </td>
                    <td>{{ $departure->fleetUnit->type }}{{ $departure->fleetUnit->registration_number ? ' ('.$departure->fleetUnit->registration_number.')' : '' }}</td>
                    <td class="departure-capacity">{{ $departure->fleetUnit->capacity }} places</td>
                    <td>
                        @if($departure->isScheduled())
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:99px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;"><x-lucide-check-circle class="w-3 h-3" /> Programmé</span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:99px;font-size:12px;font-weight:700;background:#fee2e2;color:#b91c1c;"><x-lucide-x-circle class="w-3 h-3" /> Annulé</span>
                        @endif
                    </td>
                    <td>
                        @if($departure->isScheduled())
                            @can('update', $departure)<form method="POST" action="{{ route('departures.cancel', $departure) }}" style="display:inline" onsubmit="return confirm('Annuler ce départ ?')">@csrf @method('PATCH')<input type="hidden" name="status" value="cancelled"><button class="departure-btn cancel" type="submit">Annuler <x-lucide-x class="w-3 h-3" /></button></form>@endcan
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Aucun départ ne correspond aux critères.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="departure-pagination">{{ $departures->links('pagination.pnmg') }}</div>
</div>
@endsection
