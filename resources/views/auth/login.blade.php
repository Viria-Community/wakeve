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
        *,*::before,*::after{box-sizing:border-box}
        html,body{margin:0;padding:0}
        body{min-height:100vh;color:var(--text);background:var(--bg);font-family:"Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif;-webkit-font-smoothing:antialiased}
        h1,h2,.logo strong{font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-weight:700}
        .lucide{display:inline-block;vertical-align:middle;flex:0 0 auto}

        main{min-height:100vh;min-height:100svh;display:grid;grid-template-columns:46% 54%}

        /* ---------- Panneau institutionnel ---------- */
        .brand{position:relative;overflow:hidden;padding:clamp(36px,5vw,80px);background:linear-gradient(160deg,#2c4f5b 0%,#264653 52%,#1f3b45 100%);color:#fff;display:flex;flex-direction:column;gap:32px}
        .brand::before{content:"";position:absolute;inset:0;background:url("{{ asset('images/pnmg/trame-itineraires.svg') }}") left center / auto 100% no-repeat;opacity:.14;pointer-events:none}
        .brand>*{position:relative;z-index:1}
        .logo{display:flex;align-items:center;gap:14px}
        .logo img{display:block;width:188px;height:auto}
        .logo-compact{display:none}
        .brand-copy{max-width:480px;margin-top:auto}
        .brand-copy::before{content:"";display:block;width:48px;height:3px;margin-bottom:26px;background:var(--gold)}
        h1{margin:0;font-size:clamp(30px,3.1vw,42px);line-height:1.14;letter-spacing:-.028em;font-weight:700}
        .brand-copy p{max-width:420px;margin:24px 0 0;color:#c8d9da;font-size:16px;line-height:1.75}
        .brand-footer{margin:0;padding-top:24px;border-top:1px solid rgba(255,255,255,.16);color:#a9c0c2;font-size:12px;letter-spacing:.02em}

        /* ---------- Formulaire ---------- */
        .form-side{display:flex;align-items:center;justify-content:center;padding:40px 32px;background:var(--bg)}
        .card{width:100%;max-width:430px;padding:38px 36px;border:1px solid #e2e8e6;border-radius:14px;background:#fff;box-shadow:0 14px 40px rgba(38,70,83,.07)}
        .back-to-site{display:inline-flex;align-items:center;gap:7px;min-height:40px;margin:-6px 0 10px;color:var(--green);font-size:12.5px;font-weight:700;text-decoration:none}
        .back-to-site:hover{text-decoration:underline;text-underline-offset:4px}
        .back-to-site svg{width:15px;height:15px;stroke-width:2}
        h2{margin:0;font-size:27px;line-height:1.25;letter-spacing:-.03em;color:var(--text)}
        .field{margin-top:18px}
        form .field:first-of-type{margin-top:24px}
        label{display:block;margin-bottom:8px;color:#2c4a56;font-size:13.5px;font-weight:700}
        .text-input{width:100%;min-height:46px;padding:11px 13px;border:1px solid #c7d3cf;border-radius:8px;color:var(--text);background:#fff;font:inherit;font-size:15px;transition:border-color .16s ease,box-shadow .16s ease}
        .text-input::placeholder{color:#87979c;opacity:1}
        .text-input:hover{border-color:#aebfba}
        .text-input:focus{border-color:var(--green);outline:none;box-shadow:0 0 0 3px rgba(8,122,90,.14)}
        .text-input[aria-invalid=true]{border-color:var(--danger)}
        .text-input[aria-invalid=true]:focus{box-shadow:0 0 0 3px rgba(230,57,70,.12)}
        .password-field{position:relative}
        .password-field .text-input{padding-right:54px}
        .password-field input::-ms-reveal,.password-field input::-ms-clear{display:none}
        .password-toggle{position:absolute;top:1px;right:1px;width:44px;height:44px;display:grid;place-items:center;border:0;border-radius:8px;background:transparent;color:#6d7f85;cursor:pointer;transition:background-color .16s ease,color .16s ease}
        .password-toggle:hover{background:var(--pale);color:var(--green)}
        .password-toggle[hidden],.password-toggle [hidden]{display:none}
        .password-toggle svg{display:block;width:17px;height:17px;stroke-width:2}
        .submit{width:100%;min-height:46px;margin-top:22px;padding:11px 16px;border:0;border-radius:8px;background:var(--green);color:#fff;font:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:background-color .16s ease}
        .submit:not(:disabled):hover{background:var(--green-hover)}
        .submit:not(:disabled):active{background:#05573f}
        .submit:disabled{background:#d9e0dd;color:var(--muted);cursor:not-allowed}
        .register{margin:18px 0 0;padding-top:18px;border-top:1px solid #edf1ef;color:var(--muted);text-align:center;font-size:13px;line-height:1.6}
        .register a{display:inline-block;color:var(--green);font-weight:700;text-underline-offset:4px}
        .register a:hover{text-decoration-thickness:2px}
        .field-error{margin:8px 0 0;color:var(--danger-text);font-size:12.5px;line-height:1.5}
        .status{margin:18px 0 0;padding:11px 14px;border:1px solid #bfe0d4;border-left:3px solid var(--green);border-radius:8px;background:var(--pale);color:#1d4f40;font-size:13.5px;line-height:1.5}
        :is(a,button,input,select,textarea):focus-visible{outline:3px solid var(--green);outline-offset:3px}

        @media (max-width:900px){
            main{grid-template-columns:1fr}
            .brand{padding:22px 24px;gap:18px}
            .brand-copy{margin-top:6px}
            .brand-copy::before{width:34px;height:3px;margin-bottom:14px}
            h1{font-size:23px;line-height:1.25}
            .brand-copy p,.brand-footer{display:none}
            .form-side{padding:32px 18px 44px}
            .card{max-width:430px;padding:30px 26px;border-radius:12px;box-shadow:none}
            h2{font-size:25px}
        }
        @media (max-width:599.98px){
            .brand{padding:16px}
            .logo-full{display:none}
            .logo-compact{display:flex;align-items:center;gap:10px}
            .logo-compact img{width:30px;height:30px;padding:3px;border-radius:5px;background:#fff}
            .logo-compact strong{color:#fff;font:700 19px/1 Corbel, Calibri, "Segoe UI", Arial, sans-serif;letter-spacing:.01em}
            .brand-copy::before{display:none}
            .brand-copy::after{content:"";display:block;width:32px;height:3px;margin-top:12px;background:var(--gold)}
            h1{font-size:21px}
            .form-side{padding:16px}
            .card{padding:24px 20px}
            h2{font-size:22px}
            .register a{min-height:44px;display:inline-flex;align-items:center}
            .text-input{font-size:16px}
        }
    </style>

<link rel="stylesheet" href="{{ asset('css/pnmg-ui.css') }}">
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
