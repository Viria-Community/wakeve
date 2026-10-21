@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Employés - PNMG')
@section('content')
@include('employees._styles')
@php($isAdmin = auth()->user()->isAdmin())

<div class="employee-head">
    <div>
        <div class="employee-breadcrumb">Plateforme › Gestion › Employés</div>
        <h1>Employés</h1>
        <p>Chaque employé possède un compte de connexion et un rôle sur la plateforme.</p>
    </div>
    @can('create', App\Models\Employee::class)<a class="btn" href="{{ route('employees.create') }}">Nouvel employé</a>@endcan
</div>

<div class="employee-summary">
    <div class="employee-stat"><span>Total</span><strong>{{ $employeeSummary->total }}</strong><small>Fiches avec compte</small></div>
    <div class="employee-stat success"><span>Actifs</span><strong>{{ $employeeSummary->active }}</strong><small>Accès autorisé</small></div>
    <div class="employee-stat account"><span>Désactivés</span><strong>{{ $employeeSummary->inactive }}</strong><small>Accès suspendu</small></div>
</div>

<div class="employee-card">
    <div class="employee-card-head directory-title">
        <div><h2>Répertoire des employés</h2><p>{{ $employees->total() }} résultat(s)</p></div>
    </div>
    <form class="employee-filters" method="GET" action="{{ route('employees.index') }}">
        <div>
            <label for="search">Rechercher</label>
            <input id="search" name="search" value="{{ request('search') }}" placeholder="Nom, email ou fonction">
        </div>
        <div>
            <label for="status">État du compte</label>
            <select id="status" name="status">
                <option value="">Tous les états</option>
                <option value="active" @selected(request('status') === 'active')>Actif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Désactivé</option>
            </select>
        </div>
        <div class="employee-filter-actions">
            <button class="btn" type="submit">Appliquer</button>
            @if(request()->hasAny(['search', 'status']))<a href="{{ route('employees.index') }}">Réinitialiser</a>@endif
        </div>
    </form>
    <div class="employee-table-wrap">
        <table class="employee-table">
            <thead><tr><th>Employé</th>@if($isAdmin)<th>Compagnie</th>@endif<th>Rattachement</th><th>Fonction</th><th>Rôle</th><th>État</th><th></th></tr></thead>
            <tbody>
            @forelse($employees as $employee)
                <tr>
                    <td><div class="person"><span class="avatar">{{ mb_strtoupper(mb_substr($employee->first_name, 0, 1).mb_substr($employee->last_name, 0, 1)) }}</span><span><strong>{{ $employee->fullName() }}</strong><small>{{ $employee->user->email }}</small></span></div></td>
                    @if($isAdmin)<td>{{ $employee->company->legal_name }}</td>@endif
                    <td><strong>{{ $employee->agency?->name ?: 'Siège de la compagnie' }}</strong></td>
                    <td>{{ $employee->internal_role ?: 'Non renseignée' }}</td>
                    <td>{{ ['company_manager' => 'Gestionnaire', 'agency_manager' => 'Responsable d’agence', 'agent' => 'Agent'][$employee->user->role] ?? $employee->user->role }}</td>
                    <td>@include('employees._account-status')</td>
                    <td><a class="employee-link" href="{{ route('employees.show', $employee) }}">Consulter</a></td>
                </tr>
            @empty
                <tr><td colspan="{{ $isAdmin ? 7 : 6 }}"><div class="employee-empty"><strong>Aucun employé trouvé</strong><span>Modifiez les filtres ou ajoutez votre premier employé.</span></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="employee-pagination">{{ $employees->links() }}</div>
</div>

@push('styles')
<style>
.employee-summary{grid-template-columns:repeat(3,1fr)}.employee-stat{position:relative;padding-top:34px}.employee-stat>span{position:absolute;top:16px;left:19px;font-size:11px;font-weight:800;text-transform:uppercase}.employee-stat small{display:block;margin-top:5px;color:#7b8794;font-size:11px}.employee-stat.success{border-top:3px solid var(--green)}.employee-stat.account{border-top:3px solid var(--gold)}.directory-title p{margin:4px 0 0;color:#64748b;font-size:12px}.employee-filters{align-items:end;grid-template-columns:2fr 1fr auto}.employee-filters label{display:block;margin-bottom:6px;color:#475569;font-size:11px;font-weight:800}.employee-filter-actions{display:flex;align-items:center;gap:11px}.employee-filter-actions a{color:#64748b;font-size:12px;font-weight:700;text-decoration:none}.employee-empty{padding:32px;text-align:center}.employee-empty strong,.employee-empty span{display:block}.employee-empty span{margin-top:6px;color:#64748b;font-size:13px}@media(max-width:900px){.employee-filters{grid-template-columns:1fr 1fr}}@media(max-width:650px){.employee-summary,.employee-filters{grid-template-columns:1fr}.employee-filter-actions{align-items:stretch;flex-direction:column}.employee-head .btn{width:100%}}
</style>
@endpush
@endsection
