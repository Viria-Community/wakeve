@extends('layouts.app')
@section('title', 'Documents autorisés - PNMG')
@section('content')
<h1>Documents autorisés</h1>
<p>Consultez les documents depuis les modules auxquels vous avez accès.</p>
@if(auth()->user()->permits('companies.view'))<p><a class="btn" href="{{ route('documents.companies.show', auth()->user()->company_id) }}">Documents de ma compagnie</a></p>@endif
@if(auth()->user()->permits('fleet.view'))<p><a class="btn" href="{{ route('fleet-units.index') }}">Flotte et documents associés</a></p>@endif
@endsection
