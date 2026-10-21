@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Nouvel employé - PNMG')
@section('content')
@include('employees._styles')
<div class="employee-head"><div><div class="employee-breadcrumb"><a href="{{ route('employees.index') }}">Employés</a> › Nouvel employé</div><h1>Nouvel employé</h1><p>Créer un employé avec son compte de connexion et son rôle.</p></div></div>
<form class="employee-form" method="POST" action="{{ route('employees.store') }}">@csrf @include('employees._form', ['submitLabel'=>'Créer l’employé'])</form>
@endsection
