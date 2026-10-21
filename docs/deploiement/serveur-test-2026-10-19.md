# Mise à jour du serveur de test PNMG

Version candidate préparée le 19 octobre 2026 pour la recette avant la soutenance du 2 novembre. Le fichier `PNMG_serveur_test_2026-10-19.zip` contient le code actuel, les logos, les ressources compilées et un manifeste SHA-256. Il ne contient ni `.env`, ni dépendances installées, ni base de données, ni documents privés, ni sessions.

La structure actuelle de la base, ses 24 tables, leurs relations et leurs règles de gestion sont documentées dans [base-de-donnees-2026-10-19.md](../base-de-donnees-2026-10-19.md), inclus dans l'archive du code.

## Avant l'installation

1. Confirmer l'adresse du serveur, son accès SSH, l'URL HTTPS et le répertoire réel de l'application. Les anciennes coordonnées du 16 septembre ne sont pas confirmées.
2. Vérifier PHP 8.3 ou plus récent avec les extensions requises par Composer, MySQL/MariaDB, Nginx, Composer et l'espace disque. La racine web doit pointer vers `public/`.
3. Relever la version installée, les migrations appliquées et la configuration du worker de file d'attente et du planificateur. Conserver ces observations dans la fiche de recette.
4. Sauvegarder séparément le code actuel, la base et `storage/app/private`. Protéger les sauvegardes et noter leur emplacement et leurs empreintes. Prévoir un environnement isolé pour vérifier la restauration.
5. Confirmer que le fichier `.env` du serveur contient une clé `APP_KEY` propre à cet environnement, `APP_ENV=production`, `APP_DEBUG=false`, l'URL HTTPS correcte et les paramètres de base de données. Conserver la clé actuelle lors d'une mise à jour. En test, envoyer les courriels vers un canal de test ou le journal.

## Reprendre la base de l'Ubuntu local

La base du serveur Ubuntu local est la **source** à transférer vers le serveur de test de l'entreprise. Une copie complète conserve les utilisateurs, leurs rôles et les empreintes de leurs mots de passe, donc le compte administrateur actuel. Les documents téléversés résident séparément dans `storage/app/private` : les copier avec la base pour que les liens enregistrés restent utilisables. Ne pas transférer les sessions ni le fichier `.env` de la source. Le serveur de test conserve sa propre configuration et sa propre clé `APP_KEY` ; vérifier auparavant que la base ne contient aucune valeur chiffrée par l'application qui dépendrait de la clé source.

Sur l'Ubuntu source, confirmer d'abord le nom réel de la base dans `.env`, le moteur et sa version, la version du code et les migrations appliquées. Placer les sauvegardes dans un répertoire privé hors de `public/` et arrêter les écritures applicatives pendant le cliché de la base et des fichiers. Pour MySQL, utiliser `mysqldump --single-transaction --quick --routines --triggers --events --no-tablespaces` avec demande interactive du mot de passe ; ne jamais inscrire ce dernier dans une commande ou dans le dépôt. Archiver `storage/app/private`, calculer les empreintes SHA-256 des deux fichiers et limiter leur accès au propriétaire. La cohérence de `--single-transaction` suppose des tables transactionnelles et l'absence de modification de schéma pendant l'export.

Transférer les deux fichiers par SSH vers un espace privé du serveur de test. Avant toute substitution, sauvegarder la base et les fichiers privés déjà présents sur ce serveur. Restaurer la copie source dans une **nouvelle base isolée**, vérifier le nombre de lignes, les migrations, la présence du compte admin sans afficher son empreinte ni son mot de passe, ainsi que l'ouverture d'un document. Faire pointer ensuite le `.env` de test vers cette base, appliquer seulement les migrations manquantes pour la version du code installée et reconstruire les caches. Vérifier la connexion réelle de l'admin, les rôles et un parcours métier. Conserver l'ancienne base pour un retour arrière. Adapter les commandes d'export et d'import au moteur et aux versions effectivement relevés, en particulier si MySQL et MariaDB diffèrent entre les deux serveurs.

## Installation

Faire l'installation dans une fenêtre de maintenance avec accès aux sauvegardes. Exemple pour l'installation historique `/var/www/pnmg`, à adapter seulement après confirmation de l'hôte :

```bash
unzip -t PNMG_serveur_test_2026-10-19.zip
mkdir -p /tmp/pnmg-release-2026-10-19
unzip -q PNMG_serveur_test_2026-10-19.zip -d /tmp/pnmg-release-2026-10-19

cd /var/www/pnmg
php artisan down --retry=60
rsync -a --exclude='.env' --exclude='storage/' --exclude='vendor/' \
  /tmp/pnmg-release-2026-10-19/ /var/www/pnmg/
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
php artisan optimize:clear
php artisan migrate:status
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

Les fichiers de `public/build` sont déjà compilés et inclus. Vérifier que le serveur web peut écrire dans `storage/` et `bootstrap/cache/`, puis recharger le service PHP-FPM réellement installé. Vérifier le worker de file d'attente et le planificateur si leurs tâches sont activées. Ne pas lancer `key:generate` sur une installation existante. Ne pas écraser les documents privés ni le `.env` du serveur.

Les valeurs des modes de transport et des types de documents dans les seeders restent des propositions techniques. Ne pas relancer les seeders sur une base existante sans vérifier les valeurs attendues avec le responsable métier.

## Recette sur la version installée

- Vérifier `/up`, la vitrine, la connexion, l'inscription et le suivi public d'une demande en HTTPS, sur ordinateur et sur téléphone.
- Créer une demande avec des données de test ; contrôler le document, activer la compagnie, puis vérifier agences, employés et comptes, flotte, suspension et réactivation.
- Contrôler les permissions par rôle, les erreurs 403/404/500, les journaux et l'absence de données sensibles dans les réponses publiques.
- Exécuter la suite de tests sur une **base de test isolée** MySQL/MariaDB, notamment le verrouillage concurrent ignoré sous SQLite. Ne jamais pointer les tests automatisés vers la base de recette contenant des dossiers à conserver.
- Restaurer une sauvegarde de la base et des fichiers privés dans un environnement isolé. Vérifier l'intégrité des fichiers et rejouer au moins un parcours fonctionnel. Consigner la date, la version, les résultats et les anomalies.
- Après corrections, noter la version exacte et figer le jeu de chiffres de recette utilisé dans le mémoire, sa conclusion et le Tableau 4.

## Retour arrière

Si une migration ou un parcours essentiel échoue, remettre l'application en maintenance et restaurer **ensemble** le code, la base et les fichiers privés depuis les sauvegardes prises avant l'installation. Vérifier les parcours essentiels avant de rouvrir l'accès. Ne pas se limiter à `migrate:rollback`, car les migrations et les données peuvent avoir changé ensemble.

## État de la candidate locale

- 177 tests lancés sous SQLite : 176 réussis, 1 ignoré (verrouillage concurrent), 1 031 assertions.
- 177 tests réussis sur une base MariaDB temporaire isolée : 1 032 assertions, dont le contrôle de verrouillage concurrent. La base temporaire a été supprimée après le test.
- Compilation des ressources, compilation des vues, contrôle de style PHP et audit Composer réussis.
- Audit npm initial : trois avis ; dépendances indirectes corrigées, nouvel audit : aucune vulnérabilité signalée.
- Les migrations locales sont appliquées. La recette sur le serveur et la restauration restent à réaliser après installation.
- L'archive est un instantané du dossier de travail, pas encore un commit Git de passation. Son manifeste énumère chaque fichier et son empreinte SHA-256.

## Sauvegarde source et essai de restauration — 19 octobre 2026

Sur l'Ubuntu local `novqx`, la base source `pnmg` sous MySQL 8.4.11 a été exportée dans un fichier `pnmg.sql.gz` et les documents privés dans `private.tar.gz`. Les deux fichiers ont passé `sha256sum -c SHA256SUMS`. Le dump a été importé dans une **nouvelle base locale isolée** `pnmg_restore_20261019_140449` : 24 tables et un compte administrateur ont été constatés. L'archive des documents a été extraite dans un répertoire de vérification distinct : 9 fichiers retrouvés. La base `pnmg` et les documents en service n'ont pas été remplacés par ce test.

La sauvegarde se trouve encore sur la même machine Ubuntu. Il reste à en faire une copie privée hors de cette machine, à retirer les copies temporaires de vérification après contrôle, puis à répéter la restauration et la recette sur le serveur de test de l'entreprise dès que ses accès seront fournis. Ces contrôles locaux prouvent que les archives se lisent ; ils ne valident pas encore le déploiement ni la connexion fonctionnelle de l'administrateur sur le serveur cible.

## Export de livraison avec administrateur seul — 19 octobre 2026

La base active `pnmg` sur `novqx` a été réinitialisée. Le contrôle transmis après recréation indique un utilisateur, un administrateur `raphsadibi@admin.com` et aucune compagnie. L'application a été remise en service. L'export `/home/no/backups/pnmg_test_admin_2026-10-19.sql.gz` a passé `gzip -t` et a été restauré dans une base locale isolée : 24 tables, un utilisateur, un administrateur et aucune compagnie. Son empreinte SHA-256 est `b79ca0239432c53adc06778535c7b0e1a30a7f950ee15f356e601256f589ae5e`.

Le mot de passe provisoire a été remplacé sur la VM avant l'export et une nouvelle connexion avec le mot de passe final a réussi, selon la vérification de l'administrateur. La copie de cet export vers Windows et la vérification de son empreinte après transfert restent à faire. La sauvegarde complète antérieure est conservée séparément pour retour arrière et ne fait pas partie de la livraison allégée.
