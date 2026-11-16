Voici les fichiers créés ou modifiés pour le module Offre, classés par rôle. 

## Base de données (migrations)
- `database/migrations/2026_11_06_074755_create_places_table.php`
- `database/migrations/2026_11_06_074757_create_lines_table.php`
- `database/migrations/2026_11_06_074758_create_line_places_table.php`
- `database/migrations/2026_11_06_074800_create_departures_table.php`
- `database/migrations/2026_11_06_074801_create_fares_table.php`

## Modèles
- `app/Models/Place.php` (lieux)
- `app/Models/Line.php` (lignes)
- `app/Models/Departure.php` (départs)
- `app/Models/Fare.php` (tarifs)

## Contrôleurs
- `app/Http/Controllers/LineController.php`
- `app/Http/Controllers/DepartureController.php`
- `app/Http/Controllers/FareController.php`
- `app/Http/Controllers/SearchController.php`
- `app/Http/Controllers/DashboardOffreController.php`
- `app/Http/Controllers/PlaceController.php`

## Validation (FormRequests)
- Lignes : `StoreLineRequest`, `UpdateLineRequest`, `UpdateLineStatusRequest`
- Départs : `StoreDepartureRequest`, `UpdateDepartureRequest`, `CancelDepartureRequest`
- Tarifs : `StoreFareRequest`, `UpdateFareRequest`, `UpdateFareStatusRequest`

## Droits d'accès (policies)
- `app/Policies/LinePolicy.php`
- `app/Policies/DeparturePolicy.php`
- `app/Policies/FarePolicy.php`
- `app/Policies/CompanyPolicy.php` (modifié : restriction de la modification au gestionnaire, commit séparé)

## Vues (écrans)
- Lignes : `lines/index`, `create`, `edit`, `show`
- Départs : `departures/index`, `create`, `edit`, `show`
- Tarifs : `fares/index`, `create`, `edit`
- Recherche : `search/index`
- Tableau de bord : `dashboard-offre/index`
- Style : `layouts/_offre-icons.blade.php`

## Fichiers modifiés (existants)
- `routes/web.php` : routes `/offre/...` ajoutées
- `resources/views/layouts/_sidebar.blade.php` : menu « Offre de transport » déroulant
- `resources/views/layouts/app.blade.php` : inclusion du style Offre

## Données et tests
- Factories : `PlaceFactory`, `LineFactory`, `DepartureFactory`, `FareFactory`
- Seeders : `OffreTestSeeder`, `OffreTestManagerSeeder` (test), `OffreDemoSeeder` (démonstration)
- `phpunit.xml` et `tests/` (tests incomplets, à refaire proprement)

**Points à nettoyer avant la livraison :**
- les doublons de migrations à la racine du projet (`2026_11_06_..._create_line_place_table.php`, etc.), à supprimer ;
- les seeders de test, à exclure de la livraison (ils contiennent des mots de passe de démonstration) ;
- les tests : le dossier `tests/` a été réécrit plusieurs fois, il ne contient pas encore de test complet.

**Pour la liste exacte** :
```
cd ~/projets/module-offre && git diff --stat 17a1510 HEAD -- app database resources routes tests phpunit.xml | tail -80
```

Structure générale

Le module suit le modèle MVC (Modèle, Vue, Contrôleur) de Laravel :

Modèles : représentent les données et leurs relations.
Contrôleurs : reçoivent la requête, vérifient les droits, appellent la validation, enregistrent.
Vues : affichent les écrans (Blade).
Modèles : relations et méthodes
belongsTo : une ligne appartient à une compagnie, un départ à une ligne (company(), line()).
hasMany : une ligne a plusieurs départs et tarifs (departures(), fares()).
belongsToMany avec pivot : les points desservis, avec leur ordre (places() avec withPivot('stop_order')).
scopeActive : filtre les lignes actives (Line::active()).
isActive() et isScheduled() : tests d’état simples.
modeCoherent() : vérifie que le mode de la ligne fait partie des modes de la compagnie (règle RM-009).
Contrôleurs : les actions

Chaque contrôleur suit les mêmes actions CRUD :

index() : liste (avec filtres).
create() : affiche le formulaire de création.
store() : enregistre une nouvelle ligne, un départ ou un tarif.
show() : affiche la fiche.
edit() : affiche le formulaire de modification.
update() : enregistre la modification.
updateStatus() et cancel() : changent l’état (désactivation, annulation).

Pour la création d’une ligne, store() utilise une transaction (DB::transaction) : la ligne et ses points desservis sont enregistrés ensemble, ou pas du tout.

Validation : FormRequests

Chaque saisie passe par une classe dédiée :

rules() : les règles de champ (obligatoire, entier, existe dans la table).
messages() : les messages en français.
prepareForValidation() : prépare les données avant contrôle (compagnie prise sur l’utilisateur connecté, ordre des arrêts déduit de la position).
withValidator() : les règles métier, par exemple origine différente de destination.
authorize() : vérifie que l’utilisateur a le droit de faire l’action.
Droits d’accès : policies
Gate::authorize('update', $line) : vérifie la policy avant l’action.
viewAny, view, create, update : les méthodes de chaque policy (LinePolicy, DeparturePolicy, FarePolicy).
Le contrôle de périmètre est fait dans la policy : un gestionnaire ne voit et ne modifie que sa compagnie.
Routes
Route::get, post, put, patch : chaque action a son verbe HTTP. L’annulation est un PATCH, la mise à jour un PUT.
Route::resource pour les routes standard, et des routes nommées (lines.index, departures.cancel) utilisées dans les vues avec route().
Vues : Blade
@extends et @section('content') : héritage de la mise en page.
@forelse / @empty : affichage d’une liste, avec message si elle est vide.
@can : n’affiche un bouton que si l’utilisateur a le droit.
@csrf et @method('PATCH') : protection des formulaires et verbe HTTP correct.
@error : affiche le message de validation sous le champ.
Sécurité
Jeton CSRF sur tous les formulaires.
Requêtes paramétrées (Eloquent), donc pas d’injection SQL.
Suppression logique (deleted_at, statut inactive) : aucune donnée n’est effacée physiquement, conformément à RM-011.