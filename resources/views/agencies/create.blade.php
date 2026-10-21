@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Nouvelle agence - PNMG')
@section('content')
@include('agencies._styles')
<div class="agency-page-head"><div><div class="agency-breadcrumb"><a href="{{ route('agencies.index') }}">Agences</a> › Nouvelle agence</div><h1>Nouvelle agence</h1><p>Créer un point de service rattaché à une compagnie.</p></div></div>
<form class="agency-form-card" method="POST" action="{{ route('agencies.store') }}">@csrf @include('agencies._form', ['submitLabel' => 'Créer l’agence'])</form>
@endsection
