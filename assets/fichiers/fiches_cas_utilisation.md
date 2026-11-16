# Fiches descriptives des cas d'utilisation — module Offre de transport

Légende : **RF / RM / RNF** = exigences du cahier des charges ; **M1 à M8** = maquettes ; **RG** = règles de gestion. Les points marqués **(à valider)** sont des choix de conception.

Préconditions communes à tous les cas d'utilisation (sauf CU-14) : l'utilisateur est authentifié (CU-14, RNF-001) et ne voit que l'offre de sa propre compagnie lorsqu'il est gestionnaire ou responsable d'agence.

---

## CU-01 — Créer une ligne

| Rubrique | Description |
|---|---|
| Exigences | RF-004, RF-001 ; RM-001, RM-002, RM-009, RM-012 |
| Acteur principal | Gestionnaire de compagnie |
| Acteur secondaire | Module Compagnies (fournit compagnies validées, modes et lieux de référence) |
| Objectif | Déclarer une ligne rattachée à une compagnie validée et active |
| Préconditions | La compagnie est validée et active ; elle a au moins un mode de transport |
| Maquette / séquence | M2 ; séquence « Créer une ligne » |

**Scénario nominal**
1. Le gestionnaire ouvre le formulaire « Nouvelle ligne ».
2. Le système propose la compagnie (validée et active), ses modes de transport et les lieux.
3. Le gestionnaire saisit le libellé, le mode, l'origine, la destination et, s'il y en a, les points desservis avec leur ordre.
4. Le gestionnaire valide.
5. Le système contrôle les données (voir exceptions).
6. Le système enregistre la ligne avec le statut « active » et l'horodate.
7. Le système confirme et affiche la fiche de la ligne (M3).

**Exceptions**
- 5a. Un champ obligatoire est manquant : le système refuse et signale le champ (RM-012).
- 5b. L'origine est identique à la destination : le système refuse (RM-002).
- 5c. La compagnie n'est pas validée et active : le système refuse (RM-001).
- 5d. Le mode choisi ne fait pas partie des modes de la compagnie : le système refuse (RM-009).
- 4a. Le gestionnaire annule : aucune donnée n'est enregistrée.

**Postcondition** : une ligne active existe, rattachée à la compagnie.

---

## CU-02 — Consulter la liste et la fiche d'une ligne

| Rubrique | Description |
|---|---|
| Exigences | RF-005 |
| Acteurs | Gestionnaire de compagnie ; administrateur (consultation de toutes les compagnies) |
| Objectif | Voir les lignes et toutes les informations saisies pour l'une d'elles |
| Maquettes | M1 (liste), M3 (fiche) |

**Scénario nominal**
1. L'acteur ouvre « Lignes ».
2. Le système affiche la liste des lignes de son périmètre (libellé, mode, origine, destination, statut).
3. L'acteur sélectionne une ligne.
4. Le système affiche la fiche : itinéraire et points desservis, tarifs, prochains départs.

**Exception** : 2a. Aucune ligne : le système affiche une liste vide avec le bouton « Nouvelle ligne ».

---

## CU-03 — Mettre à jour une ligne

| Rubrique | Description |
|---|---|
| Exigences | RF-006 ; RM-002, RM-009, RM-012 |
| Acteur principal | Gestionnaire de compagnie |
| Préconditions | La ligne existe et appartient à sa compagnie |
| Maquette | M2 en mode modification |

**Scénario nominal**
1. Le gestionnaire choisit « Modifier » sur une ligne.
2. Le système affiche le formulaire pré-rempli.
3. Le gestionnaire modifie les champs (libellé, mode, origine, destination, points desservis) et valide.
4. Le système applique les mêmes contrôles que CU-01 et enregistre la modification avec horodatage.

**Exceptions** : mêmes que CU-01 (5a, 5b, 5d).

---

## CU-04 — Désactiver une ligne

| Rubrique | Description |
|---|---|
| Exigences | RF-007 ; RM-010, RM-011 |
| Acteur principal | Gestionnaire de compagnie |
| Préconditions | La ligne est active |
| Maquette | M1 (action « Désactiver ») |

**Scénario nominal**
1. Le gestionnaire choisit « Désactiver » sur une ligne.
2. Le système demande une confirmation.
3. Le gestionnaire confirme.
4. Le système passe la ligne au statut « désactivée » (suppression logique) ; elle n'est plus proposée ni exposée comme active.

**Exceptions** : 3a. Le gestionnaire refuse : rien ne change.

**Points à valider** : sort des départs déjà programmés sur une ligne désactivée ; possibilité de réactiver une ligne (non prévue au CDC).

---

## CU-05 — Programmer un départ

| Rubrique | Description |
|---|---|
| Exigences | RF-008 ; RM-003, RM-010, RM-012 |
| Acteurs principaux | Gestionnaire de compagnie ; responsable d'agence (départs de son agence) |
| Inclut | CU-06 « Affecter un véhicule » |
| Préconditions | La ligne existe et est active |
| Maquette / séquence | M5 ; séquence « Programmer un départ » |

**Scénario nominal**
1. L'acteur ouvre « Programmer un départ » sur une ligne.
2. Le système affiche le formulaire (ligne, agence facultative, date, heure, véhicule).
3. L'acteur saisit la date et l'heure.
4. L'acteur choisit le véhicule (CU-06).
5. L'acteur valide.
6. Le système contrôle les données, enregistre le départ au statut « programmé » et l'horodate.
7. Le système confirme et ajoute le départ à la liste (M4).

**Exceptions**
- 6a. Date ou heure manquante : le système refuse (RM-003, RM-012).
- 6b. La ligne est désactivée : le système refuse (RM-010).

**Postcondition** : un départ programmé est rattaché à la ligne.

---

## CU-06 — Affecter un véhicule à un départ

| Rubrique | Description |
|---|---|
| Exigences | RF-009, RF-002 ; RM-004, RM-005 |
| Acteur principal | Celui de CU-05 |
| Acteur secondaire | Module Compagnies (flotte existante) |
| Inclus dans | CU-05 |
| Maquette | M5 (liste « Véhicule » et capacité) |

**Scénario nominal**
1. Le système propose uniquement les véhicules actifs de la compagnie de la ligne.
2. L'acteur choisit un véhicule.
3. Le système affiche la capacité du départ, déduite du véhicule (non modifiable, non stockée).

**Exception** : 2a. Le véhicule choisi appartient à une autre compagnie : le système refuse (RM-004).

---

## CU-07 — Consulter et mettre à jour les départs

| Rubrique | Description |
|---|---|
| Exigences | RF-010 ; RM-010, RM-011 |
| Acteurs principaux | Gestionnaire de compagnie ; responsable d'agence (départs de son agence) |
| Maquettes | M4 (liste), M5 (modification) |

**Scénario nominal**
1. L'acteur ouvre « Départs ».
2. Il filtre par ligne et par date.
3. Le système affiche les départs correspondants (date, heure, ligne, véhicule, places, agence, état).
4. L'acteur choisit « Modifier » sur un départ.
5. Le système affiche le formulaire (M5) ; l'acteur modifie et valide ; le système contrôle comme en CU-05 et enregistre avec horodatage.

**Exception** : 3a. Aucun départ : liste vide.

---

## CU-08 — Suivre l'état des départs (programmé / annulé)

| Rubrique | Description |
|---|---|
| Exigences | RF-010 ; RM-010 |
| Acteurs principaux | Gestionnaire de compagnie ; responsable d'agence (son agence) |
| Préconditions | Le départ est au statut « programmé » |
| Maquette / état | M4 ; diagramme d'états du départ |

**Scénario nominal**
1. L'acteur choisit « Annuler » sur un départ programmé.
2. Le système passe le départ à l'état « annulé » et l'horodate.
3. Le système distingue clairement le départ annulé dans les listes.

**Exception** : 1a. Le départ est déjà annulé : l'action n'est pas proposée.

---

## CU-09 — Définir un tarif pour une ligne

| Rubrique | Description |
|---|---|
| Exigences | RF-011 ; RM-006, RM-012 |
| Acteur principal | Gestionnaire de compagnie |
| Préconditions | La ligne existe |
| Maquette | M6 |

**Scénario nominal**
1. Le gestionnaire ouvre les tarifs d'une ligne.
2. Il saisit le montant et, éventuellement, la classe tarifaire.
3. Il valide.
4. Le système enregistre le tarif, rattaché à la ligne, au statut « actif », avec horodatage.

**Exceptions** : 3a. Montant absent, nul ou négatif : le système refuse (RF-011, RM-012).

---

## CU-10 — Consulter et mettre à jour les tarifs

| Rubrique | Description |
|---|---|
| Exigences | RF-012 ; RM-011 |
| Acteur principal | Gestionnaire de compagnie |
| Maquettes | M3 (consultation), M6 |

**Scénario nominal**
1. Le gestionnaire ouvre les tarifs d'une ligne.
2. Le système affiche la liste (classe, montant, statut).
3. Le gestionnaire modifie un tarif ou le désactive ; le système contrôle le montant et enregistre avec horodatage.

---

## CU-11 — Rechercher / filtrer l'offre

| Rubrique | Description |
|---|---|
| Exigences | RF-013 ; RM-010, RNF-003 |
| Acteurs | Gestionnaire de compagnie ; administrateur **(à valider : le CDC ne précise pas l'acteur)** |
| Acteur secondaire | Phases futures (s'appuieront sur l'offre) **(à valider)** |
| Maquette / séquence | M7 ; séquence « Rechercher l'offre » |

**Scénario nominal**
1. L'acteur ouvre « Offre de transport ».
2. Le système affiche les lignes actives et les départs à venir de son périmètre.
3. L'acteur renseigne un ou plusieurs filtres : compagnie, origine, destination, date.
4. Le système applique les filtres cumulés et exclut les lignes et départs désactivés.
5. Le système affiche les résultats ; l'acteur peut ouvrir la fiche d'une ligne (CU-02).

**Exception** : 4a. Aucun résultat : le système l'indique.

---

## CU-12 — Superviser l'offre déclarée

| Rubrique | Description |
|---|---|
| Exigences | Section 8 (administrateur de la plateforme) |
| Acteur principal | Administrateur de la plateforme |
| Maquettes | M1, M3, M4, M7 (consultation de toutes les compagnies) |

**Scénario nominal**
1. L'administrateur consulte les lignes, départs et tarifs déclarés par les compagnies.
2. Le système lui donne accès à toutes les compagnies.

**Point à valider** : la supervision est ici limitée à la consultation ; le CDC ne prévoit pas d'action de modération.

---

## CU-13 — Consulter le tableau de bord

| Rubrique | Description |
|---|---|
| Exigences | RF-015 |
| Acteurs | Administrateur de la plateforme ; gestionnaire (sa compagnie) **(à valider)** |
| Maquette | M8 |

**Scénario nominal**
1. L'acteur ouvre « Tableau de bord ».
2. Le système affiche les dénombrements : lignes actives, lignes désactivées, départs à venir, départs annulés, et la liste des prochains départs.

---

## CU-14 — S'authentifier

| Rubrique | Description |
|---|---|
| Exigences | RNF-001 |
| Acteurs | Tous les acteurs humains |
| Préconditions | L'utilisateur possède un compte de la plateforme (module Compagnies) |

**Scénario nominal**
1. L'utilisateur saisit ses identifiants.
2. Le système les vérifie et ouvre la session.
3. Le système limite l'accès à l'offre de sa propre compagnie lorsqu'il est gestionnaire ou responsable d'agence.

**Exception** : 2a. Identifiants incorrects : le système refuse l'accès.
