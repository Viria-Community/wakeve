@extends('layouts.app')
@section('title', 'Tarifs - PNMG')

@push('styles')
<style>
    .fares-head{margin-bottom:28px}.fares-head h1{margin:6px 0 0;font-size:32px}.fares-head .line-info{margin:8px 0 0;font-size:14px;color:#64748b}.fares-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.fares-filters{display:grid;grid-template-columns:repeat(2,1fr) auto;gap:10px;padding:18px;border-bottom:1px solid #e2e8f0}.fares-filters select{min-width:0;padding:11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.fares-table-wrap{overflow-x:auto}.fares-table{width:100%;border-collapse:collapse}.fares-table th,.fares-table td{padding:15px 18px;text-align:left;border-bottom:1px solid #e2e8f0}.fares-table th{font-size:11px;color:#64748b;text-transform:uppercase;background:#f8fafc}.fare-amount{font-size:16px;font-weight:700}.fare-class{color:#64748b;font-size:12px}.fare-status{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:99px;font-size:12px;font-weight:700}.fare-status.active{background:#dcfce7;color:#166534}.fare-status.inactive{background:#e5e7eb;color:#374151}.btn-action{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none}.btn-action.primary{background:var(--green);color:#fff}.btn-action.primary:hover{background:#059669}.btn-action.secondary{background:#f1f5f9;color:#475569}.btn-action.secondary:hover{background:#e2e8f0}.fares-pagination{padding:18px}.empty-row{text-align:center;color:#64748b;padding:35px!important}
</style>
@endpush

@section('content')
<div class="fares-head">
    <div>
        <div class="breadcrumb">Offre de transport › Lignes › {{ $line->label }} › Tarifs</div>
        <h1>Tarifs</h1>
        <p class="fares-head line-info">{{ $line->label }} • {{ $line->company->legal_name }} • {{ $line->transportMode->label ?? '—' }}</p>
    </div>
    @can('create', App\Models\Fare::class)<a class="btn-action primary" href="{{ route('fares.create',$line) }}">Ajouter un tarif <x-lucide-plus-circle aria-hidden="true"/></a>@endcan
</div>

<div class="fares-panel">
    <form class="fares-filters" method="GET" action="{{ route('fares.index',$line) }}">
        <select name="status" aria-label="Statut"><option value="">Tous les statuts</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ['active'=>'Actif','inactive'=>'Inactif'][$status] }}</option>@endforeach</select>
        <button class="btn" type="submit">Filtrer</button>
    </form>
    <div class="fares-table-wrap">
        <table class="fares-table">
            <thead><tr><th>Montant</th><th>Classe</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($fares as $fare)
                <tr>
                    <td class="fare-amount">{{ number_format($fare->amount, 2, ',', ' ').' €' }}</td>
                    <td class="fare-class">{{ $fare->fare_class ?: '—' }}</td>
                    <td>
                        @if($fare->isActive())
                            <span class="fare-status active"><x-lucide-check-circle class="w-3 h-3" /> Actif</span>
                        @else
                            <span class="fare-status inactive"><x-lucide-minus-circle class="w-3 h-3" /> Inactif</span>
                        @endif
                    </td>
                    <td>
                        @can('update', $fare)<a class="btn-action secondary" href="{{ route('fares.edit',$line,$fare) }}">Modifier <x-lucide-pencil aria-hidden="true"/></a>@endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-row">Aucun tarif enregistré pour cette ligne.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="fares-pagination">{{ $fares->links('pagination.pnmg') }}</div>
</div>

<div style="margin-top:24px">
    <a class="btn-action secondary" href="{{ route('lines.show',$line) }}">Retour à la ligne <x-lucide-arrow-left aria-hidden="true"/></a>
</div>
@endsection
