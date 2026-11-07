#!/bin/sh
# Restaure une sauvegarde faite par bin/backup.sh : remplace la base ET les fichiers envoyés.
#   sh bin/restore.sh backups/2026-11-07_030000
#
# Irréversible : faire d'abord une sauvegarde de l'état actuel pour pouvoir revenir en arrière.
set -eu

cd "$(dirname "$0")/.."
SRC="${1:-}"
if [ -z "$SRC" ] || [ ! -f "$SRC/database.sql.gz" ] || [ ! -f "$SRC/uploads.tar.gz" ]; then
    echo "Usage : sh bin/restore.sh backups/AAAA-MM-JJ_HHMMSS" >&2
    exit 1
fi

if [ "${CONFIRM:-}" != "oui" ]; then
    printf 'La base et storage/uploads/ vont être remplacés par %s. Taper « oui » pour continuer : ' "$SRC"
    read -r answer
    [ "$answer" = "oui" ] || { echo "Annulé."; exit 1; }
fi

# Variables DB_* de .env, lues ligne à ligne (le fichier n'est pas exécuté comme un script).
if [ -f .env ]; then
    while IFS='=' read -r key value; do
        case "$key" in
            DB_HOST|DB_PORT|DB_NAME|DB_USER|DB_PASSWORD) export "$key=$value" ;;
        esac
    done < .env
fi

if [ -n "$(docker compose ps -q db 2>/dev/null)" ]; then
    gunzip -c "$SRC/database.sql.gz" | docker compose exec -T db sh -c 'psql -q -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -d "$POSTGRES_DB"' >/dev/null
else
    gunzip -c "$SRC/database.sql.gz" | PGPASSWORD="$DB_PASSWORD" psql -q -v ON_ERROR_STOP=1 -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "$DB_USER" -d "$DB_NAME" >/dev/null
fi

rm -rf storage/uploads.restore && mkdir storage/uploads.restore
tar -xzf "$SRC/uploads.tar.gz" -C storage/uploads.restore
rm -rf storage/uploads && mv storage/uploads.restore/uploads storage/uploads && rmdir storage/uploads.restore

echo "Restauration terminée depuis $SRC."
