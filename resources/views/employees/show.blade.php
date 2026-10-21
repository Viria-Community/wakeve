@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', $employee->fullName().' - PNMG')
@section('content')
@include('employees._styles')

<header class="employee-head">
    <div>
        <div class="employee-breadcrumb"><a href="{{ route('employees.index') }}">Employés</a> › Fiche employé</div>
        <h1>{{ $employee->fullName() }}</h1>
        <p>{{ $employee->internal_role ?: 'Fonction non renseignée' }} · @include('employees._account-status')</p>
    </div>
    @can('update', $employee)<a class="btn" href="{{ route('employees.edit', $employee) }}">Modifier</a>@endcan
</header>

<div class="employee-detail">
    <section class="employee-card">
        <div class="employee-card-head"><h2>Rattachement professionnel</h2></div>
        <div class="employee-card-body employee-info">
            <div><small>Compagnie</small><strong>{{ $employee->company->legal_name }}</strong></div>
            <div><small>Agence</small><strong>{{ $employee->agency?->name ?: 'Siège' }}</strong></div>
            <div><small>Fonction</small><strong>{{ $employee->internal_role ?: 'Non renseignée' }}</strong></div>
        </div>
    </section>

    <section class="employee-card">
        <div class="employee-card-head"><h2>Compte et accès</h2></div>
        <div class="employee-card-body">
            @if($employee->user)
                <div class="employee-info">
                    <div><small>Email</small><strong>{{ $employee->user->email }}</strong></div>
                    <div><small>Rôle standard</small><strong>{{ ['company_manager' => 'Gestionnaire de compagnie', 'agency_manager' => 'Responsable d’agence', 'agent' => 'Agent', 'admin' => 'Administrateur'][$employee->user->role] ?? $employee->user->role }}</strong></div>
                    <div><small>État de l’employé</small>@include('employees._account-status')</div>
                </div>
            @else
                <div class="account-empty">Cette ancienne fiche doit être régularisée par la création d’un compte avant de pouvoir être considérée comme un employé de la plateforme.</div>
            @endif
        </div>
        @can('manageAccount', $employee)
            <footer class="employee-card-actions"><a class="btn" href="{{ route('employees.account.edit', $employee) }}">{{ $employee->user ? 'Gérer le rôle et l’accès' : 'Régulariser la fiche' }}</a></footer>
        @endcan
    </section>
</div>

@include('employees._permissions')
@endsection
