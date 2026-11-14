@extends(auth()->user()->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Lignes - PNMG')

@push('styles')
<style>
    .lines-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:28px}.lines-head h1{margin:4px 0 6px;font-size:32px}.breadcrumb{font-size:12px;color:#64748b}.line-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.line-filters{display:grid;grid-template-columns:2fr repeat(3,1fr) auto;gap:10px;padding:18px;border-bottom:1px solid #e2e8f0}.line-filters input,.line-filters select{min-width:0;padding:11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.line-table-wrap{overflow-x:auto}.line-table{width:100%;border-collapse:collapse}.line-table th,.line-table td{padding:15px 18px;text-align:left;border-bottom:1px solid #e2e8f0}.line-table th{font-size:11px;color:#64748b;text-transform:uppercase;background:#f8fafc}.line-name{display:flex;align-items:center;gap:12px;min-width:210px}.line-name strong{display:block}.line-name small{display:block;color:#64748b;margin-top:3px}.line-pagination{padding:18px}.empty-row{text-align:center;color:#64748b;padding:35px!important}.status-label{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:99px;font-size:12px;font-weight:700}.status-label:before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}.status-active{background:#dcfce7;color:#166534}.status-inactive{background:#e5e7eb;color:#374151}.view-link{color:var(--green);font-weight:700;text-decoration:none;white-space:nowrap}
</style>
@endpush

@section('content')
<div class="lines-head">
    <div>
        <div class="breadcrumb">Offre de transport › Lignes</div>
        <h1>Lignes de transport</h1>
        <div class="muted">Répertoire des lignes déclarées par les compagnies.</div>
    </div>
    @can('create', App\Models\Line::class)<a class="btn" href="{{ route('lines.create') }}">Nouvelle ligne</a>@endcan
</div>

<div class="line-panel">
    <form class="line-filters" method="GET" action="{{ route('lines.index') }}">
        <input name="search" value="{{ request('search') }}" placeholder="Libellé ou lieu" aria-label="Rechercher">
        <select name="status" aria-label="Statut"><option value="">Tous les statuts</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ['active'=>'Active','inactive'=>'Inactive'][$status] }}</option>@endforeach</select>
        <select name="transport_mode_id" aria-label="Mode de transport"><option value="">Tous les modes</option>@foreach($transportModes as $mode)<option value="{{ $mode->id }}" @selected((string)request('transport_mode_id')===(string)$mode->id)>{{ $mode->label }}</option>@endforeach</select>
        @if(auth()->user()->isAdmin())<select name="company_id" aria-label="Compagnie"><option value="">Toutes les compagnies</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string)request('company_id')===(string)$company->id)>{{ $company->legal_name }}</option>@endforeach</select>@endif
        <button class="btn" type="submit">Filtrer</button>
    </form>
    <div class="line-table-wrap">
        <table class="line-table">
            <thead><tr><th>Libellé</th><th>Compagnie</th><th>Origine</th><th>Destination</th><th>Mode</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($lines as $line)
                <tr>
                    <td><div class="line-name"><span class="line-logo" style="width:40px;height:40px;border-radius:9px;background:#E8F5F0;color:var(--green);display:grid;place-items:center;font-weight:800;">{{ mb_strtoupper(mb_substr($line->label, 0, 2)) }}</span><span><strong>{{ $line->label }}</strong><small>{{ $line->company->legal_name }}</small></span></div></td>
                    <td>{{ $line->company->legal_name }}</td>
                    <td>{{ $line->originPlace->name ?? '—' }}</td>
                    <td>{{ $line->destinationPlace->name ?? '—' }}</td>
                    <td>{{ $line->transportMode->label ?? '—' }}</td>
                    <td><span class="status-label status-{{ $line->status }}">{{ ['active'=>'Active','inactive'=>'Inactive'][$line->status] }}</span></td>
                    <td><div style="display:flex;gap:8px"><a class="view-link" href="{{ route('lines.show',$line) }}">Consulter <x-lucide-arrow-right aria-hidden="true"/></a><a class="view-link" href="{{ route('fares.index', $line) }}">Tarifs</a></div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Aucune ligne ne correspond aux critères.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="line-pagination">{{ $lines->links('pagination.pnmg') }}</div>
</div>
@endsection
