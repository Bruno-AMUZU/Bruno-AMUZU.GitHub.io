# Projet — détail / étude de cas [V]

- **Routes** : `/fr/projets/{slug}`, `/en/projects/{slug}`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Exemple affiché** : `projects[0]` (Atelier Brume, fictif)
- **Contenu** : `common`, `projectPage`, `projects`

## Sections — liste fermée, dans cet ordre

1. **Header** [V]
2. **Retour** [V] : lien `projectPage.back` (« ← Tous les projets »), typo `label`.
3. **En-tête du projet** [V]
   - Pill : `year` · `type`.
   - Titre `display-l` : `title`.
   - Résumé `body-l` `muted` : `summary`.
   - Tags : `stack`.
   - Boutons : Button `primary` `projectPage.demo` (si `links.demo`) + Button `secondary` `projectPage.code` (si `links.github` ; absent pour les projets fictifs).
4. **Visuel principal** [V] : visuel pleine largeur du conteneur, rayon `2xl`. Projet fictif : bloc `inv` « [Visuel du projet] », 560 px de haut.
5. **Étude de cas** [V] — trois cartes `surface` en grille 3 colonnes (mobile 1), titres `heading-2` :
   - `projectPage.context` → `caseStudy.context`
   - `projectPage.solution` → `caseStudy.solution`
   - `projectPage.result` → `caseStudy.result`
6. **Galerie** [V] : grille 2 colonnes de 4 visuels secondaires, rayon `xl` (projet fictif : blocs `soft` « [Visuel] »). Masquée si aucune image.
7. **Projet suivant** [V] : titre `heading-2` `projectPage.next` + une ProjectCard de `projects[1]`.
8. **Footer** [V]

## Flottant

ChatBubble. [V]
