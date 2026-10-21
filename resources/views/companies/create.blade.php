@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Nouvelle compagnie - PNMG')
@section('content')
<div class="company-form-head"><div><div class="form-breadcrumb"><a href="{{ route('companies.index') }}">Compagnies</a> › Nouvelle compagnie</div><h1>Nouvelle compagnie</h1><p>Enregistrement d’un opérateur qui sera placé en attente de validation.</p></div></div>
<form class="company-form-card" method="POST" action="{{ route('companies.store') }}">@csrf @include('companies._form', ['submitLabel' => 'Enregistrer la compagnie'])</form>
@endsection
