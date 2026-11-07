# Mise en ligne et exploitation

Ce guide décrit comment mettre le site en production et le maintenir. L'hébergeur n'est pas encore
choisi (issue #44) : les deux cas courants sont décrits.

## 1. Prérequis de l'hébergeur

| Besoin | Pourquoi |
|---|---|
| PHP 8.4 avec `pdo_pgsql` et `opcache` | Le site |
| PostgreSQL 15 ou plus | La base (pgvector en plus pour l'assistant IA, V2.2) |
| Apache avec `.htaccess` (`mod_rewrite`, `mod_headers`) **ou** Docker | Routage vers `public/index.php` |
| Accès SSH, Git et Composer | Déploiement |
| Racine web réglable sur `public/` | Sécurité : rien d'autre ne doit être servi |
| HTTPS (Let's Encrypt) | Obligatoire (cookies `Secure`, HSTS) |
| Envoi SMTP | Notifications du formulaire, mot de passe oublié |
| Tâches planifiées (cron) | Sauvegardes |

Peu d'hébergements mutualisés proposent PostgreSQL : à vérifier avant de choisir.

## 2. Première installation

### A. Hébergement sans Docker (mutualisé ou VPS avec Apache)

```sh
git clone https://github.com/Bruno-AMUZU/<depot>.git portfolio && cd portfolio
cp .env.production.example .env        # puis remplir chaque valeur
composer install --no-dev --optimize-autoloader --classmap-authoritative
php bin/migrate.php
php bin/create-admin.php vous@exemple.fr "Bruno Amuzu"
```

- Régler la **racine web** du domaine sur le dossier `public/` (panneau de l'hébergeur).
- Utiliser les réglages de `docker/php/php.prod.ini` (erreurs masquées, cookies sécurisés, envois de 12 Mo).
- `storage/` doit être accessible en écriture par PHP.

### B. VPS avec Docker

L'image de production se construit avec la cible `prod` du Dockerfile :

```sh
docker build --target prod -t portfolio:prod -f docker/php/Dockerfile .
docker run -d --name portfolio --env-file .env \
  -v portfolio_uploads:/var/www/html/storage/uploads \
  -v portfolio_logs:/var/www/html/storage/logs \
  -p 127.0.0.1:8080:80 portfolio:prod
docker exec portfolio php bin/migrate.php
docker exec -it portfolio php bin/create-admin.php vous@exemple.fr "Bruno Amuzu"
```

Un reverse proxy (Caddy, Traefik ou nginx) placé devant gère le HTTPS. **Ni Mailpit ni Adminer en production.**

## 3. Domaine brunoamuzu.dev et HTTPS (issue #46)

1. Chez le registrar : enregistrement `A` (et `AAAA`) vers l'adresse IP du serveur, ou les DNS de l'hébergeur.
2. Activer le certificat HTTPS (Let's Encrypt) dans le panneau, ou via le reverse proxy.
3. Rediriger `http://` vers `https://` et `www.` vers le domaine nu (règle de l'hébergeur).
4. Vérifier : `APP_URL=https://brunoamuzu.dev` dans `.env` ; l'en-tête HSTS est envoyé automatiquement en production.

## 4. E-mails : SMTP, SPF, DKIM (issue #47)

1. Créer l'adresse d'envoi (`no-reply@brunoamuzu.dev`) chez l'hébergeur, ou un compte chez un service d'envoi (Brevo, Mailjet…).
2. Renseigner `MAIL_HOST`, `MAIL_PORT` (587), `MAIL_USER`, `MAIL_PASSWORD`, `MAIL_FROM` dans `.env`.
3. Ajouter dans la zone DNS du domaine les enregistrements fournis par le service :
   - **SPF** : `TXT` sur `@`, du type `v=spf1 include:<service> ~all` (un seul enregistrement SPF par domaine) ;
   - **DKIM** : `TXT` (ou `CNAME`) sur le sélecteur indiqué, ex. `mail._domainkey` ;
   - **DMARC** : `TXT` sur `_dmarc`, pour commencer `v=DMARC1; p=none; rua=mailto:vous@exemple.fr`.
4. Tester : envoyer un message par le formulaire de contact et vérifier qu'il arrive **hors des spams**, puis
   « Mot de passe oublié » depuis `/admin/connexion`.

Le message de contact est enregistré en base avant l'envoi : même si l'e-mail échoue, il est dans **Messages**.

## 5. Mises à jour

Sur un hébergement sans Docker :

```sh
sh bin/deploy.sh          # code, dépendances, sauvegarde, migrations, vérification
```

Puis vider OPcache (redémarrer PHP depuis le panneau), car `opcache.validate_timestamps = 0`.

Avec Docker : reconstruire l'image, remplacer le conteneur, puis `docker exec portfolio php bin/migrate.php`.

## 6. Sauvegardes (issue #48)

- `sh bin/backup.sh` : base + fichiers envoyés dans `backups/AAAA-MM-JJ_HHMMSS/` (14 dernières gardées).
- Planifier chaque nuit : `0 3 * * * cd /chemin/du/site && sh bin/backup.sh >> storage/logs/backup.log 2>&1`
- **Copier `backups/` hors du serveur** (autre machine, stockage externe) : une sauvegarde sur le même disque
  ne protège pas d'une panne.
- Restaurer : `sh bin/restore.sh backups/AAAA-MM-JJ_HHMMSS` (confirmation demandée). Tester une restauration
  au moins une fois après la mise en ligne.

## 7. Après la mise en ligne

- Renommer le dépôt et rediriger l'ancien site GitHub Pages vers brunoamuzu.dev (issue #51).
- Déclarer le sitemap (`https://brunoamuzu.dev/sitemap.xml`) dans Google Search Console.
- Supprimer les données de démonstration : en production, la base démarre vide (pas de `bin/seed.php`).
