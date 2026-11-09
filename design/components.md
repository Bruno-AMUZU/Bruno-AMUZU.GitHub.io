# Composants

Toutes les valeurs renvoient à `tokens.json` (`color.*` = couleur du thème courant, `space.N`, `radius.*`, `type.*`).
Chaque composant existe en thème clair et sombre via les variables de couleur, pas en double.

## Fondations

### Button [V]
| Variante | Fond | Texte | Usage |
|---|---|---|---|
| `primary` | `accent` | `onAccent` | Action principale (« Me contacter ») |
| `secondary` | `soft` | `ink` | Action secondaire (« Voir les projets ») |
| `inverse` | `inv` | `invInk` | CTA de l'en-tête, « Envoyer le message » |

- Typo `label`, padding `space.4` vertical × `space.6` horizontal, rayon `pill`, hauteur ≥ `size.touchMin`.
- États (propriété `État`, dessinés) : `défaut`, `survol` (opacité 0.85), `focus` (anneau 2 px `accent` décalé de 2 px, l'écart en `bg` : deux ombres sans flou liées aux variables), `désactivé` (opacité 0.4). [V]

### IconButton [V]
- Cercle `size.iconButton`, fond `soft`, icône 20 px trait 2 px couleur `ink`.
- Usages : bascule de thème (lune / soleil), fermeture, envoi.

### Pill [V]
- Pastille d'information : fond `soft`, typo `small`, padding `space.2` × `space.4`, rayon `pill`.
- Variante `status` : point de 8 px `accent` + texte (« Disponible… »).
- Variante `accent` : fond `accent`, texte `onAccent`, typo `caption` (tags du Lab). [V]

### Tag [V]
- Techno d'un projet : fond `soft`, typo `caption`, padding `space.2` × `space.3`, rayon `pill`.

### TechChip [V]
- Ligne d'une carte Stack : fond `soft` (ou `invSoft` dans une carte inversée), rayon `md`, padding `space.3`, gap `space.3`.
- Icône : **logo officiel** de la techno (jeu Devicon, composant `Logo`), 24 px, posé directement sur la pastille ; couleurs de marque = seule exception aux couleurs du thème ; le logo GitHub (noir) est lié à `ink` pour rester visible en sombre. [V]
- Carte IA (pas de logo officiel) : carré 34 px rayon `sm` fond `accent` + monogramme typo `caption` couleur `onAccent`. [V]
- Libellé typo `body`.

### Field [V]
- Libellé typo `small` au-dessus, gap `space.2`.
- Champ : fond `soft`, texte `ink`, sans bordure, rayon `md`, padding `space.4`, typo `body`.
- Variantes : `text`, `email`, `textarea` (4 lignes).
- États (propriété `État`, dessinés) : `défaut`, `focus` (contour intérieur 2 px `accent`), `erreur` (contour intérieur 2 px `status.danger` + message `caption` couleur `status.danger` sous le champ, texte `form.errors.*`). [V]

### ChoiceChips [V]
- Groupe de boutons « Type de projet » : non sélectionné fond `soft` / texte `ink`, sélectionné fond `inv` / texte `invInk`. Hauteur ≥ 44 px.

## Structure

### Header [V]
- ~~Barre en pilule, fond `surface`, rayon `pill`, padding `space.2` + `space.6` à gauche, dans le conteneur (marge haute `space.5`).~~ [R 2026-11-08]
- Bandeau pleine largeur collé en haut (aucune marge), fond `bg` à 88 % flouté, padding `space.3` + `gutter` sur les côtés. Logo à gauche, liens centrés, actions à droite. [V 2026-11-08]
- Gauche : logo = symbole « terminal » 28 px (`public/assets/img/logo.svg` : carré rayon 7 `#111111`, chevron `>` et curseur `_` `accent`, « b » `#F4F4F2` ; curseur clignotant, figé si `prefers-reduced-motion`) + « bruno amuzu » typo `logo`. En thème sombre, liseré 1 px `line` autour du carré. [V]
- Droite (desktop) : liens Accueil, Projets, Lab, À propos (typo `nav`) · sélecteur FR · EN · IconButton thème · Button `inverse` « Me contacter ». [V 2026-11-08 : Services et Stack retirés, Accueil ajouté]
- Fixé en haut de l'écran pendant le défilement (`position: sticky`). [V]
- Mobile [V] : logo + IconButton thème + IconButton menu ; le menu ouvre un panneau plein écran (fond `surface`) listant les mêmes liens, le sélecteur de langue et le CTA.

### SectionNav [V]
- Navigation entre les sections d'une page longue (pour l'instant : l'accueil). `nav` « Sur cette page », vrais liens d'ancre : fonctionne sans JavaScript.
- **Rail** (≥ 1024 px) : fixé à droite, centré verticalement, lignes de 32 px. Un trait par section (20 × 4 px, `line`, rayon `pill`) ; section en cours : trait 32 px `accent`. Nom de la section (typo `label`) à droite du trait, noms alignés à gauche. [V]
  - Au survol / focus : le fond du panneau (`surface`, rayon `lg`, ombre `float`) se déploie depuis la droite, les traits s'allongent, puis les noms glissent en place l'un après l'autre (30 ms d'écart). Sans animation si `prefers-reduced-motion`. [V]
  - ≥ 1520 px : nom de la section en cours affiché en permanence. Le contenu n'est pas touché (la marge suffit).
  - 1024–1519 px : traits seuls au repos. Le conteneur se resserre de 72 px de chaque côté.
- **Pastille** (< 1024 px) : fixée en bas, centrée. Fond `inv`, texte `invInk`, rayon `pill`, hauteur 44 px, ombre `float`, point `accent` + nom de la section en cours + chevron. Ouvre au-dessus une liste (fond `surface`, rayon `lg`, liens de 44 px, section en cours sur fond `soft`). S'efface quand le pied de page est visible. La ChatBubble reste séparée, à droite. [V]
- Seuils : 1024 px et 1520 px (voir journal du 2026-11-08). [V]

### LangSwitch [V]
- « FR · EN », langue active en `ink`, l'autre en `muted` (distinction par la couleur). Typo `small`. [V]

### PageHeader [V]
- En-tête des pages internes (Projets, Lab, Mentions légales…) : titre `display-l` + introduction `body-l` `muted`, sans carte autour.
- Variantes `Format=desktop` (1192 px, padding `space.16` en haut + `space.2` sur les côtés, gap `space.4`) et `Format=mobile` (358 px, titre `display-l/mobile`, padding `space.8` en haut, gap `space.3`).
- Propriétés : `Titre`, `Introduction`, `Avec introduction` (masque la phrase quand la page n'en a pas).

### SectionHeader [V]
- Titre typo `heading-1`, marge gauche `space.2`.
- Option : lien à droite (« Tous les projets → »), typo `label`.
- Marge basse `space.7`.

### Footer [V]
- Ligne : « © 2026 Bruno Amuzu » à gauche ; à droite, dans cet ordre : GitHub, LinkedIn, X, email, Mentions légales (`common.footer.links`). Typo `small`, couleur `muted`. **Pas de téléphone.**
- Variantes `Format=desktop` (une ligne, liens à droite) et `Format=mobile` (358 px : copyright puis liens en dessous, alignés à gauche, retour à la ligne). [V]

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
- En-tête : titre `heading-2` seul. [V 2026-11-09 : compteur « 4 outils » retiré]
- Liste verticale de TechChip, gap `space.2`.

### ProjectCard [V]
- Fond `surface`, rayon `xl`, padding `space.3`, gap `space.5`. Toute la carte est un lien vers la page projet.
- Visuel 240 px de haut, rayon `lg`. Projets fictifs (`image: null`) : bloc `inv` avec « [Visuel du projet] ».
- Bas (padding `space.3`) : titre `heading-2` + rond 40 px `soft` avec flèche → ; dessous, Tags.
- **Toutes les cartes projet sont identiques en taille.** Pas de variante « vedette ».
- Survol / focus [V 2026-11-09] : la carte monte de 4 px avec l'ombre `float`, un voile `accent` (35 % → transparent, en diagonale) passe sur le visuel, le rond de la flèche passe en `accent` et la flèche avance de 3 px, le PixelBurst s'allume dans le coin du visuel.

### LabCard [V]
- Dans une section inversée : fond `invSoft`, rayon `lg`, padding `space.6`.
- Pill `accent` (catégorie) puis titre `heading-3`.
- Survol / focus [V 2026-11-09] : la carte monte de 4 px avec l'ombre `float`, le PixelBurst s'allume dans le coin.

### PixelField [V]
- Fond de pixels du site public (`assets/js/pixel-field.js`) : canvas fixé derrière toute la page (`.page` au-dessus). Grille de 16 px, pixels `accent` de 12 px, opacité 50 % au plus, extinction douce ; densité proportionnelle à la surface de l'écran (peu de pixels). À la souris, des pixels s'allument dans un rayon de 3 cases autour du curseur ; sans souris, scintillement seul. Pause quand l'onglet est caché ; absent si `prefers-reduced-motion` et sur la 404.

### PixelBurst [V]
- Motif décoratif de 5 pixels `accent` de 6 px (grille 3 × 2, case du milieu bas vide, écart 2 px), en haut à droite des cartes cliquables (ProjectCard, LabCard). Les pixels apparaissent un à un (ressort, 40 ms d'écart) au survol ou au focus.

### TimelineCard [V]
- Entrée de la frise Parcours (page À propos) : repère à gauche (point 12 px `accent` + trait 2 px `line`), puis carte `surface` rayon `xl`, padding `space.7`, gap `space.2`.
- Contenu : Pill `info` (type : Formation / Expérience), période (`small`, `muted`), intitulé (`heading-2`), structure · lieu (`body`), description (`body`, `muted`).

### CertCard [V]
- Carte de certification (page À propos) : fond `surface`, rayon `xl`, padding `space.6`, gap `space.2`.
- Date (`small`, `muted`), titre (`heading-3`), organisme (`body`, `muted`), lien `about.certifications.link` (`label`). Toutes identiques.

### CaseCard [V]
- Bloc de l'étude de cas (page Projet) : fond `surface`, rayon `xl`, padding `space.7`, gap `space.3` ; titre `heading-2` (`projectPage.context` / `solution` / `result`) + texte `body` `muted`. Les 3 cartes d'une ligne ont la même hauteur.

### VideoPlayer [V]
- Vidéo d'un article du Lab (avant le texte). Ratio 16:9, fond `inv`, rayon `lg`.
- Au centre : bouton lecture rond 72 px fond `accent`, icône `lecture` couleur `onAccent`, ombre `float`.
- En bas à gauche : Pill avec la durée (`articlePage.video.duration`).
- Libellé accessible du bouton : `articlePage.video.play`.

### ContactBlock [V]
- Deux cartes côte à côte (empilées en mobile), gap `space.4` :
  - gauche : fond `accent`, rayon `2xl`, padding `space.12`, accroche `display-l` couleur `onAccent`, alignée en bas ;
  - droite : formulaire, fond `surface`, rayon `2xl`, padding `space.10` : Field Nom + Field Email (côte à côte), ChoiceChips Type de projet, Field Message, Button `inverse` « Envoyer le message ».
- **Pas de CV, pas de téléphone dans ce bloc.**
- Variantes [V] : `État=formulaire | envoyé` (la carte formulaire est remplacée par une carte `surface` : `contactPage.success.title` en `heading-1` + `contactPage.success.text` en `body` `muted`) × `Format=desktop | mobile` (mobile 358 px : cartes empilées, padding `space.6`, accroche `display-l/mobile`, champs Nom et Email l'un sous l'autre).

## Assistant IA

### ChatBubble [V]
- Bouton rond `size.chatBubble`, fond `accent`, icône bulle de dialogue `onAccent`, ombre `float`. Fixé en bas à droite, à `space.5` des bords. Présent sur toutes les pages publiques.

### ChatPanel [V]
- Panneau `size.chatPanelWidth` de large, fond `surface`, rayon `xl`, ombre `float`, au-dessus de la bulle.
- En-tête fond `inv` : « Assistant IA » (`heading-3`) [V] + sous-titre `caption` `invMuted` + IconButton fermer.
- Messages : assistant à gauche fond `soft` ; visiteur à droite fond `accent` / texte `onAccent` ; rayon `md` avec coin d'origine à 4 px.
- Bas : champ arrondi (`pill`) + IconButton envoyer (fond `inv`).
- En mobile [V] : le panneau occupe toute la largeur moins `space.4` de chaque côté.

## Back-office [V]

Reprend les mêmes tokens, en français uniquement. Détail validé sur la page Composants ; entrées inactives de la barre latérale en `invMuted`. [V]

- **AdminSidebar** : colonne 240 px, fond `inv` ; logo ; entrées Tableau de bord, Projets, Certifications, Parcours, Compétences, Services, Lab, Messages (avec compteur non lus), Réglages ; entrée active fond `accent` / texte `onAccent`, rayon `sm` ; en bas : « Voir le site » et « Déconnexion ».
  - Variante `Format=mobile` [V] : barre haute 390 px fond `inv` (logo + IconButton menu fond `invSoft`) ; le menu reprend les mêmes entrées.
- **MessageRow** [V] : ligne de message, fond `soft`, rayon `md`, padding `space.3` × `space.4` : nom (`label`), Tag type de projet, extrait (`small` `muted`, 1 ligne, masquable), date (`caption` `muted`), StatusBadge. Passe à la ligne en mobile.
- **AdminTopbar** : titre de page `heading-1/mobile` (36 px) [V] + actions à droite (Button `primary` « + Nouveau »).
  - Variante `Type=édition` [V] : lien retour (`label` `muted`) au-dessus du titre ; à droite Button `secondary` « Prévisualiser » + Button `primary` « Enregistrer » ; les actions passent à la ligne en mobile.
- **DataTable** : lignes en cartes `surface`, rayon `md`, gap `space.2` ; colonnes selon le type ; badge de statut.
  - Composant `DataTableRow`, colonne statut de largeur fixe (96 px, badge centré) pour garder les colonnes alignées entre « Publié » et « Brouillon ». [V]
  - Variante `Format=mobile` [V] : carte empilée 358 px — miniature + titre, puis langues + statut à gauche et actions à droite ; sans poignée ni date.
- **StatusBadge** : `Publié` (point `status.success`), `Brouillon` (bordure pointillée `line`), `Non lu` (point `accent`).
- **LangTabs** : onglets FR / EN au-dessus du formulaire d'édition ; onglet incomplet marqué d'un point `status.danger`.
- **StatCard** : chiffre `display-l` + libellé `small`, fond `surface`, rayon `xl`.
