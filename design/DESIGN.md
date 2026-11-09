# Portfolio v2 — Spécification de design

Source de vérité unique pour les maquettes (FigCraft) puis pour l'intégration.
**Ce qui n'est pas écrit ici n'existe pas.**

## Fichiers

| Fichier | Rôle |
|---|---|
| `DESIGN.md` | Brief, règles, périmètre, journal des décisions, questions ouvertes |
| `tokens.json` | Valeurs exactes : couleurs (2 thèmes), typo, espacements, arrondis, ombres, grilles |
| `components.md` | Composants, variantes et règles d'usage |
| `pages/*.md` | Une fiche par page : sections, ordre, contenu, comportement mobile |
| `structure.json` | Version machine des fiches (cadres attendus, sections, interdits), lue par le script de vérification. À tenir synchronisée avec `pages/*.md` |
| `maquette/` | Fichier FigCraft de la maquette et rapports de vérification |

La production de la maquette suit le skill `.claude/skills/maquette-figcraft/`.
| `content.fr.json` / `content.en.json` | Tous les textes. Clés référencées par les fiches de pages |

## Statuts

Chaque élément de la spec porte un statut :

- **[V] Validé** par Bruno. Se construit tel quel.
- **[P] Proposé**, pas encore validé. Se construit, mais apparaît dans la liste « à valider » du rapport.
- **[R] Refusé**. Ne doit jamais apparaître.

Un texte non encore rédigé vaut `[À FOURNIR]` dans les fichiers de contenu et s'affiche tel quel dans la maquette.

## Brief

- **Qui** : Bruno Amuzu, développeur web. Positionnement professionnel : quelqu'un qui construit des projets, aide des clients et qu'on peut contacter pour des missions. Pas « apprenti qui connaît quelques langages ». [V]
- **Cibles** : recruteurs en priorité (recherche de **stage ou alternance**, pas de freelance pour l’instant) ; le site reste présentable à de futurs clients (Services conservés). [V]
- **Différenciation** : IA appliquée — context engineering, harness engineering — prouvée par des réalisations et le Lab, pas par des mots-clés. [V]
- **Langues** : français et anglais. [V]
- **Stack du site** : PHP natif, PostgreSQL, Docker. [V]

## Principes visuels [V]

1. **Montrer plutôt qu'expliquer** : chaque bloc est une preuve visuelle ; texte réduit au minimum (un titre, une ligne, un bouton).
2. **Cartes arrondies en grille (bento)** ; contraste entre cartes claires, cartes inversées (noires en thème clair) et accent.
3. **L'accent colore des surfaces** (boutons, pastilles, cartes entières), jamais du texte surligné.
4. **Typo** : Space Grotesk pour les titres, Inter pour le texte.
5. **Deux thèmes** : clair et sombre, même structure. En sombre, les cartes inversées deviennent claires.
6. **Accent : Orchidée `#C084FC`**, texte posé dessus en `#111111`.

## Règles de production (pour l'IA qui construit la maquette)

1. Lire toute la spec avant de produire quoi que ce soit.
2. Aucune valeur saisie à la main : couleurs, polices, tailles, espacements, arrondis viennent **uniquement** de `tokens.json` (variables / styles FigCraft).
3. Construire les composants de `components.md` d'abord ; les pages ne contiennent **que** des instances de composants et des conteneurs en auto layout.
4. Les sections d'une page sont exactement celles de sa fiche, dans cet ordre. Aucun ajout, aucune suppression.
5. Les textes viennent **uniquement** de `content.*.json`. Ne jamais inventer de contenu ; si une clé manque, afficher `[À FOURNIR]` et le signaler.
6. Information manquante ou ambiguë → poser la question, ne pas trancher seul.
7. Après chaque page : lancer la vérification automatique, joindre une capture et le rapport (écarts + éléments [P]). Attendre la validation avant la page suivante.
8. Ne jamais reprendre un élément de l'ancien site (v1) qui n'est pas dans la spec.
9. Les projets de la maquette sont ceux de `content.*.json` → `projects` (tous fictifs, `fictional: true`). N'y ajouter aucun projet réel.

## Périmètre de la maquette

**Formats** : desktop (1440 px) et mobile (390 px), chacun en thème clair et sombre.
Ordre de production : composants → Accueil → les autres pages publiques → back-office.

### Site public
| Page | Fiche | Statut |
|---|---|---|
| Accueil | `pages/accueil.md` | [V] |
| À propos | `pages/a-propos.md` | [V] |
| Projets (liste) | `pages/projets.md` | [V] |
| Projet (détail / étude de cas) | `pages/projet.md` | [V] |
| Lab (liste) | `pages/lab.md` | [V] |
| Article du Lab | `pages/article.md` | [V] |
| Contact | `pages/contact.md` | [V] |
| Mentions légales | `pages/mentions-legales.md` | [P] |
| 404 | `pages/404.md` | [P] |

### Back-office
| Page | Fiche | Statut |
|---|---|---|
| Connexion | `pages/admin-connexion.md` | [V] |
| Tableau de bord | `pages/admin-tableau-de-bord.md` | [V] |
| Liste d'un type de contenu | `pages/admin-liste.md` | [V] |
| Édition d'un contenu (FR/EN) | `pages/admin-edition.md` | [V] |
| Messages | `pages/admin-messages.md` | [V] |

Le back-office reprend les tokens du site public, avec une barre latérale `inv`, et est en **français uniquement**. [V]
Le détail des fiches back-office reste [P] tant qu'il n'a pas été vu en maquette.

Le back-office gère des **types de contenu définis** : projets, certifications, parcours, compétences, services, articles du Lab, messages, réglages. **Pas de création libre de sections.** [V]

## Journal des décisions

| Date | Décision | Statut |
|---|---|---|
| 2026-10-29 | PHP natif plutôt que Laravel | [V] |
| 2026-10-29 | PostgreSQL plutôt que MySQL (pgvector pour l'assistant IA) | [V] |
| 2026-10-29 | Bilingue FR/EN | [V] |
| 2026-10-29 | Création libre de sections dans le back-office | [R] |
| 2026-10-29 | Certifications et parcours sur une page À propos, pas sur l'accueil | [V] |
| 2026-10-29 | Directions A (éditorial) et B (terminal) : trop textuelles | [R] |
| 2026-10-29 | Style « studio produit » / bento (direction C → D) | [V] |
| 2026-10-29 | Typo Space Grotesk + Inter | [V] |
| 2026-10-29 | Mot surligné façon marqueur dans un titre | [R] |
| 2026-10-29 | Accent vert citron | [R] |
| 2026-10-29 | Thème clair + thème sombre | [V] |
| 2026-10-29 | Carte « aperçu du back-office » dans le hero | [R] |
| 2026-10-29 | Carte « Pour qui » | [R] |
| 2026-10-29 | Assistant IA en carte dans la page | [R] |
| 2026-10-29 | Assistant IA en bulle flottante qui ouvre un panneau | [V] |
| 2026-10-29 | Stack = section à part entière, au niveau de Services | [V] |
| 2026-10-29 | Section Services avec mini-illustrations | [V] |
| 2026-10-29 | Projet vedette en pleine largeur | [R] |
| 2026-10-29 | Projets en grille homogène, détail sur la page projet | [V] |
| 2026-10-29 | CV dans la section Contact | [R] |
| 2026-10-29 | CV et photo sur la page À propos | [V] |
| 2026-10-29 | Hero en texte uniquement, sans visuel | [V] |
| 2026-10-30 | Accent Orchidée `#C084FC` | [V] |
| 2026-10-30 | Contact : page dédiée **ou** section de l'accueil, jamais les deux | [V] |
| 2026-10-30 | Maquette : **projets fictifs uniquement**, aucun projet réel (BDI WEAR, Robbie Lens, Portfolio CMS retirés) | [V] |
| 2026-10-30 | Liste Stack de `content.*.json` | [V] |
| 2026-10-30 | Menu mobile « burger » plein écran | [V] |
| 2026-10-30 | Back-office : mêmes tokens, barre latérale `inv`, français uniquement | [V] |
| 2026-10-30 | Filtres (Projets, Lab), galerie et « Projet suivant » (page projet) | [V] |
| 2026-10-30 | Liens : GitHub (en premier), LinkedIn, X, email | [V] |
| 2026-10-30 | Contact = **page dédiée** `/contact` ; plus de section Contact sur l'accueil | [V] |
| 2026-11-02 | Logo « terminal » `>b_` (images/logo.svg), composant `Marque` dans le Header et le back-office ; couleurs `color.brand.*` | [V] |
| 2026-11-02 | Logos officiels (Devicon) dans les TechChip, initiales pour la carte IA | [V] |
| 2026-11-02 | Icônes dessinées sur mesure (lune, soleil, fermer, envoyer, menu, bulle, flèche, modifier, supprimer) | [V] |
| 2026-11-02 | Pas de styles de texte supplémentaires : « FR » actif distingué par la couleur, titre de l'assistant en `heading-3`, pastille accent en `caption` | [V] |
| 2026-11-02 | Back-office : entrées inactives en `invMuted`, titre de page `heading-1/mobile` | [V] |
| 2026-11-02 | États Button (survol, focus, désactivé) et Field (focus, erreur) | [V] |
| 2026-11-02 | Pas de freelance pour l'instant : recherche de **stage ou alternance** ; pastille du hero « En recherche d'un stage ou d'une alternance » | [V] |
| 2026-11-02 | Pastille « Disponible — freelance & opportunités » | [R] |
| 2026-11-02 | CTA principal « Me contacter » (au lieu de « Démarrer un projet ») ; section Services conservée | [V] |
| 2026-11-02 | Nom de l'assistant : « Assistant IA » (transparence : on sait tout de suite que c'est une IA) | [V] |
| 2026-11-02 | Nom « Assistant de Bruno » (ambigu : peut évoquer une personne) | [R] |
| 2026-11-02 | Accueil validé : hero mobile 32 px (pastille sur 2 lignes), Lab mobile 24 px, bulle dans le premier écran, cartes de même hauteur, icône de thème = action, menu mobile plein écran | [V] |
| 2026-11-02 | Textes conservés tels quels : accroche du hero, page Contact (« Un projet en tête ? », « Type de projet »), message d'accueil de l'assistant | [V] |
| 2026-11-02 | Page À propos validée : TimelineCard (frise point `accent` + trait `line`), CertCard, hauteurs de cartes libres | [V] |
| 2026-11-02 | Page Projets validée ; composant PageHeader pour l'en-tête des pages internes | [V] |
| 2026-11-02 | Page Projet validée ; composant CaseCard (étude de cas) | [V] |
| 2026-11-02 | Article du Lab : vidéo facultative avant le texte (format cours OpenClassrooms) ; champ vidéo dans le back-office | [V] |
| 2026-11-02 | Vidéos hébergées sur YouTube ; lecteur VideoPlayer validé tel quel | [V] |
| 2026-11-02 | Police de code : Consolas (maquette) / pile monospace système (site) ; style `code` | [V] |
| 2026-11-02 | Footer : variante mobile (copyright puis liens à la ligne) | [V] |
| 2026-11-02 | Page Contact validée : ContactBlock (formulaire / envoyé × desktop / mobile) ; bulle en bas de page quand la page est plus courte qu'un écran | [V] |
| 2026-11-02 | Mentions légales : clair **et** sombre (le thème choisi par le visiteur s'applique partout), pas de bulle, titre + carte de blocs | [V] |
| 2026-11-02 | Mentions légales en clair uniquement | [R] |
| 2026-11-02 | Page 404 : « 404 » en `onAccent` dans un bloc `accent` | [V] |
| 2026-11-02 | « 404 » en texte couleur `accent` (contraire au principe 3, contraste insuffisant) | [R] |
| 2026-11-02 | Back-office : récupération du mot de passe par lien email (usage unique, 30 min) + script serveur en secours ; 3 écrans (demande, lien envoyé, nouveau mot de passe) | [V] |
| 2026-11-02 | Pas de « mot de passe oublié » sous prétexte d'un seul administrateur | [R] |
| 2026-11-03 | Tableau de bord validé : MessageRow (extrait masqué sur le tableau de bord, affiché dans Messages), AdminSidebar `Format=mobile` (barre haute + menu), section Actions rapides conservée | [V] |
| 2026-11-03 | Liste validée : DataTableRow colonne statut 96 px, variante mobile (sans poignée ni date), état vide = carte `surface` texte `body-l` `muted` + Button `primary` | [V] |
| 2026-11-03 | Édition : libellés des champs repris du tableau « Champs par type » (`admin.edit.fields`), aide « Glisser une image ou parcourir » et « Ajouter une techno… », sélecteur de statut en ChoiceChips | [V] |
| 2026-11-03 | Renommer le dépôt GitHub `Bruno-AMUZU.GitHub.io` (nom pressenti : `portfolio` ou `brunoamuzu.dev`) **seulement une fois la v2 en ligne sur brunoamuzu.dev**, pour ne pas couper le site actuel ; puis recréer un dépôt `Bruno-AMUZU.github.io` qui redirige vers brunoamuzu.dev | [V] |
| 2026-11-03 | Édition validée : AdminTopbar `Type=édition`, titres de cartes `heading-3`, « Mis à jour le [DATE] », champ vide si le contenu est absent, lien Supprimer sous le formulaire en mobile | [V] |
| 2026-11-03 | Messages validée : non lus marqués par le StatusBadge « Non lu », extrait sur sa propre ligne dans la liste étroite, email en `label` sans soulignement, pas de cadre « détail plein écran » mobile. Maquette complète. | [V] |
| 2026-11-03 | Départ à blanc : tout le contenu (CV, certifications, images de projets…) est ajouté depuis le back-office. Fichiers stockés sur le disque du serveur dans `storage/uploads/` (ignoré par git), métadonnées en base. Dossiers `data/` et `images/Portfolio/` supprimés | [V] |
| 2026-11-03 | Site v1 (`index.html`, `style.css`, `JS/`, `mentions-légales.html`, anciennes icônes) supprimé de la branche `v2` ; il reste en ligne depuis `main` jusqu'à la mise en ligne de la v2 | [V] |
| 2026-11-03 | `design/` reste dans le dépôt comme spec, mais n'est jamais servi en ligne. `.claude/` et `design/maquette/rapports/` restent en local, non suivis par git | [V] |
| 2026-11-03 | Architecture **MVC** : `public/` seul dossier exposé (point d'entrée unique `index.php`) ; `src/` ne contient que des classes : `Core/`, `Controller/Site/`, `Controller/Admin/`, `Model/` ; vues à part dans `views/` (`layouts/`, `components/`, `site/`, `admin/`) | [V] |
| 2026-11-03 | Vues dans `src/View/` | [R] |
| 2026-11-07 | Exception à « `src/` = classes uniquement » : `src/helpers.php` regroupe les petites fonctions globales des vues (dont `e()` pour l'échappement), chargé par Composer (`autoload.files`) | [V] |
| 2026-11-07 | Fonctions utilitaires déplacées hors de `src/` (ex. `bootstrap/helpers.php`) | [R] |
| 2026-11-07 | Textes d'interface validés : titre À propos « Développeur full‑stack, je construis des outils qui vous simplifient la vie. », intros Projets et Lab, confirmation du contact, mention RGPD (conservation 12 mois), phrase de la 404, bloc « Données personnelles » des mentions légales. Le bloc « Hébergement » attend le choix de l'hébergeur | [V] |
| 2026-11-07 | À propos : la présentation devient un récit de 14 paragraphes écrit par Bruno (la tablette, la découverte du code, l'IA), au lieu de 3-4 phrases ; première phrase en `heading-3`, photo qui reste visible pendant la lecture (desktop). Écart assumé à la maquette | [V] |
| 2026-11-07 | Lab : ne traite plus seulement d'IA. Catégories Back-end, Front-end, IA, Outils (au lieu de Context engineering, Harness engineering, RAG) ; intro « Je vous montre ce que j'expérimente. » | [V] |
| 2026-11-07 | Messages de contact supprimés automatiquement après 12 mois, comme l'annonce la mention RGPD | [V] |
| 2026-11-08 | Menu : Accueil · Projets · Lab · À propos. Services et Stack quittent le menu (ce sont des sections de l'accueil) ; Projets reste, il mène à la page `/projets`. Écart assumé à la maquette | [V] |
| 2026-11-08 | Header fixé en haut pendant le défilement | [V] |
| 2026-11-08 | Header : bandeau pleine largeur collé en haut, fond translucide flouté, liens centrés, au lieu de la pilule dans le conteneur. Écart assumé à la maquette FigCraft | [V] |
| 2026-11-08 | Accueil : navigation de section (SectionNav). Grand écran : rail de traits à droite, façon table des matières de Notion, la section en cours garde son nom affiché (variante « libellé actif » choisie parmi 3). Petit écran : pastille flottante centrée en bas, qui ouvre la liste des sections ; la bulle de l'assistant (V2.2) reste séparée, à droite. Écart assumé à la maquette, qui n'est pas mise à jour | [V] |
| 2026-11-08 | SectionNav : seuils dictés par la marge du conteneur (rail dès 1340 px) | [R] trop haut : un portable 1920 px à 150 % fait 1280 px CSS et voyait la pastille |
| 2026-11-08 | SectionNav : conteneur resserré de 136 px de chaque côté pour afficher les noms en permanence dès 1024 px | [R] tout le contenu rétrécissait trop |
| 2026-11-08 | SectionNav adaptatif (option A) : ≥ 1520 px, rail « libellé actif » dans la marge, sans toucher au contenu ; 1024–1519 px, traits seuls et panneau des noms au survol (façon Notion), conteneur resserré de 56 px de chaque côté ; < 1024 px, pastille | [V] |
| 2026-11-08 | SectionNav plus grand (traits 20 × 4 px, 32 px pour la section en cours, lignes de 32 px, noms en `label`) et animation au survol : le panneau se déploie depuis la droite, les noms arrivent l'un après l'autre. Conteneur resserré de 72 px (au lieu de 56) entre 1024 et 1519 px | [V] |
| 2026-11-09 | StackCard : le compteur d'outils (« 4 outils ») est retiré | [V] |
| 2026-11-09 | Animations retenues (parmi 9 proposées) : champ de pixels du hero (version avec peu de pixels), apparition des cartes au défilement, survol des cartes cliquables (soulèvement, voile accent, PixelBurst), pixels qui tombent sur la 404. Écarts assumés à la maquette | [V] |
| 2026-11-09 | Règles des animations : désactivées si `prefers-reduced-motion` ; aucun contenu masqué sans JavaScript (seules les cartes encore sous l'écran sont préparées) ; ni bibliothèque ni dépendance ; courbe commune `motion.outSoft` | [V] |
| 2026-11-09 | Titres pixelisés, traînée de pixels, bascule de thème en pixels, curseur du terminal sur le titre, transition entre les pages | [R] non retenus |
| 2026-11-09 | Champ de pixels dans la carte du hero | [R] remplacé par le fond de pixels |
| 2026-11-09 | Fond de pixels sur tout le site public (choisi parmi : dans la carte, autour du hero, toute la page, hero sans carte) : canvas fixé derrière la page, quelques pixels `accent` (12 px dans une grille de 16 px, opacité 50 % au plus) s'allument au hasard et réagissent à la souris ; les cartes opaques glissent par-dessus. Absent de la 404 (elle a ses pixels qui tombent) | [V] |
| 2026-11-09 | Hero sans carte : titre, sous-titre et boutons posés directement sur le fond de pixels (comparé dans une maquette aux options « dans la carte », « autour du hero » et « toute la page », puis essayé sur le site). Écart assumé à la maquette | [V] |
| 2026-11-03 | Modèle séparé en **entités** (`src/Model/Entity/`, uniquement les données) et **repositories** (`src/Model/Repository/`, uniquement le SQL) | [V] |
| 2026-11-03 | Modèle « Active Record » (une classe qui porte données et SQL) | [R] |
| 2026-11-03 | **Composer** : chargement automatique PSR-4 (`App\` → `src/`), PHPMailer (envoi SMTP), PHPUnit (tests) ; pas de framework. `vendor/` non suivi par git | [V] |
| 2026-11-03 | **Docker (dev)** : Apache + PHP (image `php:apache`), PostgreSQL, Mailpit (capture des e-mails), Adminer (interface de la base) | [V] |
| 2026-11-03 | nginx + php-fpm | [R] |
| 2026-11-03 | **Production** : Mailpit et Adminer jamais déployés ; envoi des e-mails par un vrai SMTP (hébergeur ou service d'envoi) configuré dans `.env`, avec SPF/DKIM sur le domaine. Le message de contact est d'abord enregistré en base, l'e-mail n'est qu'une notification | [V] |
| 2026-11-02 | Les contenus `[À FOURNIR]` seront traités dans une discussion dédiée ; la maquette les affiche tels quels d'ici là | [V] |
| 2026-10-30 | Téléphone affiché | [R] |
| 2026-10-30 | Instagram | [R] |

## Questions ouvertes

1. **Lab** : quels premiers sujets d'articles ? (aucun titre n'est fixé — n'empêche pas la maquette)
2. **Parcours** : dates et intitulés exacts des formations et expériences (n'empêche pas la maquette)
