# ADR-001 : Choix de PostgreSQL et modèle d'inventaire

## Statut
Accepté

## Contexte
Le CdC impose PostgreSQL 16+ avec PostGIS (§3.1) pour gérer les données
géographiques (villes, gares, lignes) et garantir la cohérence transactionnelle
sur les réservations. Le risque principal est la survente : plusieurs voyageurs
peuvent tenter de réserver la même place au même moment.

## Décision
Utiliser PostgreSQL 16+ avec PostGIS comme base principale, avec :
- Une table `inventory` par départ/classe/tronçon, avec une contrainte
  `CHECK (sold + held <= capacity)` au niveau base de données.
- Des verrous explicites `SELECT ... FOR UPDATE` lors de la création d'un hold,
  pour sérialiser les accès concurrents sur la même ligne d'inventaire.
- Un TTL sur les holds (colonne `expires_at`), libéré par un job planifié.
- PostGIS pour les coordonnées des stations et le calcul de distances/itinéraires.

## Alternatives écartées
- **MySQL** : pas retenu, non conforme au CdC et support PostGIS absent.
- **Verrouillage optimiste (version + retry)** : écarté pour le MVP, plus complexe
  à tester correctement en 6 semaines ; le verrouillage pessimiste (`FOR UPDATE`)
  est plus simple à garantir sans survente.
- **Redis pour l'inventaire en temps réel** : écarté comme source de vérité,
  gardé uniquement comme cache (cache-aside) pour la recherche (§ CdC cache).

## Conséquences
- Toute écriture sur l'inventaire doit passer par une transaction avec verrou
  ordonné, pour éviter les deadlocks entre tronçons.
- Les tests de concurrence (scénarios 1 à 4 du CdC) doivent vérifier
  qu'aucune survente ne se produit sous charge simultanée.
- La contrainte CHECK protège la base même en cas de bug applicatif.
