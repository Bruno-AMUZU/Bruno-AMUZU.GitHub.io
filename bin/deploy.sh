#!/bin/sh
# Met à jour le site sur le serveur (hébergement sans Docker, accès SSH), depuis la racine du projet :
#   sh bin/deploy.sh
#
# 1. récupère la dernière version de la branche de production ;
# 2. installe les dépendances sans les outils de développement ;
# 3. applique les migrations de la base ;
# 4. vérifie que la page d'accueil répond.
# Une sauvegarde est faite avant les migrations : en cas de problème, sh bin/restore.sh backups/…
set -eu

cd "$(dirname "$0")/.."
BRANCH="${DEPLOY_BRANCH:-main}"

if [ ! -f .env ] || ! grep -q '^APP_ENV=prod' .env; then
    echo "Refusé : .env absent ou APP_ENV différent de prod." >&2
    exit 1
fi

echo "→ Code ($BRANCH)"
git fetch --quiet origin "$BRANCH"
git checkout --quiet "$BRANCH"
git merge --ff-only --quiet "origin/$BRANCH"   # jamais de fusion automatique sur le serveur

echo "→ Dépendances"
composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction --quiet

echo "→ Sauvegarde avant migration"
sh bin/backup.sh

echo "→ Migrations"
php bin/migrate.php

# OPcache garde l'ancien code en mémoire (validate_timestamps = 0) : il faut le vider.
# Selon l'hébergeur : redémarrer PHP depuis le panneau, ou appeler opcache_reset() via une URL protégée.
echo "→ Pensez à vider OPcache (redémarrage de PHP) si l'hébergeur ne le fait pas."

URL="$(grep '^APP_URL=' .env | cut -d= -f2)"
if [ -n "$URL" ] && command -v curl >/dev/null 2>&1; then
    CODE="$(curl -s -o /dev/null -w '%{http_code}' "$URL/fr")"
    echo "→ Vérification : $URL/fr répond $CODE"
    [ "$CODE" = "200" ] || exit 1
fi

echo "Déploiement terminé."
