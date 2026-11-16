# MLD — module Offre de transport

Notation : **clé primaire en gras** ; `#` = clé étrangère. Les relations existantes sont réduites aux attributs utiles au module.

## Relations existantes (réutilisées)

- COMPAGNIE (**id_compagnie**, raison_sociale, nom_commercial, statut)
- AGENCE (**id_agence**, nom, ville, statut, #id_compagnie)
- MODE_TRANSPORT (**id_mode**, libelle)
- PRATIQUE (**#id_compagnie**, **#id_mode**)
- UNITE_FLOTTE (**id_unite**, type, immatriculation, capacite, disponibilite, statut, #id_compagnie, #id_mode)

## Relations du module Offre

- LIEU (**id_lieu**, nom, type_lieu, ville, province)
- LIGNE (**id_ligne**, libelle, statut, #id_compagnie, #id_mode, #id_lieu_origine, #id_lieu_destination)
- DESSERT (**#id_ligne**, **#id_lieu**, ordre_passage)
- DEPART (**id_depart**, date_depart, heure_depart, statut, #id_ligne, #id_unite, #id_agence)
- TARIF (**id_tarif**, montant, classe_tarifaire, statut, #id_ligne)
