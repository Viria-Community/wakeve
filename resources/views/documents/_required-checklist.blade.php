<section class="required-documents-checklist" aria-labelledby="required-documents-title">
    <div class="required-documents-head">
        <div>
            <h3 id="required-documents-title">Pièces obligatoires pour valider la compagnie</h3>
            <p>Chaque pièce doit être fournie, approuvée par la PNMG et non expirée.</p>
        </div>
        <span>{{ $requiredDocumentTypes->count() }} pièce(s) exigée(s)</span>
    </div>
    <div class="required-documents-list">
        @forelse($requiredDocumentTypes as $requiredType)
            @php
                $requiredDocument = $requiredDocuments->firstWhere('document_type_id', $requiredType->id);
                $requiredState = match (true) {
                    ! $requiredDocument => ['missing', 'Manquante'],
                    $requiredDocument->review_status === 'rejected' => ['rejected', 'Refusée'],
                    ($requiredDocument->review_status ?? 'pending') === 'pending' => ['pending', 'À contrôler'],
                    $requiredDocument->status === 'expired' => ['expired', 'Expirée'],
                    $requiredDocument->status === 'to_renew' => ['renew', 'Approuvée · à renouveler'],
                    default => ['approved', 'Approuvée'],
                };
            @endphp
            <div class="required-document-row">
                <div>
                    <strong>{{ $requiredType->label }}</strong>
                    <small>Document obligatoire</small>
                </div>
                <span class="required-document-state {{ $requiredState[0] }}">{{ $requiredState[1] }}</span>
            </div>
        @empty
            <p class="required-documents-empty">Aucune pièce obligatoire n’est configurée dans le référentiel documentaire.</p>
        @endforelse
    </div>
    <p class="required-documents-note"><strong>À distinguer :</strong> le numéro RCCM est obligatoire dans l’identification de la compagnie, mais il ne constitue pas actuellement une pièce à téléverser séparément.</p>
</section>

@once
    @push('styles')
        <style>
            .required-documents-checklist{margin:16px 20px;border:1px solid #efd89e;border-radius:10px;background:#fffaf0;overflow:hidden}.required-documents-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:14px 16px;border-bottom:1px solid #efd89e}.required-documents-head h3{margin:0;font-size:16px}.required-documents-head p{margin:4px 0 0;color:#64748b;font-size:11px}.required-documents-head>span{flex:0 0 auto;padding:5px 8px;border-radius:99px;background:#fff;color:#805d16;font-size:10px;font-weight:800}.required-documents-list{display:grid}.required-document-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px 16px;border-bottom:1px solid #f4e7c4;background:#fff}.required-document-row strong,.required-document-row small{display:block}.required-document-row small{margin-top:3px;color:#805d16;font-size:10px;font-weight:800;text-transform:uppercase}.required-document-state{flex:0 0 auto;padding:5px 8px;border-radius:99px;font-size:10px;font-weight:850}.required-document-state.approved{background:#e8f5f0;color:#076047}.required-document-state.pending,.required-document-state.renew{background:#fff7e5;color:#805d16}.required-document-state.missing,.required-document-state.rejected,.required-document-state.expired{background:#fff0f0;color:#a32130}.required-documents-empty{margin:0;padding:13px 16px;color:#64748b;font-size:12px}.required-documents-note{margin:0;padding:11px 16px;color:#596970;font-size:11px;line-height:1.5}
            @media(max-width:620px){.required-documents-head,.required-document-row{align-items:flex-start;flex-direction:column}.required-documents-head>span,.required-document-state{align-self:flex-start}}
        </style>
    @endpush
@endonce
