@extends('layouts.admin')
@section('title', 'Personnel PNMG - Administration')
@section('content')
@include('employees._styles')
<div class="employee-head"><div><h1>Personnel PNMG</h1><p>Comptes internes de la plateforme, séparés des employés des compagnies.</p></div><a class="btn" href="{{ route('platform-staff.create') }}">Nouvel employé</a></div>
<div class="employee-card"><div class="employee-card-body"><div class="employee-table-wrap"><table class="employee-table"><thead><tr><th>Employé</th><th>Email</th><th>Rôle PNMG</th><th>État</th><th></th></tr></thead><tbody>@forelse($staff as $member)<tr><td><strong>{{ $member->name }}</strong></td><td>{{ $member->email }}</td><td>{{ $member->platformRole?->name ?? 'Rôle manquant' }}</td><td><span class="employee-status {{ $member->status }}">{{ $member->status === 'active' ? 'Actif' : 'Inactif' }}</span></td><td><a class="btn secondary" href="{{ route('platform-staff.edit',$member) }}">Modifier</a></td></tr>@empty<tr><td colspan="5">Aucun employé PNMG créé.</td></tr>@endforelse</tbody></table></div>{{ $staff->links() }}</div></div>
@endsection
