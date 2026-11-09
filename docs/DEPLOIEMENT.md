# Mise en ligne et exploitation

Ce guide décrit comment mettre le site en production et le maintenir.

**Hébergement retenu (issue #44)** : VPS en Docker (cas B), derrière le reverse proxy commun Caddy
(dépôt `side-projects/caddy`, installé dans `/opt/caddy`), qui reçoit les ports 80 et 443, gère HTTPS
et transmet les requêtes au site sur le réseau Docker `proxy`. VPS KVM1 Hostinger jusqu'au 17 janvier
2027, puis VPS-1 OVHcloud (voir « Changer de serveur »). Le cas A reste décrit pour mémoire.

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

### B. VPS avec Docker (retenu)

Fichiers : `compose.prod.yaml` (site + PostgreSQL avec pgvector), `bin/deploy-docker.sh`. Le site ne
publie aucun port : Caddy le joint sous le nom `portfolio`. Les fichiers envoyés et les journaux sont dans
`storage/`, sur le disque du serveur ; la base dans le volume Docker `portfolio_pgdata`.

En root, sur le serveur (le réseau `proxy` et Caddy existent déjà) :

```sh
git clone -b v2 https://github.com/Bruno-AMUZU/Bruno-AMUZU.GitHub.io.git /opt/portfolio && cd /opt/portfolio
cp .env.production.example .env && chmod 600 .env
openssl rand -hex 24                   # → DB_PASSWORD
nano .env                              # remplir chaque valeur
sh bin/deploy-docker.sh                # image, base, migrations, vérification
docker compose exec app php bin/create-admin.php vous@exemple.fr "Bruno Amuzu"
```

Puis activer le bloc `brunoamuzu.fr` dans le Caddyfile et recharger Caddy
(`docker exec caddy caddy reload --config /etc/caddy/Caddyfile`). **Ni Mailpit ni Adminer en production.**

Derrière Caddy, Apache (`mod_remoteip`) reprend l'adresse du visiteur dans `X-Forwarded-For`, seulement si
la requête vient des réseaux Docker : les limites de tentatives restent propres à chaque visiteur.

## 3. Domaine brunoamuzu.fr et HTTPS (issue #46)

Domaine acheté chez OVH, zone DNS chez OVH.

1. Zone DNS : `A` sur `@` vers l'adresse IP du VPS, `CNAME` `www` vers `brunoamuzu.fr.`.
2. HTTPS : Caddy obtient et renouvelle le certificat dès que le bloc `brunoamuzu.fr` est actif.
3. `http://` vers `https://` : automatique avec Caddy ; `www.` vers le domaine nu : bloc `redir` du Caddyfile.
4. Vérifier : `APP_URL=https://brunoamuzu.fr` dans `.env` ; l'en-tête HSTS est envoyé automatiquement en production.

## 4. E-mails : SMTP, SPF, DKIM (issue #47)

1. Créer l'adresse d'envoi (`no-reply@brunoamuzu.fr`) chez l'hébergeur, ou un compte chez un service d'envoi (Brevo, Mailjet…).
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

Avec Docker (VPS) :

```sh
cd /opt/portfolio && sh bin/deploy-docker.sh   # code, image, sauvegarde, migrations, redémarrage, vérification
```

Le script déploie la branche en place (`v2` jusqu'à la fusion dans `main`). Le conteneur est recréé :
OPcache repart à neuf, rien à vider.

## 6. Sauvegardes (issue #48)

- `sh bin/backup.sh` : base + fichiers envoyés dans `backups/AAAA-MM-JJ_HHMMSS/` (14 dernières gardées).
- Planifier chaque nuit : `0 3 * * * cd /chemin/du/site && sh bin/backup.sh >> storage/logs/backup.log 2>&1`
- Planifier aussi la suppression des messages de plus de 12 mois (durée annoncée par la mention RGPD) :
  `30 3 * * * cd /chemin/du/site && php bin/purge-messages.php >> storage/logs/purge.log 2>&1`
  (avec Docker : `30 3 * * * cd /opt/portfolio && docker compose exec -T app php bin/purge-messages.php >> storage/logs/purge.log 2>&1`)
  (le site la fait déjà à chaque message reçu et à l'ouverture des Messages ; la tâche la garantit sans activité).
- **Copier `backups/` hors du serveur** (autre machine, stockage externe) : une sauvegarde sur le même disque
  ne protège pas d'une panne.
- Restaurer : `sh bin/restore.sh backups/AAAA-MM-JJ_HHMMSS` (confirmation demandée). Tester une restauration
  au moins une fois après la mise en ligne.

## 7. Changer de serveur (Hostinger → OVH, avant le 17 janvier 2027)

1. Sur l'ancien serveur : `sh bin/backup.sh`, puis copier le dossier de sauvegarde et le `.env` sur le nouveau.
2. Sur le nouveau : Docker, `docker network create proxy`, Caddy, puis l'installation du cas B (sans
   `create-admin`) et `CONFIRM=oui sh bin/restore.sh backups/…`.
3. La veille, baisser le TTL des enregistrements DNS (300 s) ; le jour J, remplacer l'adresse IP du `A @`.

## 8. Après la mise en ligne

- Renommer le dépôt et rediriger l'ancien site GitHub Pages vers brunoamuzu.fr (issue #51).
- Déclarer le sitemap (`https://brunoamuzu.fr/sitemap.xml`) dans Google Search Console.
- Supprimer les données de démonstration : en production, la base démarre vide (pas de `bin/seed.php`).
