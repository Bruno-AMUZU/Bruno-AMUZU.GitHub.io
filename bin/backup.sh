#!/bin/sh
# Sauvegarde la base et les fichiers envoyés dans backups/AAAA-MM-JJ_HHMMSS/.
#   sh bin/backup.sh            (depuis la racine du projet)
#
# - Base : pg_dump compressé (database.sql.gz), via le conteneur « db » si Docker Compose tourne,
#   sinon avec pg_dump installé sur le serveur et les identifiants de .env.
# - Fichiers : archive de storage/uploads/ (uploads.tar.gz).
# - Garde les 14 dernières sauvegardes. À planifier chaque nuit (cron), puis copier backups/
#   hors du serveur (autre machine, stockage externe) : une sauvegarde sur le même disque ne protège
#   pas d'une panne du serveur.
set -eu

cd "$(dirname "$0")/.."
KEEP=14
DEST="backups/$(date +%Y-%m-%d_%H%M%S)"
mkdir -p "$DEST"

# Variables DB_* de .env, lues ligne à ligne (le fichier n'est pas exécuté comme un script).
if [ -f .env ]; then
    while IFS='=' read -r key value; do
        case "$key" in
            DB_HOST|DB_PORT|DB_NAME|DB_USER|DB_PASSWORD) export "$key=$value" ;;
        esac
    done < .env
fi

if [ -n "$(docker compose ps -q db 2>/dev/null)" ]; then
    docker compose exec -T db sh -c 'pg_dump --no-owner --clean --if-exists -U "$POSTGRES_USER" "$POSTGRES_DB"' | gzip > "$DEST/database.sql.gz"
else
    PGPASSWORD="$DB_PASSWORD" pg_dump --no-owner --clean --if-exists -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "$DB_USER" "$DB_NAME" | gzip > "$DEST/database.sql.gz"
fi

tar -czf "$DEST/uploads.tar.gz" -C storage uploads

# Une sauvegarde vide est une sauvegarde ratée.
if [ ! -s "$DEST/database.sql.gz" ] || ! gzip -t "$DEST/database.sql.gz"; then
    echo "Échec : la sauvegarde de la base est vide ou corrompue." >&2
    exit 1
fi

# Rotation : on supprime les plus anciennes au-delà de $KEEP.
ls -1d backups/*/ 2>/dev/null | sort -r | tail -n +$((KEEP + 1)) | while read -r old; do rm -rf "$old"; done

echo "Sauvegarde : $DEST ($(du -sh "$DEST" | cut -f1))"
