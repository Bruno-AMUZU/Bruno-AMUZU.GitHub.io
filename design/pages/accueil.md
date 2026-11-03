# Accueil [V]

- **Routes** : `/fr/`, `/en/`
- **Cadres** : Desktop 1440 · Mobile 390 × thème clair · sombre
- **Contenu** : `content.*.json` → `common`, `home`

## Sections — liste fermée, dans cet ordre

1. **Header** — composant Header. [V]
2. **Hero** [V]
   - Une seule carte `surface`, rayon `2xl`, padding `space.24` haut / `space.16` côtés et bas (mobile : `space.8`).
   - Pill `status` : `home.hero.status`.
   - Titre `display-xl` : `home.hero.title` (largeur max 1000 px).
   - Ligne de séparation `line`, puis sur une ligne : `home.hero.subtitle` (`body-l`, `muted`) à gauche ; Button `primary` `home.hero.ctaPrimary` + Button `secondary` `home.hero.ctaSecondary` à droite.
   - **Texte uniquement : aucune image, aucune carte annexe.**
3. **Services** (`#services`) [V]
   - SectionHeader `home.services.title`, sans lien.
   - 3 ServiceCard en grille 3 colonnes (mobile : 1 colonne), dans l'ordre de `home.services.items`. La 3e (IA) en variante inversée.
4. **Stack** (`#stack`) [V]
   - SectionHeader `home.stack.title`, sans lien.
   - 4 StackCard en grille 4 colonnes (tablette 2, mobile 1), dans l'ordre de `stack`. La carte « IA » en variante inversée.
5. **Projets** (`#projets`) [V]
   - SectionHeader `home.projects.title` + lien `home.projects.link` → page Projets.
   - 3 ProjectCard identiques en grille 3 colonnes (mobile 1) : les 3 premiers de `projects`.
6. **Lab IA** (`#lab`) [V]
   - Section entière dans une carte `inv`, rayon `2xl`, padding `space.12`.
   - Titre `home.lab.title` (`heading-1`, couleur `invInk`) + lien `home.lab.link`.
   - 3 LabCard en grille 3 colonnes (mobile 1) : les 3 derniers de `lab`.
7. **Footer** [V]

Le CTA « Me contacter » (header et hero) mène à la page `/contact`.

## Éléments flottants

- ChatBubble en bas à droite. [V]
- Sur un cadre dédié par format (« Accueil — chat ouvert ») : ChatPanel ouvert avec la conversation `chat.demo`. [V]

## Espacements

- Entre deux sections : `layout.*.sectionGap`. Conteneur centré `layout.*.containerMax`, marges `layout.*.gutter`.

## Interdits sur cette page

Carte back-office, carte « Pour qui », projet vedette pleine largeur, section Contact / formulaire, CV, téléphone, mot surligné, photo.
