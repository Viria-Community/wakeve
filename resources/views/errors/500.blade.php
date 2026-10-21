<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('images/pnmg/icone-couleur.svg') }}" type="image/svg+xml">
    <title>Service temporairement indisponible — PNMG</title>
    @include('layouts._palette')
    <style>
        * { box-sizing: border-box; }
        body { display: grid; place-items: center; min-height: 100vh; margin: 0; padding: 24px; background: var(--dark); color: var(--text); font-family: "Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif; }
        main { width: min(680px, 100%); padding: clamp(28px, 6vw, 54px); border-left: 8px solid var(--gold); background: var(--surface); }
        .code { color: var(--green); font-weight: 800; letter-spacing: .14em; }
        h1 { margin: 18px 0 12px; font: 700 clamp(34px, 7vw, 56px)/1.1 Corbel, Calibri, "Segoe UI", Arial, sans-serif; }
        p { max-width: 520px; line-height: 1.6; }
        a { display: inline-flex; align-items: center; min-height: 44px; margin-top: 12px; padding: 10px 18px; border-radius: 8px; background: var(--green); color: var(--surface); font-weight: 700; text-decoration: none; }
        a:hover { background: var(--green-hover); }
        a:focus-visible { outline: var(--pnmg-focus); outline-offset: var(--pnmg-focus-offset); }
    </style>
</head>
<body>
<main>
    <p class="code">ERREUR 500</p>
    <h1>Le service est momentanément indisponible.</h1>
    <p>Nous ne pouvons pas afficher cette page pour le moment. Réessayez dans quelques instants.</p>
    <a href="{{ route('home') }}">Retour à l’accueil</a>
</main>
</body>
</html>
