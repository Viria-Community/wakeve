@extends($user->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Mon profil - PNMG')
@push('styles')
<style>
    .profile-head{margin-bottom:25px}.profile-head h1{margin:5px 0;font-size:32px}.profile-head p,.profile-breadcrumb{margin:0;color:#64748b}.profile-breadcrumb{font-size:12px}.profile-grid{display:grid;grid-template-columns:1.3fr .7fr;gap:20px}.profile-card{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden}.profile-section{padding:24px}.profile-section+.profile-section{border-top:1px solid #e2e8f0}.profile-section h2{margin:0 0 5px;font-size:18px}.profile-section>p{margin:0 0 18px;color:#64748b;font-size:13px}.profile-fields{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}.profile-field.full{grid-column:1/-1}.profile-field label{display:block;margin-bottom:7px;font-size:13px;font-weight:700}.profile-field input{width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:8px}.profile-field input:focus{outline:3px solid #E8F5F0;border-color:var(--green)}.field-error{display:block;margin-top:5px;color:#b91c1c;font-size:12px}.profile-footer{display:flex;justify-content:flex-end;padding:18px 24px;border-top:1px solid #e2e8f0;background:#f8fafc}.security-card{padding:22px}.profile-identity{display:flex;align-items:center;gap:13px;padding-bottom:18px;border-bottom:1px solid #e2e8f0}.profile-identity-avatar{width:48px;height:48px;display:grid;place-items:center;border-radius:13px;background:var(--dark);color:#fff;font-weight:900}.profile-identity strong,.profile-identity small{display:block}.profile-identity small{margin-top:3px;color:#64748b;font-size:12px}.profile-facts{display:grid;gap:12px;margin:18px 0}.profile-facts div{padding:11px;border-radius:9px;background:#f8fafc}.profile-facts dt{color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.07em}.profile-facts dd{margin:4px 0 0;font-size:13px;overflow-wrap:anywhere}.security-card h2{margin:16px 0 8px}.security-card p{color:#64748b;line-height:1.55}.security-card ul{padding-left:20px;color:#475569;line-height:1.7}.profile-errors{margin-bottom:20px;padding:14px;border-radius:9px;background:#fef2f2;color:#991b1b}
    @media(max-width:850px){.profile-grid,.profile-fields{grid-template-columns:1fr}.profile-field.full{grid-column:auto}}
</style>
@endpush
@section('content')
@php
    $profileRole = $user->customRole?->name ?? match ($user->role) {
        'admin' => 'Administrateur de la plateforme',
        'platform_staff' => $user->platformRole?->name ?? 'Personnel PNMG',
        'company_manager' => 'Gestionnaire de compagnie',
        'agency_manager' => 'Responsable d’agence',
        default => 'Agent de compagnie',
    };
    $profileInitials = collect(preg_split('/\s+/u', trim($user->name)) ?: [])->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
@endphp
<div class="profile-head"><div class="profile-breadcrumb">Mon compte › Profil</div><h1>Mon profil</h1><p>Gérez vos informations personnelles et la sécurité de votre compte.</p></div>
@if($errors->any())<div class="profile-errors"><strong>Le formulaire contient des erreurs.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="profile-grid">
    <form class="profile-card" method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
        <section class="profile-section"><h2>Informations du compte</h2><p>Ces informations vous identifient sur la plateforme.</p><div class="profile-fields">
            <div class="profile-field"><label for="name">Nom complet</label><input id="name" name="name" value="{{ old('name',$user->name) }}" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="profile-field"><label for="email">Adresse email</label><input id="email" type="email" name="email" value="{{ old('email',$user->email) }}" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
        </div></section>
        <section class="profile-section"><h2>Changer le mot de passe</h2><p>Laissez ces champs vides si vous souhaitez uniquement modifier le nom ou l’adresse email.</p><div class="profile-fields">
            <div class="profile-field full"><label for="current_password">Mot de passe actuel</label><input id="current_password" type="password" name="current_password" autocomplete="current-password">@error('current_password')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="profile-field"><label for="password">Nouveau mot de passe</label><input id="password" type="password" name="password" autocomplete="new-password">@error('password')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="profile-field"><label for="password_confirmation">Confirmation</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"></div>
        </div></section>
        <div class="profile-footer"><button class="btn" type="submit">Enregistrer</button></div>
    </form>
    <aside class="profile-card security-card">
        <div class="profile-identity"><span class="profile-identity-avatar">{{ $profileInitials ?: 'PN' }}</span><div><strong>{{ $user->name }}</strong><small>{{ $profileRole }}</small></div></div>
        <dl class="profile-facts"><div><dt>Email</dt><dd>{{ $user->email }}</dd></div>@if($profilePhone)<div><dt>Téléphone</dt><dd>{{ $profilePhone }}</dd></div>@endif @if($user->company)<div><dt>Compagnie</dt><dd>{{ $user->company->legal_name }}</dd></div>@endif</dl>
        <h2>Sécurité du compte</h2><p>Le mot de passe protège l’accès aux fonctions autorisées pour votre profil.</p><ul><li>Au moins 8 caractères</li><li>Majuscules et minuscules</li><li>Au moins un chiffre</li><li>Au moins un symbole</li></ul><p>Ne communiquez jamais vos identifiants.</p>
    </aside>
</div>
@endsection
