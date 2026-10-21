@extends('layouts.onboarding')
@section('title', 'Documents administratifs')
@section('step1', 'done')
@section('step2', 'done')
@section('step3', 'done')
@section('step4', 'active')
@section('content')
<div class="page-intro"><h1>Documents administratifs</h1><p>Déposez toutes les pièces indiquées comme obligatoires. Formats acceptés : PDF, JPG ou PNG, 2 Mo maximum par fichier.</p></div>
<form method="POST" enctype="multipart/form-data" action="{{ route('registration.submit') }}">@csrf
<section class="card"><div class="card-body">@foreach($documentTypes as $type)<div @class(['document', 'required-document' => $type->is_required])><h2>{{ $type->label }} @if($type->is_required)<span class="required-badge">Obligatoire</span>@endif</h2><div class="grid">
    <div class="full"><label for="file-{{ $type->id }}">Fichier @if($type->is_required)<span aria-hidden="true">*</span>@endif</label><input class="document-file" id="file-{{ $type->id }}" type="file" name="documents[{{ $type->id }}][file]" accept=".pdf,.jpg,.jpeg,.png" data-valid-until="valid-{{ $type->id }}" data-feedback="feedback-{{ $type->id }}" data-required="{{ $type->is_required ? 'true' : 'false' }}" @required($type->is_required)><div class="hint" id="feedback-{{ $type->id }}">{{ $type->is_required ? 'Pièce indispensable à la validation' : 'Aucun fichier sélectionné' }} · 2 Mo maximum.</div>@error("documents.{$type->id}.file")<div class="field-error">{{ $message }}</div>@enderror</div>
    <div><label for="issued-{{ $type->id }}">Date de délivrance</label><input id="issued-{{ $type->id }}" type="date" name="documents[{{ $type->id }}][issued_at]" value="{{ old("documents.{$type->id}.issued_at") }}"></div>
    <div><label for="valid-{{ $type->id }}">Valable jusqu'au @if($type->is_required)<span aria-hidden="true">*</span>@endif</label><input id="valid-{{ $type->id }}" type="date" name="documents[{{ $type->id }}][valid_until]" value="{{ old("documents.{$type->id}.valid_until") }}" min="{{ now()->toDateString() }}" @required($type->is_required)>@error("documents.{$type->id}.valid_until")<div class="field-error">{{ $message }}</div>@enderror</div>
</div></div>@endforeach</div>
<div class="card-actions"><a class="button secondary" href="{{ route('registration.modes') }}">Précédent</a><button class="button" type="submit">Soumettre la demande</button></div></section></form>
<script>
document.querySelectorAll('.document-file').forEach((input) => {
    input.addEventListener('change', () => {
        const file = input.files[0];
        const feedback = document.getElementById(input.dataset.feedback);
        const validUntil = document.getElementById(input.dataset.validUntil);
        const alwaysRequired = input.dataset.required === 'true';
        validUntil.required = alwaysRequired || Boolean(file);

        if (!file) {
            feedback.textContent = 'Aucun fichier sélectionné · 2 Mo maximum.';
            feedback.style.color = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            input.value = '';
            validUntil.required = alwaysRequired;
            feedback.textContent = 'Ce fichier dépasse la limite de 2 Mo.';
            feedback.style.color = '#991b1b';
            return;
        }

        feedback.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} Mo`;
        feedback.style.color = '#166534';
    });
});
</script>
<style>.required-document{border-left:4px solid var(--green);background:#f8fcfa}.required-badge{display:inline-flex;margin-left:8px;padding:4px 7px;border-radius:99px;background:#e8f5f0;color:#076047;font-size:9px;font-weight:900;text-transform:uppercase;vertical-align:middle}.field-error{margin-top:6px;color:#991b1b;font-size:12px}</style>
@endsection
