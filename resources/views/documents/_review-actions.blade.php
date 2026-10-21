@php
    $canReviewDocument = auth()->user()->permitsPlatform('documents.validate')
        && ($document->review_status ?? 'pending') === 'pending';
    $reviewRoute = $document instanceof \App\Models\CompanyDocument
        ? route('company-documents.review', $document)
        : route('fleet-documents.review', $document);
    $reviewKey = $document instanceof \App\Models\CompanyDocument ? 'company' : 'fleet';
@endphp

@if($canReviewDocument)
    <div class="document-review-actions" aria-label="Décision sur le document">
        <form method="POST" action="{{ $reviewRoute }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="decision" value="approved">
            <button class="btn success document-review-button" type="submit">Valider</button>
        </form>
        <form class="document-reject-form" method="POST" action="{{ $reviewRoute }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="decision" value="rejected">
            <label class="sr-only" for="review-comment-{{ $reviewKey }}-{{ $document->id }}">Motif du refus</label>
            <input id="review-comment-{{ $reviewKey }}-{{ $document->id }}" name="comment" required maxlength="1000" placeholder="Motif du refus">
            <button class="btn danger document-review-button" type="submit">Refuser</button>
        </form>
    </div>
@endif

@once
    @push('styles')
        <style>
            .document-review-actions{display:flex;align-items:flex-start;gap:8px;min-width:300px}.document-review-actions form{margin:0}.document-reject-form{display:flex;align-items:stretch;gap:8px;flex:1}.document-reject-form input{min-width:150px;width:100%;padding:9px 10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:inherit}.document-review-button{min-height:38px;padding:9px 13px;white-space:nowrap}.btn.success{background:#16855f}.btn.danger{background:#c93442}.sr-only{position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important}
            @media(max-width:700px){.document-review-actions,.document-reject-form{min-width:240px;flex-direction:column}.document-review-button{width:100%}}
        </style>
    @endpush
@endonce
