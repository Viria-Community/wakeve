<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — PNMG</title>
    @include('layouts._meta')
    @include('layouts._palette')
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font-family:"Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif;line-height:1.75;-webkit-font-smoothing:antialiased}
        a{color:var(--green)}
        .skip-link{position:fixed;top:-60px;left:16px;z-index:50;padding:10px 14px;border-radius:7px;background:#fff;color:var(--dark);font-weight:700}
        .skip-link:focus{top:12px}
        .public-bar{border-bottom:1px solid rgba(255,255,255,.14);background:var(--dark);color:#fff}
        .public-bar-inner,.legal-shell{width:min(960px,calc(100% - 40px));margin:auto}
        .public-bar-inner{min-height:72px;display:flex;align-items:center;justify-content:space-between;gap:20px}
        .public-brand{color:#fff;text-decoration:none;display:block}
        .public-brand img{display:block;width:188px;height:auto}
        .back-link{display:inline-flex;align-items:center;min-height:40px;padding:8px 14px;border:1px solid rgba(255,255,255,.35);border-radius:7px;color:#fff;font-size:12.5px;font-weight:700;text-decoration:none;transition:background-color .16s ease,border-color .16s ease}
        .back-link:hover{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.6)}
        .legal-shell{padding:64px 0 84px}
        .legal-intro{padding-bottom:28px;border-bottom:2px solid var(--green)}
        .legal-intro p{max-width:720px;color:var(--muted)}
        h1,h2{font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;line-height:1.2;color:var(--text)}
        h1{margin:0;font-size:clamp(32px,5vw,46px);letter-spacing:-.03em}
        h2{margin:38px 0 10px;font-size:21px;letter-spacing:-.01em}
        p,li{font-size:15px}
        .legal-meta{margin-top:14px;color:var(--muted);font-size:12.5px}
        .legal-note{margin:28px 0;padding:16px 20px;border:1px solid #eadcb6;border-left:3px solid var(--gold);border-radius:8px;background:#fdf9ef;color:#5f512f}
        .legal-footer{padding:26px 0;border-top:1px solid var(--border);background:#fff;color:var(--muted);font-size:12.5px}
        .legal-footer-inner{width:min(960px,calc(100% - 40px));margin:auto;display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}
        .legal-footer a{color:var(--text)}
        .legal-footer a:hover{color:var(--green)}
        :is(a,button,input,select,textarea):focus-visible{outline:3px solid var(--green);outline-offset:3px}
        @media(max-width:640px){
            .public-bar-inner{min-height:64px}
            .public-brand img{width:160px}
            .legal-shell{padding:42px 0 58px}
            h2{font-size:19px}
        }
    </style>
    @stack('styles')
<link rel="stylesheet" href="{{ asset('css/pnmg-ui.css') }}">
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="public-bar"><div class="public-bar-inner"><a class="public-brand" href="{{ route('home') }}" aria-label="Accueil PNMG"><img src="{{ asset('images/pnmg/logo-horizontal-inverse.png') }}" alt="PNMG — Plateforme Nationale de Mobilité du Gabon" width="188" height="40"></a><a class="back-link" href="{{ route('home') }}">Retour à l’accueil</a></div></header>
<main class="legal-shell" id="contenu">@yield('content')</main>
<footer class="legal-footer"><div class="legal-footer-inner"><span>Plateforme Nationale de Mobilité du Gabon</span><span><a href="{{ route('legal.terms') }}">Conditions générales</a> · <a href="{{ route('legal.privacy') }}">Confidentialité</a></span></div></footer>
@include('layouts._loading')
</body>
</html>
