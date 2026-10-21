@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Modifier une agence - PNMG')
@section('content')
@include('agencies._styles')
<div class="agency-page-head"><div><div class="agency-breadcrumb"><a href="{{ route('agencies.index') }}">Agences</a> › <a href="{{ route('agencies.show',$agency) }}">{{ $agency->name }}</a> › Modification</div><h1>Modifier l’agence</h1><p>La désactivation conserve les données et la traçabilité.</p></div></div>
<form class="agency-form-card" method="POST" action="{{ route('agencies.update',$agency) }}">@csrf @method('PUT') @include('agencies._form', ['submitLabel' => 'Enregistrer les modifications'])</form>
@endsection
