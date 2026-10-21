@extends('layouts.admin')
@section('title', 'Mon espace PNMG')
@section('content')
<div class="page-head"><div><h1>Mon espace PNMG</h1><p>{{ auth()->user()->platformRole?->name }}</p></div></div>
<div class="stats-grid">@if(auth()->user()->permitsPlatform('companies.view'))<a class="stat-card" href="{{ route('companies.index') }}"><span>Compagnies en attente</span><strong>{{ $pendingCompanies }}</strong><small>{{ $activeCompanies }} compagnie(s) active(s)</small></a>@endif @if(auth()->user()->permitsPlatform('documents.view'))<a class="stat-card" href="{{ route('documents.index') }}"><span>Documents à contrôler</span><strong>{{ $pendingDocuments }}</strong><small>Ouvrir le contrôle documentaire</small></a>@endif</div>
@push('styles')<style>.page-head{margin-bottom:24px}.page-head h1{margin:0 0 7px}.page-head p{margin:0;color:var(--muted)}.stats-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.stat-card{display:block;padding:24px;border:1px solid var(--border);border-radius:13px;background:#fff;color:inherit;text-decoration:none}.stat-card:hover{border-color:var(--green)}.stat-card span,.stat-card small{display:block;color:var(--muted)}.stat-card strong{display:block;margin:7px 0;font-size:36px;color:var(--dark)}@media(max-width:700px){.stats-grid{grid-template-columns:1fr}}</style>@endpush
@endsection
