@extends('layouts.public')
@section('title', 'Conditions générales d’utilisation')
@section('meta_description', 'Conditions générales d’utilisation de la Plateforme Nationale de Mobilité du Gabon.')

@section('content')
<header class="legal-intro">
    <h1>Conditions générales d’utilisation</h1>
    <p>Ces conditions encadrent l’accès à la Plateforme Nationale de Mobilité du Gabon et son utilisation par les compagnies de transport, leurs représentants et les agents habilités.</p>
    <div class="legal-meta">Version en vigueur : 23 septembre 2026</div>
</header>

<div class="legal-note"><strong>À retenir :</strong> l’utilisation de la plateforme implique de fournir des informations exactes, de protéger ses accès et de ne déposer que des documents licites et autorisés.</div>

<h2>1. Objet du service</h2>
<p>La PNMG permet l’enregistrement administratif des compagnies de transport, le dépôt et le suivi de leurs pièces justificatives, ainsi que la gestion des agences, employés, flottes, rôles et habilitations. La plateforme accompagne l’instruction des dossiers ; elle ne garantit pas leur acceptation.</p>

<h2>2. Accès et comptes</h2>
<p>L’accès aux fonctions privées nécessite un compte nominatif. Chaque utilisateur est responsable de la confidentialité de ses identifiants et des opérations réalisées depuis son compte. Le partage de compte est interdit. Toute suspicion d’accès non autorisé doit être signalée sans délai à l’administration de la plateforme.</p>

<h2>3. Exactitude des informations</h2>
<p>La compagnie et son représentant garantissent que les données transmises sont exactes, complètes et à jour. Ils s’engagent à corriger toute information devenue inexacte et à disposer des droits nécessaires pour communiquer les données personnelles et documents déposés.</p>

<h2>4. Documents téléversés</h2>
<p>Seuls les formats et tailles indiqués dans l’interface sont acceptés. Les fichiers peuvent faire l’objet de contrôles techniques et administratifs. Il est interdit de déposer un fichier malveillant, falsifié, illicite ou sans lien avec la démarche. Un document peut être refusé ou demandé de nouveau lorsqu’il est illisible, expiré, incohérent ou non conforme.</p>

<h2>5. Rôles et habilitations</h2>
<p>Les droits d’accès dépendent du rôle attribué et du périmètre de la compagnie ou de l’administration. Les responsables doivent limiter les habilitations au strict nécessaire et les retirer lorsqu’elles ne sont plus justifiées. Les opérations sensibles peuvent être journalisées et soumises à une vérification renforcée.</p>

<h2>6. Sécurité et usages interdits</h2>
<p>Il est interdit de contourner les contrôles d’accès, tester la sécurité sans autorisation, automatiser des requêtes abusives, extraire massivement des données, perturber le service ou usurper l’identité d’un tiers. La plateforme peut limiter, suspendre ou fermer un accès en cas de risque de sécurité, de fraude ou de non-respect des présentes conditions.</p>

<h2>7. Disponibilité du service</h2>
<p>La plateforme est exploitée avec un objectif de continuité et de sécurité. Des interruptions peuvent néanmoins survenir pour maintenance, évolution, incident ou cause extérieure. Lorsqu’une échéance administrative est impérative, l’utilisateur doit conserver une copie de ses justificatifs et suivre les éventuelles instructions officielles alternatives.</p>

<h2>8. Propriété et réutilisation</h2>
<p>Les éléments propres à la PNMG — structure, identité visuelle, textes et composants — ne peuvent pas être reproduits ou réutilisés hors des exceptions prévues par le droit applicable ou sans autorisation. L’utilisateur conserve les droits qu’il détient sur les informations et documents qu’il transmet.</p>

<h2>9. Données personnelles</h2>
<p>Les traitements de données liés au service sont décrits dans la <a href="{{ route('legal.privacy') }}">politique de confidentialité</a>, qui précise notamment les finalités, destinataires, durées de conservation et droits des personnes.</p>

<h2>10. Évolution des conditions</h2>
<p>Les présentes conditions peuvent être adaptées pour tenir compte d’une évolution du service, de la sécurité ou du cadre applicable. La date de version affichée en tête de page permet d’identifier le texte en vigueur.</p>

<h2>11. Droit applicable et contact</h2>
<p>Les présentes conditions sont régies par le droit applicable au Gabon. Toute demande relative au service ou à ces conditions doit être adressée à l’administration de la PNMG par les coordonnées officielles communiquées dans l’espace utilisateur ou par l’autorité de tutelle.</p>
@endsection
