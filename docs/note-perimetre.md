# Note de périmètre : Wakeve MVP web

## Objectif
Réaliser seule, en 6 semaines, un MVP web de la plateforme Wakeve
(recherche, réservation, paiement et billetterie de voyages au Gabon).

## Inclus (MVP)
- Fondation : Docker, Laravel 11, auth (OTP), RBAC
- Compagnies, flotte et réseau (villes, gares, lignes du Gabon)
- Offre : départs, inventaire par tronçon, tarifs, recherche
- Réservation : holds, zéro survente, règles passagers, idempotence
- Paiement (mock mobile money), ledger, billets QR + PDF
- Embarquement : manifeste et scan
- Back-office compagnie simplifié
- Front web Next.js : recherche, parcours de réservation, espace voyageur

## Exclu / reporté
- Mobile Flutter (voyageur et embarquement)
- Services associés (assurance, assistance, épargne, crédit)
- Comptes entreprises et agences revendeuses
- Bannières publicitaires, IA
- Expansion (multi-devises, connecteurs, GDS)
- Aérien : P2, fait seulement s'il reste du temps

## Contraintes
Stack imposée par le CdC (§3.1). Un billet par voyageur, montants en bigint (XAF),
idempotence, zéro survente. « Terminé » = tests + documentation + logs + gestion d'erreurs.

## Risques
- Travail en solo sur un projet prévu pour une équipe
- Concurrence sur l'inventaire (holds, verrous)
- Paiement simulé uniquement

## Critères de réussite
Scénarios d'acceptation P0 (§26.1) verts, démo complète du parcours famille multi-classes.
