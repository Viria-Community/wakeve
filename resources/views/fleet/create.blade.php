@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Nouvelle unité de flotte - PNMG')
@section('content') @include('fleet._styles') <div class="fleet-head"><div><div class="fleet-breadcrumb"><a href="{{ route('fleet-units.index') }}">Flotte nationale</a> › Nouvelle unité</div><h1>Nouvelle unité de flotte</h1><p>Ajouter un véhicule ou une embarcation au registre.</p></div></div><form class="fleet-form" method="POST" action="{{ route('fleet-units.store') }}">@csrf @include('fleet._form',['submitLabel'=>'Ajouter l’unité'])</form>@endsection
