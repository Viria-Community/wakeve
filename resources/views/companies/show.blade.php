@extends(auth()->user()->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', $company->legal_name.' - PNMG')

@push('styles')
<style>
    .company-detail-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:26px}.company-detail-head h1{margin:5px 0;font-size:32px}.detail-breadcrumb{font-size:12px;color:#64748b}.detail-actions{display:flex;gap:9px;flex-wrap:wrap}.detail-actions .btn{white-space:nowrap}.detail-status{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:99px;font-size:12px;font-weight:700}.detail-status:before{content:'';width:7px;height:7px;border-radius:50%;background:currentColor}.status-pending,.status-to_renew{background:var(--gold-pale);color:var(--warning-text)}.status-active,.status-validated,.status-valid{background:var(--pale);color:var(--green)}.status-suspended,.status-rejected,.status-expired{background:var(--danger-pale);color:var(--danger-text)}.detail-metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}.detail-metric{padding:18px;background:#fff;border:1px solid #e2e8f0;border-radius:12px}.detail-metric strong{display:block;font-size:26px}.detail-metric span{color:#64748b;font-size:12px}.detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.detail-card{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.detail-card.full{grid-column:1/-1}.detail-card-head{padding:17px 20px;border-bottom:1px solid #e2e8f0}.detail-card-head h2{margin:0;font-size:18px}.detail-card-body{padding:20px}.info-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.info-item small{display:block;margin-bottom:5px;color:#64748b;text-transform:uppercase;font-size:10px;font-weight:700}.info-item strong{font-size:14px}.history-item{position:relative;padding:0 0 18px 22px;border-left:2px solid #E8F5F0}.history-item:last-child{padding-bottom:0}.history-item:before{content:'';position:absolute;left:-6px;top:3px;width:10px;height:10px;border-radius:50%;background:var(--green)}.history-item strong{display:block}.history-item small{display:block;color:#64748b;margin-top:5px}.document-table{width:100%;border-collapse:collapse}.document-table th,.document-table td{padding:14px 20px;text-align:left;border-bottom:1px solid #e2e8f0}.document-table th{font-size:11px;text-transform:uppercase;color:#64748b;background:#f8fafc}.download-link{color:var(--green);font-weight:700;text-decoration:none}.decision-card{margin-bottom:22px;padding:20px;border:1px solid #b8ddcf;border-radius:13px;background:var(--bg)}.decision-card h2{margin:0 0 7px;font-size:19px}.decision-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:16px}.decision-option{padding:16px;border:1px solid #E8F5F0;border-radius:10px;background:#fff}.decision-option label{display:block;margin-bottom:7px;font-weight:700}.decision-option textarea{width:100%;min-height:76px;margin-bottom:10px;padding:10px;border:1px solid #cbd5e1;border-radius:8px;resize:vertical}.btn.success{background:var(--green)}.btn.danger{background:var(--danger-text)}.btn.warning{background:var(--warning);color:var(--text)}.muted{color:#64748b}.errors ul{margin:0}
    @media(max-width:900px){.detail-metrics{grid-template-columns:repeat(2,1fr)}.detail-grid,.decision-grid{grid-template-columns:1fr}}@media(max-width:620px){.company-detail-head{align-items:flex-start;flex-direction:column}.detail-actions{width:100%}.detail-actions .btn{flex:1}.detail-metrics,.info-list{grid-template-columns:1fr}.document-scroll{overflow-x:auto}}
</style>
@endpush

@section('content')
@php
    $statusLabels = ['pending'=>'En attente','validated'=>'Validée','rejected'=>'Refusée','active'=>'Active','suspended'=>'Suspendue'];
    $documentLabels = ['valid'=>'Valide','to_renew'=>'À renouveler','expired'=>'Expiré'];
    $modeLabels = ['road'=>'Routier','river'=>'Fluvial','maritime'=>'Maritime','rail'=>'Ferroviaire','air'=>'Aérien'];
    $expiredDocuments = $company->documents->where('status','expired')->count();
    $renewDocuments = $company->documents->where('status','to_renew')->count();
    $approvedDocuments = $company->documents->where('review_status','approved')->count();
    $pendingDocuments = $company->documents->filter(fn($document) => !$document->review_status || $document->review_status === 'pending')->count();
    $rejectedDocuments = $company->documents->where('review_status','rejected')->count();
    $missingRequiredDocuments = $requiredDocumentTypes->whereNotIn('id', $company->documents->pluck('document_type_id'));
    $documentsReady = $company->documents->isNotEmpty() && $missingRequiredDocuments->isEmpty() && $expiredDocuments === 0 && $company->documents->every(fn($document) => $document->review_status === 'approved');
@endphp

@if($errors->any())<div class="errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="company-detail-head">
    <div>
        <div class="detail-breadcrumb"><a href="{{ route('companies.index') }}">Compagnies</a> › Fiche opérateur</div>
        <h1>{{ $company->legal_name }}</h1>
        <span class="detail-status status-{{ $company->status->value }}">{{ $statusLabels[$company->status->value] }}</span>
        @if($company->trade_name)<span class="muted"> · {{ $company->trade_name }}</span>@endif
    </div>
    <div class="detail-actions">
        @if(auth()->user()->isPlatformMember())<a class="btn secondary" href="{{ route('companies.index') }}">Retour</a>@endif
        @if(auth()->user()->permitsPlatform('documents.view') || auth()->user()->can('update',$company))
            <a class="btn secondary" href="{{ route('documents.companies.show',$company) }}">Documents</a>
        @endif
        @can('update',$company)
            <a class="btn" href="{{ route('companies.edit',$company) }}">Modifier</a>
        @endcan
    </div>
</div>

<div class="company-state-banner status-{{ $company->status->value }}"><div><strong>{{ $company->status === \App\Enums\CompanyStatus::Active ? 'Compagnie opérationnelle' : 'Statut : '.$statusLabels[$company->status->value] }}</strong><span>{{ $company->status === \App\Enums\CompanyStatus::Active ? 'Les modules de gestion de la compagnie sont ouverts.' : 'Les capacités opérationnelles dépendent de la décision administrative et de la conformité du dossier.' }}</span></div><span class="detail-status status-{{ $company->status->value }}">{{ $statusLabels[$company->status->value] }}</span></div>

<div class="detail-metrics">
@can('viewAny', \App\Models\Agency::class)    <a class="detail-metric" href="{{ route('agencies.index') }}"><strong>{{ $company->agencies->count() }}</strong><span>Agences</span><small>Ouvrir le registre</small></a>@endcan
@can('viewAny', \App\Models\Employee::class)    <a class="detail-metric" href="{{ route('employees.index') }}"><strong>{{ $company->employees->count() }}</strong><span>Employés</span><small>Voir le personnel</small></a>@endcan
@can('viewAny', \App\Models\FleetUnit::class)    <a class="detail-metric" href="{{ route('fleet-units.index') }}"><strong>{{ $company->fleetUnits->count() }}</strong><span>Unités de flotte</span><small>Voir la flotte</small></a>@endcan
    <a class="detail-metric {{ $rejectedDocuments || $expiredDocuments ? 'critical' : ($pendingDocuments || $renewDocuments ? 'warning' : '') }}" href="{{ route('documents.companies.show',$company) }}"><strong>{{ $company->documents->count() }}</strong><span>Documents</span><small>{{ $approvedDocuments }} validé(s) · {{ $pendingDocuments }} à contrôler</small></a>
</div>

@can('changeStatus',$company)
<section class="decision-card">
    <h2>Décision administrative</h2>
    <div class="muted">Les décisions sont enregistrées dans l’historique de la compagnie.</div>
    @if($company->status === \App\Enums\CompanyStatus::Pending && auth()->user()->permitsPlatform('companies.validate'))
        @unless($documentsReady)<div class="decision-warning">La validation reste bloquée jusqu’à ce que toutes les pièces soient approuvées et non expirées.@if($missingRequiredDocuments->isNotEmpty())<br><strong>Pièce obligatoire manquante :</strong> {{ $missingRequiredDocuments->pluck('label')->implode(', ') }}.@endif</div>@endunless
        <div class="decision-grid">
            <form class="decision-option" method="POST" action="{{ route('companies.status.update',$company) }}">@csrf<input type="hidden" name="action" value="validate"><strong>Valider le dossier</strong><p class="muted">Active la compagnie si son dossier documentaire est recevable.</p><button class="btn success" type="submit" @disabled(!$documentsReady)>Valider et activer</button></form>
            <form class="decision-option" method="POST" action="{{ route('companies.status.update',$company) }}">@csrf<input type="hidden" name="action" value="reject"><label for="reject_reason">Refuser le dossier</label><textarea id="reject_reason" name="reason" required placeholder="Motif obligatoire">{{ old('action') === 'reject' ? old('reason') : '' }}</textarea><button class="btn danger" type="submit">Confirmer le refus</button></form>
        </div>
    @elseif($company->status === \App\Enums\CompanyStatus::Active && auth()->user()->permitsPlatform('companies.suspend'))
        <form class="decision-option" style="margin-top:16px" method="POST" action="{{ route('companies.status.update',$company) }}">@csrf<input type="hidden" name="action" value="suspend"><label for="suspend_reason">Suspendre la compagnie</label><textarea id="suspend_reason" name="reason" required placeholder="Motif obligatoire">{{ old('action') === 'suspend' ? old('reason') : '' }}</textarea><button class="btn warning" type="submit">Confirmer la suspension</button></form>
    @elseif($company->status === \App\Enums\CompanyStatus::Suspended && auth()->user()->permitsPlatform('companies.suspend'))
        <form class="decision-option" style="margin-top:16px" method="POST" action="{{ route('companies.status.update',$company) }}">@csrf<input type="hidden" name="action" value="reactivate"><label for="reactivate_reason">Réactiver la compagnie</label><textarea id="reactivate_reason" name="reason" placeholder="Motif facultatif">{{ old('action') === 'reactivate' ? old('reason') : '' }}</textarea><button class="btn success" type="submit">Confirmer la réactivation</button></form>
    @else
        <p class="muted">Aucune transition administrative n’est disponible pour ce statut.</p>
    @endif
</section>
@endcan

<div class="detail-grid">
    <section class="detail-card"><div class="detail-card-head"><h2>Identification</h2></div><div class="detail-card-body info-list">
        <div class="info-item"><small>Raison sociale</small><strong>{{ $company->legal_name }}</strong></div>
        <div class="info-item"><small>Nom commercial</small><strong>{{ $company->trade_name ?: 'Non renseigné' }}</strong></div>
        <div class="info-item"><small>RCCM</small><strong>{{ $company->rccm ?: 'Non renseigné' }}</strong></div>
        <div class="info-item"><small>NIF</small><strong>{{ $company->nif ?: 'Non renseigné' }}</strong></div>
        <div class="info-item full-info"><small>Mode de transport</small><div class="mode-chips">@forelse($company->transportModes as $mode)<span>{{ $modeLabels[$mode->label] ?? ucfirst($mode->label) }}</span>@empty<strong>Non renseigné</strong>@endforelse</div></div>
    </div></section>
    <section class="detail-card"><div class="detail-card-head"><h2>Coordonnées</h2></div><div class="detail-card-body info-list">
        <div class="info-item"><small>Email</small><strong>{{ $company->email }}</strong></div>
        <div class="info-item"><small>Téléphone</small><strong>{{ $company->phone }}</strong></div>
        <div class="info-item"><small>Ville</small><strong>{{ config('geography.'.$company->province.'.name', 'Province non renseignée') }} · {{ $company->city ?: 'Localité non renseignée' }}</strong></div>
        <div class="info-item"><small>Adresse</small><strong>{{ $company->address ?: 'Non renseignée' }}</strong></div>
    </div></section>
    <section class="detail-card"><div class="detail-card-head"><h2>Historique des statuts</h2></div><div class="detail-card-body">
        @forelse($company->statusHistory as $entry)
            <div class="history-item"><strong>{{ $entry->previous_status ? ($statusLabels[$entry->previous_status] ?? $entry->previous_status) : 'Création' }} → {{ $statusLabels[$entry->new_status] ?? $entry->new_status }}</strong><small>{{ $entry->created_at?->format('d/m/Y à H:i') }} · {{ $entry->decidedByUser?->name ?: 'Système' }}</small><small>{{ $entry->reason ?: 'Sans motif' }}</small></div>
        @empty<p class="muted">Aucun changement de statut enregistré.</p>@endforelse
    </div></section>
    <section class="detail-card full"><div class="detail-card-head linked-head"><div><h2>Documents de conformité</h2><p>État des pièces transmises et de leur contrôle administratif</p></div><a href="{{ route('documents.companies.show',$company) }}">Ouvrir le dossier complet</a></div><div class="document-review-summary"><div class="approved"><strong>{{ $approvedDocuments }}</strong><span>Pièces validées</span></div><div class="pending"><strong>{{ $pendingDocuments }}</strong><span>À contrôler</span></div><div class="rejected"><strong>{{ $rejectedDocuments }}</strong><span>Refusées</span></div><div class="missing"><strong>{{ $missingRequiredDocuments->count() }}</strong><span>Obligatoires manquantes</span></div></div>@include('documents._required-checklist', ['requiredDocumentTypes' => $requiredDocumentTypes, 'requiredDocuments' => $company->documents])<div class="document-scroll"><table class="document-table"><thead><tr><th>Type</th><th>Échéance</th><th>État d’échéance</th><th>Contrôle PNMG</th><th>Fichier</th><th>Décision</th></tr></thead><tbody>
        @forelse($company->documents as $document)<tr><td><strong>{{ $document->documentType->label }}</strong>@if($document->documentType->is_required)<small class="required-type">Obligatoire</small>@endif</td><td>{{ $document->valid_until->format('d/m/Y') }}</td><td><span class="detail-status status-{{ $document->status }}">{{ $documentLabels[$document->status] }}</span></td><td><span class="review-status {{ $document->review_status }}">{{ ['pending'=>'En attente','approved'=>'Validé','rejected'=>'Refusé'][$document->review_status] ?? 'En attente' }}</span>@if($document->review_status === 'rejected')<small class="review-reason"><strong>Motif :</strong> {{ $document->review_comment }}</small>@endif</td><td><a class="download-link" href="{{ route('company-documents.preview',$document) }}" target="_blank" rel="noopener">Consulter</a><br><a class="download-link" href="{{ route('company-documents.download',$document) }}">Télécharger</a></td><td>@include('documents._review-actions', ['document' => $document])</td></tr>
        @empty<tr><td colspan="6" class="muted">Aucun document associé à cette compagnie.</td></tr>@endforelse
    </tbody></table></div></section>
</div>
@push('styles')<style>.company-state-banner{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:20px;padding:16px 19px;border:1px solid #dce4e1;border-radius:11px;background:#fff}.company-state-banner.status-active,.company-state-banner.status-validated{border-color:#b8ddcf;background:#E8F5F0}.company-state-banner.status-pending{border-color:#efd89e;background:#FFF7E5}.company-state-banner.status-suspended,.company-state-banner.status-rejected{border-color:#efc5c5;background:#fff0f0}.company-state-banner strong,.company-state-banner>div>span{display:block}.company-state-banner>div>span{margin-top:4px;color:#64748b;font-size:12px}.detail-metric{display:block;color:inherit;text-decoration:none;transition:transform .15s,border-color .15s}.detail-metric:hover{transform:translateY(-2px);border-color:#b8ddcf}.detail-metric small{display:block;margin-top:5px;color:var(--green);font-size:10px;font-weight:750}.detail-metric.warning{border-top:3px solid var(--gold)}.detail-metric.critical{border-top:3px solid var(--danger)}.full-info{grid-column:1/-1}.mode-chips{display:flex;gap:7px;flex-wrap:wrap}.mode-chips span{padding:6px 9px;border-radius:99px;background:#E8F5F0;color:var(--dark);font-size:11px;font-weight:800}.linked-head{display:flex;align-items:center;justify-content:space-between;gap:15px}.linked-head p{margin:4px 0 0;color:#64748b;font-size:11px}.linked-head a{color:var(--green);font-size:12px;font-weight:800;text-decoration:none}.document-review-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;padding:16px 20px;border-bottom:1px solid #e2e8f0;background:#f8fafc}.document-review-summary>div{padding:12px;border:1px solid #e2e8f0;border-radius:9px;background:#fff}.document-review-summary strong,.document-review-summary span{display:block}.document-review-summary strong{font-size:22px}.document-review-summary span{margin-top:3px;color:#64748b;font-size:11px}.document-review-summary .approved{border-top:3px solid var(--green)}.document-review-summary .pending,.document-review-summary .missing{border-top:3px solid var(--gold)}.document-review-summary .rejected{border-top:3px solid var(--danger)}.decision-warning{margin-top:14px;padding:11px 13px;border-left:3px solid var(--warning);border-radius:7px;background:var(--warning-pale);color:#824519;font-size:12px}.btn:disabled{cursor:not-allowed;opacity:.55}.review-status{display:inline-flex;padding:5px 8px;border-radius:99px;font-size:10px;font-weight:800}.review-status.pending{background:#FFF7E5;color:#805d16}.review-status.approved{background:#E8F5F0;color:#076047}.review-status.rejected{background:#fff0f0;color:#a32130}.review-reason{display:block;max-width:300px;margin-top:5px;color:#a32130;font-size:11px}.required-type{display:inline-flex;margin-top:5px;padding:3px 6px;border-radius:99px;background:#e8f5f0;color:#076047;font-size:9px;font-weight:900;text-transform:uppercase}@media(max-width:760px){.document-review-summary{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){.company-state-banner,.linked-head{align-items:flex-start;flex-direction:column}.full-info{grid-column:auto}}</style>@endpush
@endsection
