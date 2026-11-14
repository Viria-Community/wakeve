@extends('layouts.app')
@section('title', 'Tableau de bord Offre - PNMG')

@push('styles')
<style>
    .dashboard-offre-head{margin-bottom:28px}.dashboard-offre-head h1{margin:6px 0 0;font-size:32px}.dashboard-kpis{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-bottom:24px}.kpi-card{padding:24px;background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12}.kpi-label{margin-bottom:8px;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase}.kpi-value{font-size:30px;font-weight:800}.kpi-sub{color:#64748b;font-size:13px}.dashboard-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}.dashboard-card{padding:24px;background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12}.dashboard-card h2{margin:0 0 16px;font-size:16px}.dashboard-list{list-style:none;margin:0;padding:0}.dashboard-list li{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #e2e8f0}.dashboard-list li:last-child{border-bottom:none}.dashboard-list li .item-label{font-size:14px}.dashboard-list li .item-value{font-weight:700}
    @media(max-width:760px){.dashboard-kpis{grid-template-columns:1fr}.dashboard-cards{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="dashboard-offre-head">
    <div>
        <div class="breadcrumb">Offre de transport › Tableau de bord</div>
        <h1>Tableau de bord de l’offre</h1>
        <div class="muted">Aperçu de l’offre de transport déclarée.</div>
    </div>
</div>

<div class="dashboard-kpis">
    <div class="kpi-card">
        <div class="kpi-label">Lignes</div>
        <div class="kpi-value">{{ $activeLines }}</div>
        <div class="kpi-sub">lignes actives sur {{ $totalLines }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Départs à venir</div>
        <div class="kpi-value">{{ $scheduledDepartures }}</div>
        <div class="kpi-sub">départs programmés</div>
    </div>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <h2>Aperçu des lignes</h2>
        <ul class="dashboard-list">
            <li><span class="item-label">Total lignes</span><span class="item-value">{{ $totalLines }}</span></li>
            <li><span class="item-label">Lignes actives</span><span class="item-value" style="color:var(--green)">{{ $activeLines }}</span></li>
            <li><span class="item-label">Lignes inactives</span><span class="item-value" style="color:#64748b">{{ $totalLines - $activeLines }}</span></li>
        </ul>
    </div>
    <div class="dashboard-card">
        <h2>Aperçu des départs</h2>
        <ul class="dashboard-list">
            <li><span class="item-label">Total départs</span><span class="item-value">{{ $totalDepartures }}</span></li>
            <li><span class="item-label">Départs programmés</span><span class="item-value" style="color:var(--green)">{{ $scheduledDepartures }}</span></li>
            <li><span class="item-label">Départs annulés</span><span class="item-value" style="color:#64748b">{{ $totalDepartures - $scheduledDepartures }}</span></li>
        </ul>
    </div>
</div>
@endsection
