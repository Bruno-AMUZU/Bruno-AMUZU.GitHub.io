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
- **Cibles** : recruteurs et clients freelance, à égalité. [V]
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
| 2026-10-30 | Téléphone affiché | [R] |
| 2026-10-30 | Instagram | [R] |

## Questions ouvertes

1. **Lab** : quels premiers sujets d'articles ? (aucun titre n'est fixé — n'empêche pas la maquette)
2. **Parcours** : dates et intitulés exacts des formations et expériences (n'empêche pas la maquette)
