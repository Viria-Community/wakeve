@extends('layouts.app')
@section('title', 'Journal de mon périmètre')
@section('content')
<div class="page-title"><h1>Journal d’activité</h1><p>{{ $agency?->name ?? auth()->user()->company?->legal_name }} · Événements enregistrés dans votre périmètre depuis l’activation du journal d’agence.</p></div>
<form method="GET" class="panel"><div class="grid"><div><label for="date">Date</label><input type="date" id="date" name="date" value="{{ request('date') }}"></div><div><label for="action">Action</label><select id="action" name="action"><option value="">Toutes les actions</option>@foreach($actions as $action)<option @selected(request('action')===$action)>{{ $action }}</option>@endforeach</select></div></div><button class="btn" style="margin-top:16px">Filtrer</button></form>
<div class="dashboard-card" style="overflow-x:auto;margin-top:20px"><table><thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Élément</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at->format('d/m/Y H:i') }}</td><td>{{ $log->user?->name ?: 'Système' }}</td><td>{{ $log->description }}</td><td>{{ $log->subject_label }}</td></tr>@empty<tr><td colspan="4">Aucune activité dans cette sélection.</td></tr>@endforelse</tbody></table></div>{{ $logs->links() }}
@endsection
