<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#264653">
    <title>Connexion - PNMG</title>
    @section('title', 'Connexion sécurisée')
    @section('meta_description', 'Connexion sécurisée à votre espace PNMG.')
    @include('layouts._meta')
    <meta name="robots" content="noindex, nofollow">
    @include('layouts._palette')
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; color: var(--text); background: var(--bg); font-family: "Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif; }
        h1, h2, .logo strong { font-family: Corbel, Calibri, "Segoe UI", Arial, sans-serif; font-weight: 700; }
        main { min-height: 100vh; min-height: 100svh; display: grid; grid-template-columns: minmax(320px, 42%) 1fr; }
        .brand { padding: clamp(32px, 5vw, 80px); background: var(--dark) url("{{ asset('images/pnmg/trame-itineraires.svg') }}") center / cover no-repeat; color: white; display: flex; flex-direction: column; gap: 32px; }
        .logo { display: flex; align-items: center; gap: 14px; }
        .logo-compact { display: none; }
        .logo-mark { display: grid; place-items: center; width: 46px; height: 46px; border: 1px solid #ffffff40; border-radius: 12px; background: var(--green); }
        .logo-mark svg { width: 24px; height: 24px; stroke-width: 1.9; }
        .logo strong { display: block; font-size: 23px; letter-spacing: .08em; }
        .logo small { display: block; margin-top: 3px; color: #dce7e9; font-size: 11px; letter-spacing: .1em; text-transform: uppercase; }
        .brand-copy { max-width: 530px; margin-top: auto; }
        .brand-copy::before { content: ''; display: block; width: 52px; height: 4px; margin-bottom: 28px; background: var(--gold); }
        h1 { margin: 0; font-size: clamp(30px, 3vw, 44px); line-height: 1.15; letter-spacing: -.025em; font-weight: 700; }
        .brand-copy p { max-width: 430px; margin: 26px 0 0; color: #dce7e9; font-size: 17px; line-height: 1.75; }
        .brand-footer { margin: 0; color: #dce7e9; font-size: 12px; letter-spacing: .035em; }
        .form-side { padding: 24px 32px; display: flex; align-items: center; justify-content: center; }
        .card { width: 100%; max-width: 420px; padding: 32px; border: 1px solid var(--border); border-radius: 12px; background: #fff; box-shadow: 0 5px 20px #17352b0a; }
        .back-to-site { display: inline-flex; align-items: center; gap: 7px; min-height: 44px; margin: -10px 0 8px; color: var(--green); font-size: 13px; font-weight: 700; text-decoration: none; }
        .back-to-site:hover { text-decoration: underline; text-underline-offset: 4px; }
        .back-to-site svg { width: 16px; height: 16px; stroke-width: 2; }
        .back-to-site:focus-visible { outline: 3px solid var(--green); outline-offset: 3px; }
        h2 { margin: 0; font-size: 28px; line-height: 1.25; letter-spacing: -.03em; }
        .field { margin-top: 18px; }
        form .field:first-of-type { margin-top: 24px; }
        label { display: block; margin-bottom: 9px; font-size: 14px; font-weight: 650; }
        .text-input { width: 100%; min-height: 44px; padding: 10px 12px; border: 1px solid #a8b6bb; border-radius: 6px; color: var(--text); background: white; font: inherit; font-size: 16px; }
        .text-input::placeholder { color: var(--muted); opacity: 1; }
        .text-input:focus { border-color: var(--green); }
        .text-input:focus-visible { outline: 3px solid var(--green); outline-offset: 3px; }
        .text-input[aria-invalid=true] { border-color: var(--danger); }
        .password-field { position: relative; }
        .password-field .text-input { padding-right: 56px; }
        .password-field input::-ms-reveal, .password-field input::-ms-clear { display: none; }
        .password-toggle { position: absolute; top: 0; right: 0; width: 44px; height: 44px; display: grid; place-items: center; border: 0; border-radius: 6px; background: transparent; color: var(--muted); cursor: pointer; }
        .password-toggle:hover { background: var(--pale); color: var(--green); }
        .password-toggle[hidden], .password-toggle [hidden] { display: none; }
        .password-toggle svg { display: block; width: 16px; height: 16px; stroke-width: 2; }
        .submit { width: 100%; min-height: 44px; margin-top: 18px; padding: 10px 14px; border: 0; border-radius: 8px; background: var(--green); color: white; font: inherit; font-size: 15px; font-weight: 700; cursor: pointer; }
        .submit:not(:disabled):hover { background: var(--green-hover); }
        .submit:not(:disabled):active { filter: brightness(.9); }
        .submit:disabled { background: #d9e0dd; color: var(--muted); cursor: not-allowed; }
        .register { margin: 18px 0 0; color: var(--muted); text-align: center; font-size: 13px; line-height: 1.5; }
        .register a { display: inline-block; color: var(--green); font-weight: 700; text-underline-offset: 4px; }
        .field-error { margin: 8px 0 0; color: var(--danger-text); font-size: 13px; line-height: 1.5; }
        .status { margin-bottom: 20px; padding: 12px 14px; border: 1px solid #b8ddcf; border-radius: 8px; background: var(--pale); font-size: 14px; line-height: 1.5; }
        @media (max-width: 800px) {
            main { display: block; }
            .brand { padding: 24px; gap: 24px; }
            .logo strong { font-size: 19px; }
            .logo-mark { width: 38px; height: 38px; font-size: 20px; }
            .brand-copy { max-width: 540px; }
            .brand-copy::before { width: 32px; height: 3px; margin-bottom: 14px; }
            h1 { font-size: 27px; line-height: 1.2; }
            .brand-copy p, .brand-footer { display: none; }
            .form-side { padding: 28px 18px 40px; }
            .card { max-width: 420px; padding: 28px 24px; border-radius: 10px; box-shadow: none; }
            h2 { font-size: 25px; }
        }
        @media (max-width: 380px) {
            .brand { padding: 20px; }
            h1 { font-size: 24px; }
            .card { padding: 24px 18px; }
            .form-side { padding: 20px 12px 32px; }
        }
        @media (max-width: 599.98px) {
            .brand { padding: 16px; gap: 12px; }
            .logo-full { display: none; }
            .logo-compact { display: flex; align-items: center; gap: 8px; }
            .logo-compact img { width: 28px; height: 28px; padding: 3px; border-radius: 4px; background: #fff; }
            .logo-compact strong { color: #fff; font: 700 18px/1 Corbel, Calibri, "Segoe UI", Arial, sans-serif; letter-spacing: .01em; }
            .brand-copy::before { display: none; }
            .brand-copy::after { content: ''; display: block; width: 32px; height: 3px; margin-top: 12px; background: var(--gold); }
            h1 { font-size: 22px; }
            .form-side { padding: 16px; }
            .card { padding: 20px; }
            h2 { font-size: 22px; }
            .register a { min-height: 44px; display: inline-flex; align-items: center; }
        }
    </style>
</head>
<body>
<main>
    <section class="brand" aria-labelledby="platform-title">
        <div class="logo"><img class="logo-full" src="{{ asset('images/pnmg/logo-horizontal-inverse.png') }}" alt="PNMG — Plateforme Nationale de Mobilité du Gabon" width="188" height="40"><span class="logo-compact"><img src="{{ asset('images/pnmg/icone-couleur.svg') }}" alt="" width="28" height="28"><strong>PNMG</strong></span></div>
        <div class="brand-copy">
            <h1 id="platform-title">Plateforme Nationale de Mobilité du Gabon</h1>
            <p>Un espace commun pour l’enregistrement et la gestion des compagnies de transport.</p>
        </div>
        <p class="brand-footer">Le référentiel des compagnies de transport</p>
    </section>
    <section class="form-side" aria-labelledby="login-title">
        <div class="card">
            <a class="back-to-site" href="{{ route('home') }}"><x-lucide-arrow-left aria-hidden="true"/>Retour au site vitrine</a>
            <h2 id="login-title">Se connecter</h2>
            @if (session('status'))
                <div class="status" role="status">{{ session('status') }}</div>
            @endif
            @foreach ($errors->getMessages() as $field => $messages)
                @if (! in_array($field, ['email', 'password']))
                    <p class="field-error" role="alert">{{ $messages[0] }}</p>
                @endif
            @endforeach
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="email">Adresse e-mail</label>
                    <input class="text-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="prenom.nom@compagnie.ga" autocomplete="username" required @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                    @error('email') <p class="field-error" id="email-error" role="alert">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="password-field">
                        <input class="text-input" id="password" name="password" type="password" autocomplete="current-password" required @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif>
                        <button class="password-toggle" id="password-toggle" type="button" aria-controls="password" aria-label="Afficher le mot de passe" aria-pressed="false" hidden>
                            <span data-password-icon="show"><x-lucide-eye aria-hidden="true" /></span>
                            <span data-password-icon="hide" hidden><x-lucide-eye-off aria-hidden="true" /></span>
                        </button>
                    </div>
                    @error('password') <p class="field-error" id="password-error" role="alert">{{ $message }}</p> @enderror
                </div>
                <button class="submit" type="submit">Se connecter</button>
            </form>
            <p class="register">Représentant d’une compagnie ? <a href="{{ route('registration.identification') }}">Créer un compte</a></p>
        </div>
    </section>
</main>
@include('layouts._loading')
<script>
    (() => {
        const password = document.getElementById('password');
        const toggle = document.getElementById('password-toggle');
        const showIcon = toggle.querySelector('[data-password-icon="show"]');
        const hideIcon = toggle.querySelector('[data-password-icon="hide"]');
        toggle.hidden = false;
        toggle.addEventListener('click', () => {
            const visible = password.type === 'password';
            password.type = visible ? 'text' : 'password';
            showIcon.hidden = visible;
            hideIcon.hidden = !visible;
            toggle.setAttribute('aria-pressed', String(visible));
            toggle.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    })();
</script>
</body>
</html>
