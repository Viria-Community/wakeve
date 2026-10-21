@extends(auth()->user()->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Compagnies - PNMG')

@push('styles')
<style>
    .muted{color:#64748b}.view-link{display:inline-flex;align-items:center;gap:5px}.view-link svg{width:15px;height:15px;stroke-width:2}
    .companies-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:28px}.companies-head h1{margin:4px 0 6px;font-size:32px}.breadcrumb{font-size:12px;color:#64748b}.company-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;margin-bottom:24px}.summary-card{padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12}.summary-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:9px;background:#E8F5F0;color:var(--green);font-weight:800}.summary-card.approved .summary-icon{background:#dcfce7;color:#15803d}.summary-card.pending .summary-icon{background:#fef3c7;color:#b45309}.summary-card.restricted .summary-icon{background:#fee2e2;color:#b91c1c}.summary-label{margin:15px 0 3px;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase}.summary-value{font-size:30px;font-weight:800}.company-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.company-filters{display:grid;grid-template-columns:2fr repeat(3,1fr) auto;gap:10px;padding:18px;border-bottom:1px solid #e2e8f0}.company-filters input,.company-filters select{min-width:0;padding:11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.company-table-wrap{overflow-x:auto}.company-table{width:100%;border-collapse:collapse}.company-table th,.company-table td{padding:15px 18px;text-align:left;border-bottom:1px solid #e2e8f0}.company-table th{font-size:11px;color:#64748b;text-transform:uppercase;background:#f8fafc}.company-name{display:flex;align-items:center;gap:12px;min-width:210px}.company-logo{width:40px;height:40px;border-radius:9px;background:#E8F5F0;color:var(--green);display:grid;place-items:center;font-weight:800}.company-name strong{display:block}.company-name small{display:block;color:#64748b;margin-top:3px}.mode-list{display:flex;flex-wrap:wrap;gap:5px}.mode-pill{padding:4px 7px;border-radius:6px;background:#f1f5f9;font-size:12px}.company-pagination{padding:18px}.empty-row{text-align:center;color:#64748b;padding:35px!important}.status-label{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:99px;font-size:12px;font-weight:700}.status-label:before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}.status-pending{background:#fef3c7;color:#92400e}.status-active,.status-validated{background:#dcfce7;color:#166534}.status-suspended,.status-rejected{background:#fee2e2;color:#991b1b}.view-link{color:var(--green);font-weight:700;text-decoration:none;white-space:nowrap}
    .company-summary{gap:16px}.summary-card{padding:19px}.summary-card.approved{border-top:3px solid var(--green)}.summary-card.pending{border-top:3px solid var(--gold)}.summary-card.restricted{border-top:3px solid var(--danger)}.summary-label{margin:0 0 3px;color:#475569;font-size:11px;font-weight:800}.summary-value{font-size:29px}.summary-note{display:block;margin-top:5px;color:#7b8794;font-size:11px}.summary-icon{display:none}
    @media(max-width:1050px){.company-summary{grid-template-columns:repeat(2,1fr)}.company-filters{grid-template-columns:repeat(2,1fr)}.company-filters .btn{width:100%}}@media(max-width:650px){.companies-head{align-items:flex-start;flex-direction:column}.company-summary,.company-filters{grid-template-columns:1fr}.companies-head .btn{width:100%;text-align:center}}
</style>
@endpush

@section('content')
<div class="companies-head">
    <div>
        @if(auth()->user()->isPlatformMember())<div class="breadcrumb">Plateforme › Compagnies de transport</div>@endif
        <h1>Compagnies de transport</h1>
        <div class="muted">Répertoire central des opérateurs enregistrés au Gabon.</div>
    </div>
    @can('create', App\Models\Company::class)<a class="btn" href="{{ route('companies.create') }}">Nouvelle compagnie</a>@endcan
</div>

@if($companySummary)
<div class="company-summary">
    <div class="summary-card"><div class="summary-label">Total compagnies</div><div class="summary-value">{{ $companySummary->total }}</div><small class="summary-note">Dans le registre national</small></div>
    <div class="summary-card approved"><div class="summary-label">Validées / actives</div><div class="summary-value">{{ $companySummary->approved }}</div><small class="summary-note">Compagnies autorisées</small></div>
    <div class="summary-card pending"><div class="summary-label">En attente</div><div class="summary-value">{{ $companySummary->pending }}</div><small class="summary-note">Dossiers à examiner</small></div>
    <div class="summary-card restricted"><div class="summary-label">Refusées / suspendues</div><div class="summary-value">{{ $companySummary->restricted }}</div><small class="summary-note">Activité non autorisée</small></div>
</div>
@endif

<div class="company-panel">
    <form class="company-filters" method="GET" action="{{ route('companies.index') }}">
        <input name="search" value="{{ request('search') }}" placeholder="Nom, email, RCCM ou NIF" aria-label="Rechercher">
        <select name="status" aria-label="Statut"><option value="">Tous les statuts</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ['pending'=>'En attente','validated'=>'Validée','rejected'=>'Refusée','active'=>'Active','suspended'=>'Suspendue'][$status] }}</option>@endforeach</select>
        <select name="transport_mode" aria-label="Mode de transport"><option value="">Tous les modes</option>@foreach($transportModes as $mode)<option value="{{ $mode->id }}" @selected((string)request('transport_mode')===(string)$mode->id)>{{ $mode->label }}</option>@endforeach</select>
        <input name="city" value="{{ request('city') }}" placeholder="Ville" aria-label="Ville">
        <button class="btn" type="submit">Filtrer</button>
    </form>
    <div class="company-table-wrap">
        <table class="company-table">
            <thead><tr><th>Compagnie</th><th>Modes</th><th>Siège</th><th>Statut</th><th>Agences</th><th>Flotte</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($companies as $company)
                @php($statusLabels = ['pending'=>'En attente','validated'=>'Validée','rejected'=>'Refusée','active'=>'Active','suspended'=>'Suspendue'])
                <tr>
                    <td><div class="company-name"><span class="company-logo">{{ mb_strtoupper(mb_substr($company->legal_name, 0, 2)) }}</span><span><strong>{{ $company->legal_name }}</strong><small>{{ $company->email }}</small></span></div></td>
                    <td><div class="mode-list">@forelse($company->transportModes as $mode)<span class="mode-pill">{{ $mode->label }}</span>@empty<span class="muted">—</span>@endforelse</div></td>
                    <td>{{ $company->city ?: 'Non renseignée' }}</td>
                    <td><span class="status-label status-{{ $company->status->value }}">{{ $statusLabels[$company->status->value] }}</span></td>
                    <td>{{ $company->agencies_count }}</td><td>{{ $company->fleet_units_count }}</td>
                    <td><a class="view-link" href="{{ route('companies.show',$company) }}">Consulter <x-lucide-arrow-right aria-hidden="true"/></a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Aucune compagnie ne correspond aux critères.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="company-pagination">{{ $companies->links() }}</div>
</div>
@endsection
