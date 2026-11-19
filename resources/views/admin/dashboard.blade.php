@extends('layouts.admin')
@section('body-class', 'pnmg-dash')
@section('title','Tableau de bord administrateur - PNMG')
@push('styles')@include('layouts._dashboard-design')@endpush
@section('content')
<div class="pnmg-admin-diagrammes">
@php
    $serieC = $registrationSeries ?? [];
    $maxC = max(1, collect($serieC)->max('n'));
    $totalC = max(1, (int) ($metrics['total_companies'] ?? 0));
    $cerc = 2 * M_PI * 58;
    $parts = [
        ['p' => ($metrics['active_companies'] ?? 0) / $totalC, 'c' => '#087A5A', 'centre' => round((($metrics['active_companies'] ?? 0) / $totalC) * 100) . ' %', 'nom' => 'Compagnies actives', 'det' => ($metrics['active_companies'] ?? 0) . ' sur ' . ($metrics['total_companies'] ?? 0)],
        ['p' => ($metrics['pending_companies'] ?? 0) / $totalC, 'c' => '#E9A23B', 'centre' => (string) ($metrics['pending_companies'] ?? 0), 'nom' => 'Demandes en attente', 'det' => 'à examiner'],
        ['p' => ($metrics['suspended_companies'] ?? 0) / $totalC, 'c' => '#B42318', 'centre' => (string) ($metrics['suspended_companies'] ?? 0), 'nom' => 'Compagnies suspendues', 'det' => ($metrics['rejected_companies'] ?? 0) . ' refusée(s)'],
    ];
@endphp
    <section class="pnmg-cadre">
        <h2>Répartition des compagnies</h2>
        <p class="pnmg-sous">Part des compagnies par statut</p>
        <div class="pnmg-anneaux">
            @php($cerc = 2 * M_PI * 58)
            @php($tot = max(1, (int) ($metrics['total_companies'] ?? 0)))
            @foreach([
                ['nom' => 'Compagnies actives', 'v' => $metrics['active_companies'] ?? 0, 'c' => '#087A5A', 'det' => ($metrics['active_companies'] ?? 0) . ' sur ' . ($metrics['total_companies'] ?? 0)],
                ['nom' => 'Demandes en attente', 'v' => $metrics['pending_companies'] ?? 0, 'c' => '#E9A23B', 'det' => 'à examiner'],
                ['nom' => 'Compagnies suspendues', 'v' => $metrics['suspended_companies'] ?? 0, 'c' => '#B42318', 'det' => ($metrics['rejected_companies'] ?? 0) . ' refusée(s)'],
            ] as $it)
                <div class="pnmg-anneau">
                    <svg viewBox="0 0 160 160" role="img" aria-label="{{ $it['nom'] }}">
                        <circle cx="80" cy="80" r="58" fill="none" stroke="#D5EBE1" stroke-width="16"/>
                        <circle cx="80" cy="80" r="58" fill="none" stroke="{{ $it['c'] }}" stroke-width="16" stroke-linecap="round" transform="rotate(-90 80 80)" stroke-dasharray="{{ round($cerc * min(1, $it['v'] / $tot), 2) }} {{ round($cerc, 2) }}"/>
                        <text x="80" y="88" text-anchor="middle" font-size="24" font-weight="800" fill="#111827">{{ $it['v'] }}</text>
                    </svg>
                    <div class="pnmg-anneau-nom">{{ $it['nom'] }}</div>
                    <div class="pnmg-anneau-detail">{{ $it['det'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="pnmg-cadre">
        <h2>Nouvelles demandes · 30 derniers jours</h2>
        <p class="pnmg-sous">Une colonne par jour : compagnies déposées</p>
        <svg class="pnmg-histo" viewBox="0 0 640 240" preserveAspectRatio="none" role="img" aria-label="Demandes par jour">
            <defs><linearGradient id="pnmg-grad-hist" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#5CFFB0"/><stop offset="100%" stop-color="#087A5A"/></linearGradient></defs>
            @foreach([0, 0.5, 1] as $palier)
                <line x1="40" x2="630" y1="{{ 215 - $palier * 195 }}" y2="{{ 215 - $palier * 195 }}" stroke="#D5EBE1"/>
                <text x="32" y="{{ 219 - $palier * 195 }}" text-anchor="end">{{ round($maxC * $palier) }}</text>
            @endforeach
            @php($larg = 590 / max(1, count($serieC)))
            @foreach($serieC as $i => $jour)
                @php($h = $jour['n'] > 0 ? max(6, ($jour['n'] / $maxC) * 195) : 3)
                <rect x="{{ round(40 + $i * $larg + 2, 1) }}" y="{{ round(215 - $h, 1) }}" width="{{ round(max(4, $larg - 5), 1) }}" height="{{ round($h, 1) }}" rx="4" fill="{{ $jour['n'] > 0 ? 'url(#pnmg-grad-hist)' : '#D5EBE1' }}"><title>{{ $jour['date'] }} : {{ $jour['n'] }}</title></rect>
                @if($i % 5 === 0)<text x="{{ round(40 + $i * $larg + $larg / 2, 1) }}" y="234" text-anchor="middle">{{ $jour['date'] }}</text>@endif
            @endforeach
        </svg>
    </section>
</div>
<style>
.pnmg-admin-diagrammes{margin-bottom:6px}
.pnmg-cadre{background:#FFFFFF;border:1px solid rgba(8,122,90,.16);border-radius:20px;padding:22px 24px;box-shadow:0 14px 34px rgba(16,46,41,.08);margin-bottom:18px;position:relative;overflow:hidden}
.pnmg-cadre::before{content:"";position:absolute;left:0;top:0;right:0;height:4px;background:linear-gradient(90deg,#087A5A,#7FD8B2,#E9C46A)}
.pnmg-cadre h2{font-size:18px;color:#111827;margin:0 0 4px;font-weight:800}
.pnmg-sous{font-size:13px;color:#111827;margin:0 0 14px}
.pnmg-anneaux{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.pnmg-anneau{text-align:center}
.pnmg-anneau svg{width:160px;height:160px;overflow:visible}
.pnmg-anneau svg text{fill:#111827}
.pnmg-anneau-nom{font-size:14px;font-weight:800;color:#111827;margin-top:6px}
.pnmg-anneau-detail{font-size:12px;color:#111827}
.pnmg-histo{width:100%;height:250px;display:block}
.pnmg-histo text{font-size:11px;fill:#111827}
@media (max-width:900px){.pnmg-anneaux{grid-template-columns:1fr}}
</style>
<div class="dashboard-page"><div class="dashboard-hero"><div><p class="dashboard-eyebrow">Supervision nationale</p><h1>Tableau de bord administrateur</h1><p>Vue consolidée des compagnies, de la conformité et de l’activité PNMG.</p></div><a class="btn" href="{{ route('companies.index',['status'=>'pending']) }}">Voir les demandes</a></div><div class="dashboard-kpis"><article class="dashboard-kpi"><p class="dashboard-kpi-label">Compagnies</p><strong class="dashboard-kpi-value">{{ $metrics['total_companies'] }}</strong><p class="dashboard-kpi-note">enregistrées</p></article><article class="dashboard-kpi"><p class="dashboard-kpi-label">En attente</p><strong class="dashboard-kpi-value">{{ $metrics['pending_companies'] }}</strong><p class="dashboard-kpi-note">dossiers à examiner</p></article><article class="dashboard-kpi"><p class="dashboard-kpi-label">Agences</p><strong class="dashboard-kpi-value">{{ $metrics['total_agencies'] }}</strong><p class="dashboard-kpi-note">sur le territoire</p></article><article class="dashboard-kpi"><p class="dashboard-kpi-label">Flotte totale</p><strong class="dashboard-kpi-value">{{ $metrics['total_fleet_units'] }}</strong><p class="dashboard-kpi-note">unités enregistrées</p></article></div><div class="dashboard-diagrams"><section class="dashboard-chart-card"><h2>Répartition des compagnies</h2><p>Statut des compagnies dans le référentiel</p><div class="dashboard-donut-row"><div class="dashboard-donut" style="background:conic-gradient(var(--green) 0 {{ $metrics['total_companies'] ? round($metrics['active_companies']/$metrics['total_companies']*100) : 0 }}%, var(--gold) 0 100%);"></div><div class="dashboard-legend"><span><span><i></i>Actives</span><strong>{{ $metrics['active_companies'] }}</strong></span><span class="gold"><span><i></i>En attente</span><strong>{{ $metrics['pending_companies'] }}</strong></span><span class="orange"><span><i></i>Suspendues</span><strong>{{ $metrics['suspended_companies'] }}</strong></span><span class="red"><span><i></i>Refusées</span><strong>{{ $metrics['rejected_companies'] }}</strong></span></div></div></section><section class="dashboard-chart-card"><h2>Suivi documentaire</h2><p>Alertes de conformité à traiter</p><div class="dashboard-bars"><div class="dashboard-bar-line"><span>Expirés</span><div class="dashboard-bar-track"><div class="dashboard-bar-fill red" style="width:{{ min(100,$expiredDocumentsCount*10) }}%"></div></div><strong>{{ $expiredDocumentsCount }}</strong></div><div class="dashboard-bar-line"><span>À renouveler</span><div class="dashboard-bar-track"><div class="dashboard-bar-fill gold" style="width:{{ min(100,$expiringDocumentsCount*10) }}%"></div></div><strong>{{ $expiringDocumentsCount }}</strong></div><div class="dashboard-bar-line"><span>Ajoutés aujourd’hui</span><div class="dashboard-bar-track"><div class="dashboard-bar-fill" style="width:{{ min(100,$documentsAddedToday*10) }}%"></div></div><strong>{{ $documentsAddedToday }}</strong></div></div></section></div><div class="dashboard-layout"><section class="dashboard-card"><div class="dashboard-card-head"><div><h2>Dernières demandes</h2><p>Les compagnies récemment créées</p></div><a href="{{ route('companies.index') }}">Voir tout →</a></div><div class="dashboard-table-wrap">@if($recentCompanies->isEmpty())<p class="dashboard-empty">Aucune compagnie enregistrée.</p>@else<table><thead><tr><th>Compagnie</th><th>Mode</th><th>Date</th><th>Statut</th><th></th></tr></thead><tbody>@foreach($recentCompanies as $company)<tr><td><strong>{{ $company->legal_name }}</strong></td><td>{{ $company->transportModes->first()?->label ?: '—' }}</td><td>{{ $company->created_at->format('d/m/Y') }}</td><td><span class="badge status-{{ $company->status->value }}">{{ ['pending'=>'En attente','validated'=>'Validée','rejected'=>'Refusée','active'=>'Active','suspended'=>'Suspendue'][$company->status->value] }}</span></td><td><a class="dashboard-link" href="{{ route('companies.show',$company) }}">Examiner</a></td></tr>@endforeach</tbody></table>@endif</div></section><aside class="dashboard-stack"><section class="dashboard-card"><div class="dashboard-card-head"><h2>Alertes conformité</h2><a href="{{ route('documents.index') }}">Ouvrir →</a></div><div class="dashboard-card-body"><div class="dashboard-alert dashboard-alert-danger"><strong>{{ $expiredDocumentsCount }}</strong> document(s) expiré(s)</div><div class="dashboard-alert dashboard-alert-warning"><strong>{{ $expiringDocumentsCount }}</strong> échéance(s) sous 30 jours</div><div class="dashboard-alert dashboard-alert-info"><strong>{{ $documentsAddedToday }}</strong> document(s) ajouté(s) aujourd’hui</div></div></section><section class="dashboard-card"><div class="dashboard-card-head"><h2>Répartition</h2></div><div class="dashboard-card-body dashboard-distribution"><a href="{{ route('companies.index',['status'=>'active']) }}"><span>Actives</span><strong>{{ $metrics['active_companies'] }}</strong></a><a href="{{ route('companies.index',['status'=>'suspended']) }}"><span>Suspendues</span><strong>{{ $metrics['suspended_companies'] }}</strong></a><a href="{{ route('companies.index',['status'=>'rejected']) }}"><span>Refusées</span><strong>{{ $metrics['rejected_companies'] }}</strong></a></div></section><section class="dashboard-card"><div class="dashboard-card-head"><h2>Actions rapides</h2></div><div class="dashboard-card-body dashboard-actions"><a class="btn" href="{{ route('companies.index',['status'=>'pending']) }}">Demandes à valider</a><a class="btn secondary" href="{{ route('documents.index') }}">Contrôle documentaire</a></div></section></aside></div><section class="dashboard-card"><div class="dashboard-card-head"><div><h2>Journal d’activité</h2><p>Dernières opérations effectuées</p></div><a href="{{ route('audit-logs.index') }}">Voir tout →</a></div><div class="dashboard-table-wrap">@if($recentAuditLogs->isEmpty())<p class="dashboard-empty">Aucune activité enregistrée.</p>@else<table><thead><tr><th>Date</th><th>Utilisateur</th><th>Compagnie</th><th>Action</th><th>Élément</th></tr></thead><tbody>@foreach($recentAuditLogs as $log)<tr><td>{{ $log->created_at->format('d/m/Y H:i') }}</td><td><strong>{{ $log->user?->name ?: 'Système / visiteur' }}</strong></td><td>{{ $log->company?->legal_name ?: 'Administration PNMG' }}</td><td>{{ ucfirst($log->action) }}</td><td>{{ $log->subject_label ?: 'Session utilisateur' }}</td></tr>@endforeach</tbody></table>@endif</div></section></div>
@endsection
