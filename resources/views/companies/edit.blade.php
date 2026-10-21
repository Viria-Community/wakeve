@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Modifier la compagnie - PNMG')
@section('content')
<div class="company-form-head"><div><div class="form-breadcrumb"><a href="{{ route('companies.index') }}">Compagnies</a> › <a href="{{ route('companies.show', $company) }}">{{ $company->legal_name }}</a> › Modification</div><h1>Modifier la compagnie</h1><p>Mettez à jour les informations de {{ $company->legal_name }}. Le statut reste géré séparément.</p></div></div>
<form class="company-form-card" method="POST" action="{{ route('companies.update',$company) }}">@csrf @method('PUT') @include('companies._form', ['submitLabel' => 'Enregistrer les modifications'])</form>
@endsection
