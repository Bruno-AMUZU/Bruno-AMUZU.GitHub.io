---
name: maquette-figcraft
description: Produit et fait évoluer la maquette du portfolio v2 de Bruno dans FigCraft (MCP figcraft / figcraft-live), strictement à partir de la spec du dossier design/ (DESIGN.md, tokens.json, components.md, structure.json, pages/*.md, content.*.json), avec vérification automatique et validation page par page. Utilise ce skill dès qu'il est question de maquette, mockup, design, écran, page, composant, charte, thème clair/sombre ou FigCraft pour le portfolio — y compris « fais la page À propos », « corrige le header », « ajoute l'état erreur de la connexion » ou « où en est la maquette ? », même sans que le mot FigCraft soit prononcé.
---

# Maquette FigCraft du portfolio v2

## Pourquoi ce skill existe

Les premières maquettes ont dérivé : éléments ajoutés sans demande (carte back-office, « Pour qui »), réflexes repris de l'ancien site (CV dans le contact), contenus inventés, choix de style non demandés (mot surligné). Cause commune : en l'absence de spec écrite, le modèle comble les trous en devinant.

Ce skill remplace la devinette par trois garde-fous :
1. **une spec fermée** (`design/`) — ce qui n'y est pas n'existe pas ;
2. **des valeurs non saisies à la main** — variables et styles générés depuis `tokens.json`, composants réutilisés ;
3. **une vérification par script**, puis la validation de Bruno avant de passer à la suite.

Le but n'est pas d'être créatif mais **fidèle**. Quand la spec ne dit rien, on demande.

## Fichiers

| Chemin | Rôle |
|---|---|
| `design/DESIGN.md` | Règles, statuts [V]/[P]/[R], journal des décisions, questions ouvertes |
| `design/tokens.json` | Seules valeurs autorisées |
| `design/components.md` | Composants et variantes |
| `design/pages/*.md` | Fiche de chaque page (sections, ordre, contenu) |
| `design/structure.json` | Version machine des fiches : cadres attendus, sections, interdits |
| `design/content.fr.json` / `.en.json` | Tous les textes (la maquette est en FR) |
| `design/maquette/portfolio-v2.figcraft` | Le fichier de maquette |
| `design/maquette/rapports/` | Rapports de vérification |
| `scripts/tokens-to-figcraft.mjs` | tokens → variables FigCraft + styles de texte |
| `scripts/verify.mjs` | Vérification d'un fichier `.figcraft` contre la spec |

Les chemins `scripts/…` sont relatifs au dossier de ce skill (`.claude/skills/maquette-figcraft/`).

## Avant toute production

1. **Lire toute la spec** : DESIGN.md, components.md, structure.json, la fiche de la page visée, et la partie de content.fr.json qu'elle référence. Ne rien produire avant.
2. **Connexion** : utiliser `mcp__figcraft-live__*` si disponible (Bruno voit la maquette se construire), sinon `mcp__figcraft__*` (headless). Les deux ont les mêmes outils.
3. **Ouvrir le fichier** `design/maquette/portfolio-v2.figcraft` (`open_file`). S'il n'existe pas : nouveau document, puis `save_file` à ce chemin.
4. **Polices** : `run_command text.missingFonts`. Si Space Grotesk ou Inter manque, **s'arrêter** et demander à Bruno de l'installer (Google Fonts) — une police de remplacement fausserait toute la maquette.

## Étape 1 — Fondations (une seule fois)

Sur une page FigCraft nommée **Composants** :

```bash
node .claude/skills/maquette-figcraft/scripts/tokens-to-figcraft.mjs design/tokens.json design/maquette/generated
```

- `run_command variable.import {"variables": "<chemin absolu>/design/maquette/generated/variables.json"}` → collections **Couleurs** (modes Clair, Sombre) et **Dimensions**.
- Lire `design/maquette/generated/text-styles.batch.json` et passer son contenu tel quel à l'outil `batch` → cadre « Typographie » et un style de texte par token (`display-xl`, `display-xl/mobile`, …).

Si `tokens.json` change plus tard : relancer le script, `variable.import` met à jour les valeurs existantes ; pour les styles, `style.update` depuis les témoins.

## Étape 2 — Composants

Construire, sur la page Composants, chaque composant de `structure.json → components`, dans l'ordre de `components.md`. Pour chacun :

- **Auto layout partout** (`layout`, `gap`, `padding`, `sizingH/V`) ; aucune position absolue sauf éléments flottants.
- **Couleurs liées** : créer la forme, puis `variable.bind {field: "fill"|"stroke", variable: "color/…"}`. Ne jamais laisser un hex saisi.
- **Espacements et arrondis** : valeurs de `space` / `radius` uniquement ; les lier (`variable.bind` sur `gap`, `cornerRadius`, …) quand c'est possible.
- **Textes** : `style.apply {style: "<nom du token>"}` sur chaque texte ; couleur liée à une variable.
- **Variantes** : un composant par variante, nommé `Propriété=valeur` (ex. `Variante=primary`), puis `component.combineVariants` et renommer le set avec le nom exact attendu (`Button`).
- **Textes modifiables** : `component.addProperty {type: "text"}` lié au calque, pour que les pages remplissent le contenu par propriété plutôt qu'en détachant.
- Nom du composant = nom exact de `structure.json`.

Puis vérifier (étape 4) avec `--components`, et faire valider la page Composants par Bruno avant toute page.

## Étape 3 — Une page à la fois

Ordre : Accueil → À propos → Projets → Projet → Lab → Article → Contact → Mentions légales → 404 → back-office (Connexion, Tableau de bord, Liste, Édition, Messages).

Pour la page visée :
1. Une page FigCraft par page du site, nommée comme la clé de `structure.json` (`page.add`).
2. Un cadre par entrée attendue (formats × thèmes, + états), nommé **exactement** `{page} / {format} / {thème}` ou `{page} ({état}) / {format} / {thème}`, largeur du format, auto layout vertical, fond lié à `color/bg`.
3. Thème : `variable.setMode {ids: [cadre], collection: "Couleurs", mode: "Clair"|"Sombre"}`. Ne pas dupliquer de composants pour le sombre : le mode suffit.
4. Sections : un calque par section, nommé `§ <id>` (ex. `§ hero`), dans l'ordre de `structure.json`. Rien d'autre au premier niveau du cadre.
5. Contenu : uniquement des **instances** de composants et des conteneurs auto layout. Chaque texte vient de `content.fr.json` (renseigné via les propriétés texte des instances). Clé absente ou `[À FOURNIR]` → afficher le texte tel quel, ne jamais le remplacer.
6. Construire d'abord le cadre Desktop / Clair, le faire vérifier, puis dériver les autres (duplication + `variable.setMode` + styles `/mobile` et grilles 1 colonne pour le mobile).

## Étape 4 — Vérifier, puis montrer

Après chaque page (et après les composants) :

```bash
# save_file d'abord, puis :
node .claude/skills/maquette-figcraft/scripts/verify.mjs design/maquette/portfolio-v2.figcraft design --page "Accueil" --out design/maquette/rapports/accueil.md
node .claude/skills/maquette-figcraft/scripts/verify.mjs design/maquette/portfolio-v2.figcraft design --components --out design/maquette/rapports/composants.md
```

- **Erreurs** (code de sortie 1) : à corriger toutes avant de montrer quoi que ce soit. Le script contrôle couleurs, polices, styles de texte, espacements, arrondis, largeur des cadres, mode clair/sombre, sections et leur ordre, cadres manquants, textes interdits (téléphone, Instagram, projets réels, « CV » sur l'accueil…).
- **Avertissements** : les examiner un par un. Un texte « absent de content » signifie soit une invention (à retirer), soit une clé manquante (à signaler à Bruno). Un « calque hors composant » doit devenir une instance ou être justifié.
- Puis `screenshot` du cadre Desktop / Clair (et des autres si utile).

Présenter à Bruno, dans cet ordre :
1. la capture ;
2. le résultat du script (0 erreur, avertissements restants expliqués) ;
3. la liste des éléments **[P]** présents sur la page (proposés, pas encore validés) ;
4. les questions (contenus `[À FOURNIR]`, ambiguïtés).

Puis **attendre sa validation** avant la page suivante. Chaque décision qu'il prend va dans le journal de `DESIGN.md` (et dans la fiche / `structure.json` concernés) — c'est ce qui empêche de refaire la même erreur plus tard.

## Règles et leur raison

- **Rien hors spec.** Un ajout « pour bien faire » est exactement ce qui a été reproché. Une idée utile se propose en texte, jamais directement dans la maquette.
- **Rien d'inventé.** Les projets sont les six projets fictifs de `content.*.json` ; aucun projet réel (BDI WEAR, Robbie Lens, Portfolio CMS). Les textes manquants restent `[À FOURNIR]`.
- **Rien de la v1.** L'ancien site n'est pas une référence : sa structure et ses contenus ne se reprennent que s'ils sont dans la spec.
- **Les éléments [R] du journal ne réapparaissent jamais** (mot surligné, carte back-office, « Pour qui », projet vedette pleine largeur, CV dans le contact, téléphone…).
- **Spec ambiguë ou muette → question.** Une question coûte une réponse ; une supposition coûte une page à refaire.
- **Changement de spec demandé par Bruno** → mettre à jour les fichiers `design/` d'abord, la maquette ensuite.

## Mémo FigCraft (testé)

| Besoin | Commande |
|---|---|
| Importer les variables | `variable.import {variables: "<chemin>"}` |
| Lier une couleur / une valeur | `variable.bind {ids, field: "fill"\|"stroke"\|"gap"\|"cornerRadius"\|…, variable: "color/surface"}` |
| Thème d'un cadre | `variable.setMode {ids, collection: "Couleurs", mode: "Sombre"}` |
| Appliquer un style de texte | `style.apply {ids, style: "heading-1"}` |
| Créer un composant / des variantes | `component.create`, `component.combineVariants`, `component.addProperty` |
| Placer une instance | `instance.create {component, parent}` |
| Plusieurs étapes en une | outil `batch` ; `{"$step": n}` = id créé à l'étape n |
| Pages | `page.add {name}`, `page.select {id}` |
| Lire l'arbre | `inspect_document`, `get_node` |
| Sauvegarder | `save_file` (le chemin est mémorisé après le premier enregistrement) |

`letterSpacing` se donne en pourcentage (`"-4%"` pour -0.04 em), `lineHeight` en px, `fontStyle` : `Regular`, `Medium`, `Semi Bold`, `Bold`.
