@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title',isset($document) ? 'Modifier un document - PNMG' : 'Ajouter un document - PNMG')
@section('content')
@include('documents._styles')
<div class="doc-head"><div><div class="doc-breadcrumb"><a href="{{ $cancelRoute }}">Documents</a> › {{ isset($document) ? 'Modification' : 'Ajout' }}</div><h1>{{ isset($document) ? 'Modifier le document' : 'Ajouter un document' }}</h1><p>{{ $ownerLabel }}</p></div></div>
@if(!isset($document) && $documentTypes->isEmpty())
<div class="doc-form">
    <div class="doc-form-body"><div class="doc-note">Tous les types de documents disponibles sont déjà enregistrés. Pour renouveler ou corriger une pièce, ouvrez le document existant depuis la liste des documents.</div></div>
    <div class="doc-form-footer"><a class="btn secondary" href="{{ $cancelRoute }}">Retour aux documents</a></div>
</div>
@else
<form class="doc-form" method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data">
    @csrf
    @isset($document) @method('PUT') @endisset
    @if($errors->any())<div class="doc-errors"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if(isset($document) && $document->review_status === 'rejected')<div class="doc-errors"><strong>Correction demandée par le contrôle PNMG</strong><div>{{ $document->review_comment }}</div></div>@endif
    <div class="doc-form-body"><div class="doc-grid">
        <div class="doc-field full"><label for="document_type_id">Type de document <span class="required">*</span></label><select id="document_type_id" name="document_type_id" required><option value="">Choisir…</option>@foreach($documentTypes as $type)<option value="{{ $type->id }}" @selected((string)old('document_type_id',$document->document_type_id ?? '') === (string)$type->id)>{{ $type->label }}{{ $type->is_required ? ' — obligatoire' : '' }}</option>@endforeach</select>@error('document_type_id')<span class="doc-error">{{ $message }}</span>@enderror</div>
        @php($replacementRequired = !isset($document) || $document->review_status === 'rejected')
        <div class="doc-field full"><label for="file">{{ isset($document) ? 'Remplacer le fichier' : 'Fichier' }} @if($replacementRequired)<span class="required">*</span>@endif</label><input class="upload-zone" id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" @required($replacementRequired)><span class="doc-help">{{ isset($document) && !$replacementRequired ? 'Laissez ce champ vide pour conserver le fichier actuel. ' : '' }}PDF, JPG ou PNG · 5 Mo maximum · stockage privé</span>@error('file')<span class="doc-error">{{ $message }}</span>@enderror</div>
        <div class="doc-field"><label for="issued_at">Date d’émission</label><input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at',isset($document) ? $document->issued_at?->format('Y-m-d') : '') }}">@error('issued_at')<span class="doc-error">{{ $message }}</span>@enderror</div>
        <div class="doc-field"><label for="valid_until">Valide jusqu’au <span class="required">*</span></label><input id="valid_until" name="valid_until" type="date" value="{{ old('valid_until',isset($document) ? $document->valid_until->format('Y-m-d') : '') }}" required>@error('valid_until')<span class="doc-error">{{ $message }}</span>@enderror</div>
        <div class="doc-field full doc-note">Le statut sera calculé automatiquement à partir de la date d’échéance.</div>
    </div></div>
    <div class="doc-form-footer"><a class="btn secondary" href="{{ $cancelRoute }}">Annuler</a><button class="btn" type="submit">{{ isset($document) ? 'Enregistrer les modifications' : 'Enregistrer le document' }}</button></div>
</form>
@endif
@endsection
