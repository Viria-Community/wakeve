@extends('layouts.admin')
@section('title','Journal d’activité - PNMG')
@section('content')
<div class="page-title"><h1>Journal d’activité</h1><p>Traçabilité des connexions et des opérations réalisées sur la plateforme.</p></div>
<section class="dashboard-card audit-card">
<form class="audit-filters" method="GET">
    <div><label for="action">Type d’activité</label><select id="action" name="action"><option value="">Toutes les activités</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('action')===$action)>{{ ucfirst($action) }}</option>@endforeach</select></div>
    <div><label for="user_id">Utilisateur</label><select id="user_id" name="user_id"><option value="">Tous les utilisateurs</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)request('user_id')===(string)$user->id)>{{ $user->name }}</option>@endforeach</select></div>
    <div><label for="company_id">Compagnie</label><select id="company_id" name="company_id"><option value="">Toutes les compagnies</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string)request('company_id')===(string)$company->id)>{{ $company->legal_name }}</option>@endforeach</select></div>
    <div><label for="date">Date</label><input id="date" type="date" name="date" value="{{ request('date') }}"></div>
    <button class="btn" type="submit">Appliquer</button>
    @if(request()->hasAny(['action','user_id','company_id','date']))<a class="reset" href="{{ route('audit-logs.index') }}">Réinitialiser</a>@endif
</form>
<div class="audit-table"><table><thead><tr><th>Date et heure</th><th>Utilisateur</th><th>Compagnie</th><th>Action</th><th>Détail</th><th>Élément concerné</th><th>Adresse IP</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td><strong>{{ $log->created_at->format('d/m/Y') }}</strong><small>{{ $log->created_at->format('H:i:s') }}</small></td><td><strong>{{ $log->user?->name ?: 'Système / visiteur' }}</strong><small>{{ $log->user?->email }}</small></td><td>@if($log->company && !$log->company->trashed())<a class="company-link" href="{{ route('companies.show',$log->company) }}">{{ $log->company->legal_name }}</a>@else<span class="muted">{{ $log->company?->legal_name ?: 'Administration PNMG' }}</span>@endif</td><td><span class="audit-action">{{ ucfirst($log->action) }}</span></td><td>{{ $log->description }}@if($log->changed_fields)<small>Champs modifiés : {{ implode(', ', $log->changed_fields) }}</small>@endif</td><td>{{ $log->subject_label ?: 'Session utilisateur' }}</td><td>{{ $log->ip_address ?: '—' }}</td></tr>
@empty<tr><td colspan="7" class="empty">Aucune activité ne correspond aux filtres.</td></tr>@endforelse
</tbody></table></div><div class="audit-pagination">{{ $logs->links() }}</div>
</section>
@push('styles')<style>.audit-card{margin-top:25px;overflow:hidden}.audit-filters{display:grid;grid-template-columns:1fr 1.3fr 1fr auto auto;align-items:end;gap:10px;padding:18px;border-bottom:1px solid var(--border)}.audit-filters label{font-size:11px;font-weight:800}.audit-filters .reset{padding:11px;color:var(--muted);font-size:12px;font-weight:700;text-decoration:none}.audit-table{overflow-x:auto}.audit-table table{min-width:920px}.audit-table td small{display:block;margin-top:4px;color:var(--muted);font-size:11px}.audit-action{display:inline-flex;padding:5px 8px;border-radius:99px;background:var(--pale);color:var(--dark);font-size:11px;font-weight:800}.audit-pagination{padding:18px}@media(max-width:850px){.audit-filters{grid-template-columns:1fr 1fr}}@media(max-width:550px){.audit-filters{grid-template-columns:1fr}.audit-filters .btn{width:100%}}</style>@endpush
@push('styles')<style>.company-link{color:var(--green);font-weight:800;text-decoration:none}</style>@endpush
@endsection
