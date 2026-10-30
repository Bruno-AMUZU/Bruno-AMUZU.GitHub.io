# Projets — liste [V]

- **Routes** : `/fr/projets`, `/en/projects`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Contenu** : `common`, `projectsPage`, `projects`

## Sections — liste fermée, dans cet ordre

1. **Header** [V]
2. **En-tête de page** [V]
   - Titre `display-l` `projectsPage.title` + texte `body-l` `muted` `projectsPage.intro`. Pas de carte autour [P].
3. **Filtres** [V]
   - Rangée de ChoiceChips : `projectsPage.filters` (Tous, Sites, Applications, IA). « Tous » sélectionné.
4. **Grille** [V]
   - Tous les `projects`, ProjectCard identiques, grille 3 colonnes (mobile 1), gap `layout.*.cardGap`.
5. **Footer** [V]

## Flottant

ChatBubble. [V]
