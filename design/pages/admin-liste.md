# Back-office — Liste d'un type de contenu [P]

- **Route** : `/admin/{type}` — exemple maquetté : **Projets**
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Contenu** : `admin.nav`, `admin.list`, `projects`

## Sections — liste fermée, dans cet ordre

1. AdminSidebar (entrée « Projets » active).
2. **AdminTopbar** : `admin.list.title` + Button `primary` `admin.list.new`.
3. **Barre d'outils** : Field recherche (`admin.list.search`) + ChoiceChips statut (`admin.list.filters` : Tous, Publiés, Brouillons).
4. **DataTable** — colonnes : poignée de réordonnancement (⋮⋮), miniature 64×40 rayon `sm`, titre, langues disponibles (Tags « FR » « EN »), StatusBadge, date de mise à jour, actions (IconButton modifier, IconButton supprimer). Une ligne par entrée de `projects` (fictifs), plus une ligne « Brouillon » `[Nouveau projet]`.
5. **État vide** (cadre séparé) : carte `surface` avec `admin.list.empty` + Button `primary`.

Mobile : chaque ligne devient une carte empilée (miniature, titre, badges, actions).

## Champs par type (référence pour les formulaires d'édition)

| Type | Champs communs | Champs traduits (FR/EN) |
|---|---|---|
| Projets | slug, année, type, stack, capture, galerie, lien GitHub, lien démo, statut, ordre | titre, résumé, contexte, solution, résultat |
| Certifications | date, organisme, fichier PDF, lien, ordre | titre |
| Parcours | type (formation/expérience), début, fin, structure, lieu, ordre | intitulé, description |
| Compétences | catégorie, logo, ordre | nom |
| Services | illustration (web/apps/ia), ordre | titre, description |
| Articles du Lab | slug, catégorie, date, temps de lecture, statut | titre, extrait, contenu |
| Messages | (lecture seule) nom, email, type, message, date, statut lu/archivé | — |
| Réglages | disponibilité (oui/non), email de contact, liens réseaux, CV (PDF), photo | textes du hero |
