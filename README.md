# Portfolio — Bruno Amuzu

Site personnel bilingue (FR / EN) avec back-office, écrit en **PHP natif** selon une architecture **MVC**,
sans framework : routeur, conteneur de dépendances, vues, modèle et sécurité sont écrits à la main.

> Développeur web en recherche d'un **stage ou d'une alternance** — PHP, PostgreSQL, IA appliquée.

## Fonctionnalités

**Site public**
- Accueil, Projets (filtres), étude de cas par projet, Lab (articles avec vidéo facultative), À propos
  (parcours, compétences, certifications, CV), Contact, Mentions légales, page 404.
- Bilingue : URL traduites (`/fr/projets` ↔ `/en/projects`), sélecteur qui garde la page en cours.
- Thème clair / sombre (préférence du système ou choix mémorisé, sans flash au chargement).
- SEO : balises Open Graph, `hreflang`, `sitemap.xml`, `robots.txt`.

**Back-office** (`/admin`, français)
- Projets, certifications, parcours, compétences, services, articles du Lab : liste, recherche, filtres,
  réordonnancement par glisser-déposer, édition FR / EN, brouillons et aperçu, envoi d'images et de PDF.
- Messages du formulaire de contact (non lus, archivés), tableau de bord, réglages du site.
- Connexion sécurisée, mot de passe oublié par lien e-mail.

## Stack

| | |
|---|---|
| Langage | PHP 8.4, sans framework |
| Base de données | PostgreSQL 17 |
| Serveur | Apache (Docker) |
| Front | HTML, CSS natif (variables générées depuis les tokens de design), JavaScript sans dépendance |
| Bibliothèques | PHPMailer (e-mails), league/commonmark (Markdown), PHPUnit (tests) |
| Outillage | Docker Compose, Mailpit, Adminer, Composer |

## Architecture

```
public/            seul dossier exposé : index.php (point d'entrée unique), assets/
src/
├── Core/          routeur, conteneur, requête/réponse, session, CSRF, vues, traductions, sécurité
├── Controller/
│   ├── Site/      pages publiques
│   └── Admin/     back-office ; Resource/ = liste et édition génériques des types de contenu
├── Model/
│   ├── Entity/    données (Project, Article…)
│   └── Repository/ SQL (requêtes préparées), table principale + table de traductions
├── Routes.php     toutes les URL
└── helpers.php    fonctions des vues (e(), t(), url()…)
views/             vues PHP : layouts/, components/, site/, admin/
lang/              textes de l'interface (fr.php, en.php)
database/          migrations SQL et données de démonstration
bin/               scripts : migrations, seeds, comptes, tokens CSS, sauvegardes, déploiement
design/            spécification de design (source de vérité, jamais servie en ligne)
tests/             PHPUnit
```

Une requête suit le chemin : `public/index.php` → `Router` → contrôleur (dépendances injectées par le
`Container`) → repository → entité → `View` (vue + gabarit) → `Response`.

## Sécurité

- Requêtes préparées partout ; aucune donnée de l'utilisateur dans le SQL.
- Échappement systématique dans les vues (`e()`), Markdown avec HTML échappé.
- Jeton CSRF sur tous les formulaires, cookies de session `HttpOnly` / `SameSite` / `Secure`.
- Content-Security-Policy stricte (scripts autorisés par nonce), HSTS, `X-Frame-Options`, `nosniff`.
- Mots de passe `password_hash`, limitation des tentatives, liens de réinitialisation à usage unique (30 min, empreinte en base).
- Fichiers envoyés : type vérifié sur le contenu, liste blanche, nom aléatoire, stockés hors de `public/`.
- Détail des erreurs affiché seulement en développement ; journal dans `storage/logs/`.

## Démarrer en local

Prérequis : Docker.

```sh
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php bin/seed.php --fresh   # base + données de démonstration
```

| | |
|---|---|
| Site | http://localhost:8090 |
| Back-office | http://localhost:8090/admin (compte : `SEED_ADMIN_EMAIL` / `SEED_ADMIN_PASSWORD` de `.env`) |
| E-mails envoyés (Mailpit) | http://localhost:8026 |
| Base de données (Adminer) | http://localhost:8091 (serveur `db`) |
| Galerie des composants | http://localhost:8090/dev/composants |

## Commandes

```sh
docker compose exec app composer test                 # tests PHPUnit
docker compose exec app php bin/migrate.php           # migrations en attente
docker compose exec app php bin/seed.php --fresh      # réinitialiser la base de dev
docker compose exec app php bin/tokens.php            # regénérer tokens.css depuis design/tokens.json
docker compose exec app php bin/create-admin.php email "Nom"
```

## Déploiement

Voir [docs/DEPLOIEMENT.md](docs/DEPLOIEMENT.md) : prérequis, installation, HTTPS, e-mails (SPF / DKIM),
mises à jour (`bin/deploy.sh`) et sauvegardes (`bin/backup.sh`, `bin/restore.sh`).

## Design

La spécification (charte, tokens, composants, fiches de pages, journal des décisions) est dans
[design/DESIGN.md](design/DESIGN.md). Les variables CSS sont générées depuis `design/tokens.json`.
