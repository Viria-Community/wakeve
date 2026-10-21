<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — PNMG</title>
    @include('layouts._meta')
    @include('layouts._palette')
    <style>
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:"Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif;line-height:1.7}a{color:var(--green)}.skip-link{position:fixed;top:-60px;left:16px;z-index:50;padding:10px 14px;background:#fff;color:var(--dark);font-weight:700}.skip-link:focus{top:12px}.public-bar{border-bottom:1px solid var(--border);background:var(--dark);color:#fff}.public-bar-inner,.legal-shell{width:min(920px,calc(100% - 36px));margin:auto}.public-bar-inner{min-height:72px;display:flex;align-items:center;justify-content:space-between;gap:20px}.public-brand{color:#fff;font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:21px;font-weight:700;text-decoration:none}.public-brand small{display:block;color:#c8d8da;font-family:"Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif;font-size:9px;letter-spacing:.14em;text-transform:uppercase}.back-link{color:#fff;font-size:13px;font-weight:700;text-decoration:none}.legal-shell{padding:64px 0 82px}.legal-intro{padding-bottom:30px;border-bottom:3px solid var(--green)}.legal-intro p{max-width:720px;color:var(--muted)}h1,h2{font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;line-height:1.2}h1{margin:0;font-size:clamp(34px,6vw,52px);letter-spacing:-.025em}h2{margin:38px 0 10px;font-size:23px}p,li{font-size:15px}.legal-meta{margin-top:14px;color:var(--muted);font-size:13px}.legal-note{margin:28px 0;padding:17px 20px;border-left:4px solid var(--gold);background:#fff8e9}.legal-footer{padding:28px 0;border-top:1px solid var(--border);background:#fff;color:var(--muted);font-size:13px}.legal-footer-inner{width:min(920px,calc(100% - 36px));margin:auto;display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}.legal-footer a{color:var(--text)}@media(max-width:640px){.public-bar-inner{min-height:64px}.legal-shell{padding:42px 0 58px}}
    </style>
    @stack('styles')
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="public-bar"><div class="public-bar-inner"><a class="public-brand" href="{{ route('home') }}" aria-label="Accueil PNMG"><img src="{{ asset('images/pnmg/logo-horizontal-inverse.png') }}" alt="PNMG — Plateforme Nationale de Mobilité du Gabon" width="188" height="40"></a><a class="back-link" href="{{ route('home') }}">Retour à l’accueil</a></div></header>
<main class="legal-shell" id="contenu">@yield('content')</main>
<footer class="legal-footer"><div class="legal-footer-inner"><span>Plateforme Nationale de Mobilité du Gabon</span><span><a href="{{ route('legal.terms') }}">Conditions générales</a> · <a href="{{ route('legal.privacy') }}">Confidentialité</a></span></div></footer>
@include('layouts._loading')
</body>
</html>
