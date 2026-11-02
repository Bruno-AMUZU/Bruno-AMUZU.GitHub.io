# Composants

Toutes les valeurs renvoient à `tokens.json` (`color.*` = couleur du thème courant, `space.N`, `radius.*`, `type.*`).
Chaque composant existe en thème clair et sombre via les variables de couleur, pas en double.

## Fondations

### Button [V]
| Variante | Fond | Texte | Usage |
|---|---|---|---|
| `primary` | `accent` | `onAccent` | Action principale (« Démarrer un projet ») |
| `secondary` | `soft` | `ink` | Action secondaire (« Voir les projets ») |
| `inverse` | `inv` | `invInk` | CTA de l'en-tête, « Envoyer le message » |

- Typo `label`, padding `space.4` vertical × `space.6` horizontal, rayon `pill`, hauteur ≥ `size.touchMin`.
- États : par défaut, survol (opacité 0.85), focus (contour 2 px `accent`, décalé de 2 px), désactivé (opacité 0.4) [P].

### IconButton [V]
- Cercle `size.iconButton`, fond `soft`, icône 20 px trait 2 px couleur `ink`.
- Usages : bascule de thème (lune / soleil), fermeture, envoi.

### Pill [V]
- Pastille d'information : fond `soft`, typo `small`, padding `space.2` × `space.4`, rayon `pill`.
- Variante `status` : point de 8 px `accent` + texte (« Disponible… »).
- Variante `accent` : fond `accent`, texte `onAccent`, typo `caption` 600 (tags du Lab).

### Tag [V]
- Techno d'un projet : fond `soft`, typo `caption`, padding `space.2` × `space.3`, rayon `pill`.

### TechChip [V]
- Ligne d'une carte Stack : fond `soft` (ou `invSoft` dans une carte inversée), rayon `md`, padding `space.3`, gap `space.3`.
- Icône 34 px rayon `sm`, couleur `color.tech.*` (seule exception aux couleurs du thème) ; dans la carte IA, icône fond `accent`.
- Libellé typo `body` 500.
- Logo réel de la techno si disponible, sinon monogramme (2-3 lettres, typo `caption` 700) [P].

### Field [V]
- Libellé typo `small` au-dessus, gap `space.2`.
- Champ : fond `soft`, texte `ink`, sans bordure, rayon `md`, padding `space.4`, typo `body`.
- Variantes : `text`, `email`, `textarea` (4 lignes).
- États [P] : focus (contour 2 px `accent`), erreur (contour 2 px `status.danger` + message `caption`).

### ChoiceChips [V]
- Groupe de boutons « Type de projet » : non sélectionné fond `soft` / texte `ink`, sélectionné fond `inv` / texte `invInk`. Hauteur ≥ 44 px.

## Structure

### Header [V]
- Barre en pilule, fond `surface`, rayon `pill`, padding `space.2` + `space.6` à gauche, dans le conteneur (marge haute `space.5`).
- Gauche : logo = symbole « terminal » 28 px (`images/logo.svg` : carré rayon 7 `#111111`, chevron `>` et curseur `_` `accent`, « b » `#F4F4F2` ; curseur clignotant, figé si `prefers-reduced-motion`) + « bruno amuzu » typo `logo`. En thème sombre, liseré 1 px `line` autour du carré. [V]
- Droite (desktop) : liens Services, Stack, Projets, Lab, À propos (typo `nav`) · sélecteur FR · EN · IconButton thème · Button `inverse` « Démarrer un projet ».
- Mobile [V] : logo + IconButton thème + IconButton menu ; le menu ouvre un panneau plein écran (fond `surface`) listant les mêmes liens, le sélecteur de langue et le CTA.

### LangSwitch [V]
- « FR · EN », langue active en `ink` gras, l'autre en `muted`. Typo `small`.

### SectionHeader [V]
- Titre typo `heading-1`, marge gauche `space.2`.
- Option : lien à droite (« Tous les projets → »), typo `label`.
- Marge basse `space.7`.

### Footer [V]
- Ligne : « © 2026 Bruno Amuzu » à gauche ; à droite, dans cet ordre : GitHub, LinkedIn, X, email, Mentions légales (`common.footer.links`). Typo `small`, couleur `muted`. **Pas de téléphone.**

## Cartes

### ServiceCard [V]
- Fond `surface` (ou `inv` pour la carte IA), rayon `xl`, padding `space.4` (bas `space.8`).
- Zone illustration 190 px de haut, fond `soft` (`invSoft`), rayon `lg`, padding `space.6`.
- Illustrations (une par service, dessinées en formes simples aux couleurs du thème + accent) :
  - `web` : fenêtre de navigateur (3 points, ligne de titre `ink`, ligne `line`, bouton `accent`).
  - `apps` : 5 barres d'histogramme, une en `accent`, une en `ink`, trois en `line`.
  - `ia` : « Contexte » → rond `accent` « Agent » → « Résultat », reliés par des traits.
- Sous l'illustration, padding horizontal `space.4` : titre `heading-2`, texte `body` couleur `muted`.

### StackCard [V]
- Fond `surface` (catégorie IA : `inv`), rayon `xl`, padding `space.7`, gap `space.5`.
- En-tête : titre `heading-2` + compteur (« 4 outils ») typo `caption` couleur `muted`.
- Liste verticale de TechChip, gap `space.2`.

### ProjectCard [V]
- Fond `surface`, rayon `xl`, padding `space.3`, gap `space.5`. Toute la carte est un lien vers la page projet.
- Visuel 240 px de haut, rayon `lg`. Projets fictifs (`image: null`) : bloc `inv` avec « [Visuel du projet] ».
- Bas (padding `space.3`) : titre `heading-2` + rond 40 px `soft` avec flèche → ; dessous, Tags.
- **Toutes les cartes projet sont identiques en taille.** Pas de variante « vedette ».

### LabCard [V]
- Dans une section inversée : fond `invSoft`, rayon `lg`, padding `space.6`.
- Pill `accent` (catégorie) puis titre `heading-3`.

### ContactBlock [V]
- Deux cartes côte à côte (empilées en mobile), gap `space.4` :
  - gauche : fond `accent`, rayon `2xl`, padding `space.12`, accroche `display-l` couleur `onAccent`, alignée en bas ;
  - droite : formulaire, fond `surface`, rayon `2xl`, padding `space.10` : Field Nom + Field Email (côte à côte), ChoiceChips Type de projet, Field Message, Button `inverse` « Envoyer le message ».
- **Pas de CV, pas de téléphone dans ce bloc.**

## Assistant IA

### ChatBubble [V]
- Bouton rond `size.chatBubble`, fond `accent`, icône bulle de dialogue `onAccent`, ombre `float`. Fixé en bas à droite, à `space.5` des bords. Présent sur toutes les pages publiques.

### ChatPanel [V]
- Panneau `size.chatPanelWidth` de large, fond `surface`, rayon `xl`, ombre `float`, au-dessus de la bulle.
- En-tête fond `inv` : « Assistant de Bruno » (`heading-3` 700, 17 px [P]) + sous-titre `caption` `invMuted` + IconButton fermer.
- Messages : assistant à gauche fond `soft` ; visiteur à droite fond `accent` / texte `onAccent` ; rayon `md` avec coin d'origine à 4 px.
- Bas : champ arrondi (`pill`) + IconButton envoyer (fond `inv`).
- En mobile [P] : le panneau occupe toute la largeur moins `space.4` de chaque côté.

## Back-office [V]

Reprend les mêmes tokens, en français uniquement. Le détail ci-dessous reste [P] jusqu'à la maquette.

- **AdminSidebar** : colonne 240 px, fond `inv` ; logo ; entrées Tableau de bord, Projets, Certifications, Parcours, Compétences, Services, Lab, Messages (avec compteur non lus), Réglages ; entrée active fond `accent` / texte `onAccent`, rayon `sm` ; en bas : « Voir le site » et « Déconnexion ».
- **AdminTopbar** : titre de page `heading-1` réduit à 36 px [P] + actions à droite (Button `primary` « + Nouveau »).
- **DataTable** : lignes en cartes `surface`, rayon `md`, gap `space.2` ; colonnes selon le type ; badge de statut.
- **StatusBadge** : `Publié` (point `status.success`), `Brouillon` (bordure pointillée `line`), `Non lu` (point `accent`).
- **LangTabs** : onglets FR / EN au-dessus du formulaire d'édition ; onglet incomplet marqué d'un point `status.danger`.
- **StatCard** : chiffre `display-l` + libellé `small`, fond `surface`, rayon `xl`.
