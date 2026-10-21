# Base de données PNMG — documentation de passation

**État documenté : 19 octobre 2026.** Source : export vérifié `pnmg_test_admin_2026-10-19.sql.gz` de MySQL 8.4.11 sur `novqx`, comparé aux migrations et aux modèles Laravel du code livré. L'export contient **24 tables** et les **27 migrations** présentes dans `database/migrations` ; aucune migration du code livré ne manque dans cet export. Il contient un compte administrateur et aucune compagnie. Il s'agit d'un état de la base de test, pas d'une description de données réelles en production.

## Sources de vérité et limites

- Les migrations `database/migrations/*.php` définissent les changements de schéma à rejouer dans l'ordre. Ce document décrit le résultat obtenu après leur application au 19 octobre 2026.
- Les modèles `app/Models` et les services `app/Services` portent des règles qui ne sont pas toutes garanties par des contraintes SQL. Il faut les conserver lors d'une reprise du code.
- Les listes créées par `database/seeders` sont des **propositions techniques**, en attente de validation métier. Elles ne doivent pas être présentées comme une nomenclature officielle.
- Le fichier historique `modelisations/back-end/specification/03_database.sql` date d'août 2026. Il ne décrit plus seul l'état courant de la base ; les migrations et le présent document priment pour la passation.
- Les fichiers téléversés ne résident **pas** dans MySQL : `file_path` stocke leur chemin sur le disque privé Laravel. Une restauration de données contenant des documents exige aussi la restauration des fichiers correspondants.

## Vue d'ensemble

| Domaine | Tables | Rôle |
| --- | --- | --- |
| Comptes et droits | `users`, `employees`, `custom_roles`, `platform_roles`, `role_presets` | Authentification, rattachement et permissions. |
| Compagnies et implantation | `companies`, `agencies`, `company_status_history` | Dossiers, agences, décisions et historique. |
| Transport et flotte | `transport_modes`, `company_transport_mode`, `fleet_units` | Référentiel et unités de flotte. |
| Documents | `document_types`, `company_documents`, `fleet_documents` | Types, fichiers, validité et revue. |
| Traçabilité | `audit_logs`, `notifications` | Événements d'audit et notifications. |
| Infrastructure Laravel | `migrations`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs` | Migration, session, cache et file d'attente. |

### Relations essentielles

- Une `company` possède plusieurs `agencies`, `employees`, `fleet_units`, `company_documents`, `users` et lignes de `company_status_history`.
- `company_transport_mode` relie les compagnies au référentiel `transport_modes` ; sa clé primaire composite empêche les doublons. La règle actuelle de l'application limite le choix métier à un mode par compagnie, mais le schéma pivot n'impose pas cette limite.
- Une `agency` appartient à une compagnie. `headquarters_for` désigne éventuellement la compagnie dont elle est le siège ; cette colonne est unique et peut être nulle. La cohérence entre `agency.company_id` et `headquarters_for` doit être respectée par l'application.
- Un `employee` appartient à une compagnie, peut être rattaché à une agence et peut avoir un compte `user`. `employees.user_id` est unique : au plus une fiche employé par compte. Le rattachement d'un agent à une agence passe par `employees.agency_id`, et non par une colonne `users.agency_id` (supprimée par la migration ADR-007).
- Une `fleet_unit` appartient à une compagnie et peut référencer un `transport_mode`. Les documents de compagnie et de flotte référencent leur propriétaire, un `document_type` et, après contrôle, un `user` réviseur.
- `custom_roles` sont propres à une compagnie ; `platform_roles` décrivent des rôles du personnel de la plateforme ; `role_presets` sont des modèles de permissions. Les droits réellement accordés dépendent du code de l'application en plus des colonnes SQL.

## Règles de gestion et contraintes importantes

### Compagnies

- `companies.rccm` est obligatoire et unique. La migration du 27 septembre normalise les valeurs existantes en supprimant les espaces superflus et en passant en majuscules avant d'ajouter l'unicité. `email` et `application_reference` sont également uniques ; la référence de demande peut être nulle.
- `companies.status` accepte `pending`, `validated`, `rejected`, `active`, `suspended`. Le service `CompanyStatusService` est le point d'écriture du statut : validation `pending → validated → active`, rejet, suspension, réactivation et renvoi d'un dossier corrigé. Chaque transition est inscrite dans `company_status_history` dans la même transaction. Le schéma SQL, à lui seul, n'interdit pas une transition invalide.
- `validated` est un état de transition journalisé avant `active`. Une compagnie ne doit pas rester durablement dans cet état par une écriture directe.
- Les tables `companies`, `agencies`, `employees` et `fleet_units` possèdent `deleted_at` pour la suppression logique. Les autres tables ne possèdent pas toutes ce mécanisme ; vérifier les effets avant une suppression physique.

### Comptes et documents

- `users.email` est unique. `users.password` contient une **empreinte**, jamais le mot de passe en clair. `users.role` est une chaîne de 30 caractères ; les rôles reconnus et leurs permissions sont déterminés par le code. `users.status` vaut `active` ou `inactive` dans le schéma actuel.
- Dans `company_documents` et `fleet_documents`, `status` (`valid`, `expired`, `to_renew`) concerne la **validité temporelle** ; `review_status` (`pending`, `approved`, `rejected` dans le code) concerne la **décision de contrôle**. Ces deux champs ne doivent pas être confondus. La base stocke `review_status` en chaîne, sans contrainte SQL sur ses trois valeurs ; le code doit les valider.
- `file_path` pointe vers un fichier du disque privé `local` de Laravel. Les colonnes `reviewed_by`, `reviewed_at` et `review_comment` gardent la dernière décision. Une correction de document refusé remet la revue à `pending`.
- Une compagnie ne peut être validée par le service que si ses documents exigés sont présents, non expirés et approuvés. `document_types.is_required` pilote cette vérification.

### Intégrité et suppression

- Plusieurs relations enfant → compagnie suppriment leurs lignes en cascade lors d'une **suppression physique** (`agencies`, `employees`, `fleet_units`, documents, historique, pivot). Les références d'audit et les comptes sont mises à `NULL` dans certains cas. La suppression logique d'une compagnie ne déclenche pas les cascades SQL.
- Les référentiels et rôles référencés utilisent souvent `RESTRICT` : une valeur utilisée ne peut pas être supprimée sans réaffectation préalable.
- `audit_logs.agency_id` est indexé mais n'a **pas** de clé étrangère dans le schéma observé. `audit_logs.subject_type`/`subject_id` et `notifications.notifiable_type`/`notifiable_id` sont des références polymorphes également sans clé étrangère SQL.
- `fleet_units.capacity` doit être strictement positive (`CHECK`). `fleet_units.registration_number` est unique lorsqu'il est renseigné.

## Données de référence initiales

| Source | Valeurs prévues | Statut |
| --- | --- | --- |
| `TransportModeSeeder` | `road`, `river`, `maritime`, `rail`, `air` | Proposition technique à valider. |
| `DocumentTypeSeeder` | Autorisation d'exploitation, Assurance, Attestation fiscale, Carte grise, Visite technique, Autorisation de circulation | Proposition technique à valider ; `applicable_to` et `is_required` précisent l'usage. |
| Migration `2026_09_15_000002` | Quatre `role_presets` de base | Créés par la migration. |
| Migration `2026_09_16_000001` | Quatre `platform_roles` de base | Créés par la migration. |

`DatabaseSeeder` appelle seulement les deux seeders de modes et de types de documents ; il ne crée aucun utilisateur. Sur une base déjà utilisée, vérifier les valeurs existantes avant de relancer un seeder.

## Exploitation et restauration

1. Pour connaître l'état d'un environnement : relever le commit installé, `php artisan migrate:status`, la version MySQL/MariaDB et la configuration de la base dans `.env` sans afficher le mot de passe.
2. Avant une mise à jour : sauvegarder **ensemble** la base et `storage/app/private`, calculer leurs empreintes et contrôler une restauration dans un environnement isolé. Les fichiers privés peuvent être absents d'une base de test volontairement vide de dossiers ; ce cas doit être explicite.
3. Sur une base existante : appliquer uniquement les migrations manquantes avec `php artisan migrate --force`. `migrate:fresh` détruit les tables et les données ; il ne convient qu'à une remise à zéro explicitement décidée.
4. Après restauration : contrôler les tables et migrations, le nombre de comptes par rôle, les clés étrangères, la connexion de l'administrateur et, si des dossiers ont été repris, l'ouverture d'au moins un document privé.
5. Ne jamais transmettre `.env`, le mot de passe MySQL, des empreintes de comptes ou des documents privés dans un dépôt public. L'export SQL de test reste sensible car il contient l'empreinte du mot de passe administrateur.

## Dictionnaire des tables et des colonnes

Les types, valeurs nulles, valeurs par défaut et relations ci-dessous proviennent du `CREATE TABLE` de l'export restauré. `—` signifie « aucune valeur par défaut explicite » ou « aucune note particulière ». Les valeurs de `enum` figurent dans la colonne Type. Les index composites et les actions de suppression sont détaillés dans les migrations ; les clés étrangères sont indiquées dans la dernière colonne.

### `companies`

Dossier administratif et état courant d'une compagnie.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `application_reference` | `varchar(30)` | Oui | `NULL` | Référence de suivi de la demande. Unique |
| `legal_name` | `varchar(150)` | Non | `—` | Raison sociale. |
| `trade_name` | `varchar(150)` | Oui | `NULL` | Nom commercial. |
| `rccm` | `varchar(50)` | Non | `—` | Numéro RCCM normalisé. Unique |
| `nif` | `varchar(50)` | Oui | `NULL` | Numéro d'identification fiscale. |
| `email` | `varchar(150)` | Non | `—` | Adresse de contact ou de connexion. Unique |
| `phone` | `varchar(30)` | Non | `—` | — |
| `address` | `varchar(255)` | Oui | `NULL` | — |
| `city` | `varchar(100)` | Oui | `NULL` | — |
| `status` | `enum('pending','validated','rejected','active','suspended')` | Non | `'pending'` | État courant. |
| `validated_at` | `timestamp` | Oui | `NULL` | Date de validation du dossier. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `deleted_at` | `timestamp` | Oui | `NULL` | Date de suppression logique. |
| `province` | `varchar(2)` | Oui | `NULL` | Code de province sur deux caractères. |

### `agencies`

Agences d'une compagnie et désignation éventuelle de son siège.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie propriétaire. FK → `companies.id` ; suppression CASCADE |
| `name` | `varchar(150)` | Non | `—` | Nom affiché. |
| `address` | `varchar(255)` | Oui | `NULL` | — |
| `city` | `varchar(100)` | Oui | `NULL` | — |
| `phone` | `varchar(30)` | Oui | `NULL` | — |
| `status` | `enum('active','inactive')` | Non | `'active'` | État courant. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `deleted_at` | `timestamp` | Oui | `NULL` | Date de suppression logique. |
| `headquarters_for` | `bigint unsigned` | Oui | `NULL` | Compagnie pour laquelle cette agence est le siège. Unique FK → `companies.id` ; suppression RESTRICT |
| `province` | `varchar(2)` | Oui | `NULL` | Code de province sur deux caractères. |

### `users`

Comptes de connexion, rôle principal et rattachement aux droits.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `name` | `varchar(191)` | Non | `—` | Nom affiché. |
| `email` | `varchar(191)` | Non | `—` | Adresse de contact ou de connexion. Unique |
| `email_verified_at` | `timestamp` | Oui | `NULL` | — |
| `password` | `varchar(191)` | Non | `—` | Empreinte du mot de passe. |
| `role` | `varchar(30)` | Non | `—` | Rôle principal interprété par l'application. |
| `company_id` | `bigint unsigned` | Oui | `NULL` | Compagnie de rattachement, si applicable. FK → `companies.id` ; suppression SET NULL |
| `status` | `enum('active','inactive')` | Non | `'active'` | État courant. |
| `remember_token` | `varchar(100)` | Oui | `NULL` | Jeton de connexion persistante. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `custom_role_id` | `bigint unsigned` | Oui | `NULL` | Rôle propre à une compagnie. FK → `custom_roles.id` ; suppression RESTRICT |
| `platform_role_id` | `bigint unsigned` | Oui | `NULL` | Rôle du personnel de la plateforme. FK → `platform_roles.id` ; suppression RESTRICT |

### `employees`

Fiches du personnel des compagnies ; rattachement éventuel à une agence et à un compte.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie propriétaire. FK → `companies.id` ; suppression CASCADE |
| `agency_id` | `bigint unsigned` | Oui | `NULL` | Agence de rattachement éventuelle. FK → `agencies.id` ; suppression SET NULL |
| `user_id` | `bigint unsigned` | Oui | `NULL` | Compte lié à la fiche, au plus un. Unique FK → `users.id` ; suppression SET NULL |
| `last_name` | `varchar(100)` | Non | `—` | — |
| `first_name` | `varchar(100)` | Non | `—` | — |
| `internal_role` | `varchar(100)` | Oui | `NULL` | — |
| `status` | `enum('active','inactive')` | Non | `'active'` | État courant. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `deleted_at` | `timestamp` | Oui | `NULL` | Date de suppression logique. |

### `fleet_units`

Unités de flotte déclarées par les compagnies.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie propriétaire. FK → `companies.id` ; suppression CASCADE |
| `type` | `varchar(50)` | Non | `—` | Type d'unité. |
| `registration_number` | `varchar(50)` | Oui | `NULL` | Numéro d'immatriculation éventuel. Unique |
| `capacity` | `smallint unsigned` | Non | `—` | Capacité positive. |
| `availability` | `enum('available','unavailable')` | Non | `'available'` | Disponibilité opérationnelle. |
| `status` | `enum('active','inactive')` | Non | `'active'` | État courant. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `deleted_at` | `timestamp` | Oui | `NULL` | Date de suppression logique. |
| `transport_mode_id` | `bigint unsigned` | Oui | `NULL` | Mode de transport de l'unité. FK → `transport_modes.id` ; suppression RESTRICT |

### `company_documents`

Pièces administratives d'une compagnie et dernière décision de revue.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie propriétaire. FK → `companies.id` ; suppression CASCADE |
| `document_type_id` | `bigint unsigned` | Non | `—` | Type de pièce. FK → `document_types.id` ; suppression RESTRICT |
| `file_path` | `varchar(255)` | Non | `—` | Chemin du fichier sur le disque privé Laravel. |
| `issued_at` | `date` | Oui | `NULL` | Date de délivrance. |
| `valid_until` | `date` | Non | `—` | Date de fin de validité. |
| `status` | `enum('valid','expired','to_renew')` | Non | `'valid'` | Validité temporelle du document. |
| `review_status` | `varchar(20)` | Non | `'pending'` | Décision de contrôle indépendante de la validité. |
| `reviewed_by` | `bigint unsigned` | Oui | `NULL` | Compte ayant pris la dernière décision. FK → `users.id` ; suppression SET NULL |
| `reviewed_at` | `timestamp` | Oui | `NULL` | Date de la dernière décision. |
| `review_comment` | `text` | Oui | `—` | Motif ou commentaire de revue. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |

### `fleet_documents`

Pièces associées à une unité de flotte et dernière décision de revue.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `fleet_unit_id` | `bigint unsigned` | Non | `—` | Unité de flotte propriétaire. FK → `fleet_units.id` ; suppression CASCADE |
| `document_type_id` | `bigint unsigned` | Non | `—` | Type de pièce. FK → `document_types.id` ; suppression RESTRICT |
| `file_path` | `varchar(255)` | Non | `—` | Chemin du fichier sur le disque privé Laravel. |
| `issued_at` | `date` | Oui | `NULL` | Date de délivrance. |
| `valid_until` | `date` | Non | `—` | Date de fin de validité. |
| `status` | `enum('valid','expired','to_renew')` | Non | `'valid'` | Validité temporelle du document. |
| `review_status` | `varchar(20)` | Non | `'pending'` | Décision de contrôle indépendante de la validité. |
| `reviewed_by` | `bigint unsigned` | Oui | `NULL` | Compte ayant pris la dernière décision. FK → `users.id` ; suppression SET NULL |
| `reviewed_at` | `timestamp` | Oui | `NULL` | Date de la dernière décision. |
| `review_comment` | `text` | Oui | `—` | Motif ou commentaire de revue. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |

### `document_types`

Référentiel des types de pièces et caractère obligatoire.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `label` | `varchar(100)` | Non | `—` | Unique |
| `applicable_to` | `enum('company','fleet_unit','both')` | Non | `—` | Périmètre : compagnie, flotte ou les deux. |
| `is_required` | `tinyint(1)` | Non | `'0'` | Obligation lors de la validation. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |

### `transport_modes`

Référentiel des modes de transport.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `label` | `varchar(50)` | Non | `—` | Unique |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |

### `company_transport_mode`

Association entre compagnies et modes de transport.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie associée. FK → `companies.id` ; suppression CASCADE |
| `transport_mode_id` | `bigint unsigned` | Non | `—` | Mode associé. FK → `transport_modes.id` ; suppression CASCADE |

Clé primaire composite : `company_id` + `transport_mode_id`.

### `company_status_history`

Historique des changements de statut des compagnies.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie propriétaire. FK → `companies.id` ; suppression CASCADE |
| `previous_status` | `enum('pending','validated','rejected','active','suspended')` | Oui | `NULL` | État avant transition. |
| `new_status` | `enum('pending','validated','rejected','active','suspended')` | Non | `—` | État après transition. |
| `reason` | `varchar(255)` | Oui | `NULL` | Motif de la décision. |
| `decided_by` | `bigint unsigned` | Oui | `NULL` | Compte décideur ; nul pour une transition système. FK → `users.id` ; suppression SET NULL |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |

### `custom_roles`

Rôles paramétrés pour une compagnie.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `company_id` | `bigint unsigned` | Non | `—` | Compagnie propriétaire. FK → `companies.id` ; suppression RESTRICT |
| `name` | `varchar(100)` | Non | `—` | Nom affiché. |
| `base_role` | `varchar(30)` | Non | `—` | Rôle principal dont part la personnalisation. |
| `permissions` | `json` | Non | `—` | Liste JSON des permissions. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `scope` | `varchar(20)` | Oui | `NULL` | Portée du rôle libre. |

Unicité composite : `company_id` + `name`.

### `platform_roles`

Rôles et permissions des agents de la plateforme.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `name` | `varchar(100)` | Non | `—` | Nom affiché. Unique |
| `permissions` | `json` | Non | `—` | Liste JSON des permissions. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |

### `role_presets`

Modèles de rôles de référence livrés par migration.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `code` | `varchar(191)` | Non | `—` | Code stable du modèle. Unique |
| `name` | `varchar(191)` | Non | `—` | Nom affiché. |
| `description` | `text` | Non | `—` | — |
| `scope` | `varchar(191)` | Non | `—` | Portée du modèle. |
| `permissions` | `json` | Non | `—` | Liste JSON des permissions. |

### `audit_logs`

Journal applicatif des actions et objets concernés.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `user_id` | `bigint unsigned` | Oui | `NULL` | Compte associé. FK → `users.id` ; suppression SET NULL |
| `company_id` | `bigint unsigned` | Oui | `NULL` | Compagnie propriétaire. FK → `companies.id` ; suppression SET NULL |
| `action` | `varchar(40)` | Non | `—` | Code de l'action. |
| `subject_type` | `varchar(191)` | Oui | `NULL` | Type de l'objet audité. |
| `subject_id` | `bigint unsigned` | Oui | `NULL` | Identifiant de l'objet audité. |
| `subject_label` | `varchar(191)` | Oui | `NULL` | Libellé de l'objet au moment de l'action. |
| `description` | `text` | Non | `—` | — |
| `changed_fields` | `json` | Oui | `NULL` | Champs modifiés au format JSON. |
| `ip_address` | `varchar(45)` | Oui | `NULL` | Adresse IP observée. |
| `user_agent` | `text` | Oui | `—` | Agent logiciel observé. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |
| `agency_id` | `bigint unsigned` | Oui | `NULL` | Agence liée à l'événement, sans FK SQL. |

### `notifications`

Notifications Laravel stockées en base.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `char(36)` | Non | `—` | Identifiant technique. PK |
| `type` | `varchar(191)` | Non | `—` | — |
| `notifiable_type` | `varchar(191)` | Non | `—` | Type du destinataire polymorphe. |
| `notifiable_id` | `bigint unsigned` | Non | `—` | Identifiant du destinataire. |
| `data` | `text` | Non | `—` | Contenu sérialisé de la notification. |
| `read_at` | `timestamp` | Oui | `NULL` | Date de lecture. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |
| `updated_at` | `timestamp` | Oui | `NULL` | Date de dernière modification. |

### `migrations`

Historique des migrations appliquées.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `int unsigned` | Non | `—` | Identifiant technique. PK |
| `migration` | `varchar(191)` | Non | `—` | Nom de migration appliquée. |
| `batch` | `int` | Non | `—` | Lot d'exécution. |

### `password_reset_tokens`

Jetons temporaires de réinitialisation de mot de passe.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `email` | `varchar(191)` | Non | `—` | Adresse de contact ou de connexion. PK |
| `token` | `varchar(191)` | Non | `—` | Empreinte du jeton de réinitialisation. |
| `created_at` | `timestamp` | Oui | `NULL` | Date de création. |

### `sessions`

Sessions applicatives lorsque le stockage en base est activé.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `varchar(191)` | Non | `—` | Identifiant technique. PK |
| `user_id` | `bigint unsigned` | Oui | `NULL` | Compte associé. |
| `ip_address` | `varchar(45)` | Oui | `NULL` | — |
| `user_agent` | `text` | Oui | `—` | — |
| `payload` | `longtext` | Non | `—` | Contenu de session sérialisé. |
| `last_activity` | `int` | Non | `—` | Dernière activité (temps Unix). |

### `cache`

Entrées de cache lorsque le pilote base de données est utilisé.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `key` | `varchar(191)` | Non | `—` | Clé de cache. PK |
| `value` | `mediumtext` | Non | `—` | Valeur sérialisée. |
| `expiration` | `bigint` | Non | `—` | Date d'expiration (temps Unix). |

### `cache_locks`

Verrous de cache lorsque le pilote base de données est utilisé.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `key` | `varchar(191)` | Non | `—` | Clé du verrou. PK |
| `owner` | `varchar(191)` | Non | `—` | Propriétaire du verrou. |
| `expiration` | `bigint` | Non | `—` | Date d'expiration (temps Unix). |

### `jobs`

Travaux en attente lorsque la file est stockée en base.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `queue` | `varchar(191)` | Non | `—` | — |
| `payload` | `longtext` | Non | `—` | Charge du travail. |
| `attempts` | `smallint unsigned` | Non | `—` | Nombre de tentatives. |
| `reserved_at` | `int unsigned` | Oui | `NULL` | Réservation (temps Unix). |
| `available_at` | `int unsigned` | Non | `—` | Disponibilité (temps Unix). |
| `created_at` | `int unsigned` | Non | `—` | Date de création. |

### `job_batches`

Suivi des lots de travaux.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `varchar(191)` | Non | `—` | Identifiant technique. PK |
| `name` | `varchar(191)` | Non | `—` | Nom affiché. |
| `total_jobs` | `int` | Non | `—` | Nombre initial de travaux. |
| `pending_jobs` | `int` | Non | `—` | Travaux restants. |
| `failed_jobs` | `int` | Non | `—` | Travaux échoués. |
| `failed_job_ids` | `longtext` | Non | `—` | — |
| `options` | `mediumtext` | Oui | `—` | — |
| `cancelled_at` | `int` | Oui | `NULL` | — |
| `created_at` | `int` | Non | `—` | Date de création. |
| `finished_at` | `int` | Oui | `NULL` | — |

### `failed_jobs`

Travaux de file ayant échoué.

| Colonne | Type SQL | NULL | Défaut | Sens / contrainte |
| --- | --- | :---: | --- | --- |
| `id` | `bigint unsigned` | Non | `—` | Identifiant technique. PK |
| `uuid` | `varchar(191)` | Non | `—` | Identifiant unique du travail. Unique |
| `connection` | `varchar(191)` | Non | `—` | — |
| `queue` | `varchar(191)` | Non | `—` | — |
| `payload` | `longtext` | Non | `—` | — |
| `exception` | `longtext` | Non | `—` | Erreur de la dernière exécution. |
| `failed_at` | `timestamp` | Non | `CURRENT_TIMESTAMP` | Date d'échec. |

