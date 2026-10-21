<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="La page demandée est introuvable sur la PNMG.">
    <link rel="icon" href="{{ asset('images/pnmg/icone-couleur.svg') }}" type="image/svg+xml">
    <title>Page introuvable — PNMG</title>
    @include('layouts._palette')
    <style>*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:var(--dark);color:#fff;font-family:"Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif}.error-shell{width:min(680px,100%);padding:54px;border-left:8px solid var(--gold);background:#fff;color:var(--text)}.code{margin:0;color:var(--green);font:700 16px/1 Corbel, Calibri, "Segoe UI", Arial, sans-serif;letter-spacing:.18em}h1{margin:18px 0 12px;font:700 clamp(36px,8vw,62px)/1.05 Corbel, Calibri, "Segoe UI", Arial, sans-serif;letter-spacing:-.035em}p{max-width:540px;margin:0 0 28px;color:var(--muted);line-height:1.65}.actions{display:flex;gap:12px;flex-wrap:wrap}.button{display:inline-flex;min-height:46px;align-items:center;padding:10px 18px;border:1px solid var(--green);background:var(--green);color:#fff;font-weight:700;text-decoration:none}.button.secondary{background:#fff;color:var(--green)}@media(max-width:560px){.error-shell{padding:36px 25px}}</style>
</head>
<body>
<main class="error-shell"><p class="code">ERREUR 404</p><h1>Cette page n’existe pas.</h1><p>Le lien est peut-être incomplet ou la page a été déplacée. Revenez à l’accueil ou connectez-vous à votre espace.</p><div class="actions"><a class="button" href="{{ route('home') }}">Retour à l’accueil</a><a class="button secondary" href="{{ route('login') }}">Se connecter</a></div></main>
</body>
</html>
