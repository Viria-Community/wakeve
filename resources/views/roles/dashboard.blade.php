@extends('layouts.app')
@section('title', 'Mon espace - PNMG')
@section('content')
<h1>Bienvenue, {{ auth()->user()->name }}</h1>
<p>Votre rôle : <strong>{{ auth()->user()->customRole?->name ?? 'Accès restreint' }}</strong>.</p>
<p>Le menu présente les modules autorisés pour votre compte.</p>
@endsection
