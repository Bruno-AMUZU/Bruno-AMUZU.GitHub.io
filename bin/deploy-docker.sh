#!/bin/sh
# Met à jour le site sur le VPS (Docker, compose.prod.yaml), en root, depuis la racine du projet :
#   sh bin/deploy-docker.sh
#
# 1. récupère la dernière version de la branche en place (v2 jusqu'à la fusion dans main) ;
# 2. reconstruit l'image : un échec laisse la version en service intacte ;
# 3. sauvegarde la base et les fichiers avant toute migration ;
# 4. applique les migrations, puis remplace le conteneur (OPcache repart à neuf) ;
# 5. vérifie que le site répond.
# En cas de problème : sh bin/restore.sh backups/…
set -eu

cd "$(dirname "$0")/.."

if [ ! -f .env ] || ! grep -q '^APP_ENV=prod' .env; then
    echo "Refusé : .env absent ou APP_ENV différent de prod." >&2
    exit 1
fi
if ! grep -q '^COMPOSE_FILE=compose.prod.yaml' .env; then
    echo "Refusé : COMPOSE_FILE=compose.prod.yaml manquant dans .env." >&2
    exit 1
fi

BRANCH="$(git rev-parse --abbrev-ref HEAD)"
echo "→ Code ($BRANCH)"
git fetch --quiet origin "$BRANCH"
git merge --ff-only --quiet "origin/$BRANCH"   # jamais de fusion automatique sur le serveur

echo "→ Image"
docker compose build --pull app

# Fichiers envoyés et journaux : écrits par Apache (www-data, uid 33) dans le conteneur.
mkdir -p storage/uploads storage/logs
chown -R 33:33 storage/uploads storage/logs

if [ -n "$(docker compose ps -q db 2>/dev/null)" ]; then
    echo "→ Sauvegarde avant migration"
    sh bin/backup.sh
fi

echo "→ Migrations (avec la nouvelle image, avant qu'elle ne serve le site)"
docker compose up -d --wait db
docker compose run --rm -T app php bin/migrate.php

echo "→ Redémarrage"
docker compose up -d --wait

CODE="$(docker compose exec -T app curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1/fr || true)"
echo "→ Vérification : /fr répond $CODE"
[ "$CODE" = "200" ] || exit 1

docker image prune -f >/dev/null
echo "Déploiement terminé."
