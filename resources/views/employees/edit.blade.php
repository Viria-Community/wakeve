@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title','Modifier un employé - PNMG')
@section('content')
@include('employees._styles')
<div class="employee-head"><div><div class="employee-breadcrumb"><a href="{{ route('employees.index') }}">Employés</a> › <a href="{{ route('employees.show',$employee) }}">{{ $employee->fullName() }}</a> › Modification</div><h1>Modifier l’employé</h1><p>Modifiez son identité, sa fonction ou son rattachement. Le rôle et l’état se gèrent depuis sa fiche.</p></div></div>
<form class="employee-form" method="POST" action="{{ route('employees.update',$employee) }}">@csrf @method('PUT') @include('employees._form', ['submitLabel'=>'Enregistrer les modifications'])</form>
@endsection
