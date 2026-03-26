#!/bin/bash
set -euo pipefail

# Thor Metal Art — Database Backup
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/srv/stacks/thormetalart/backups"
CONTAINER="thormetalart_mysql"

source /srv/stacks/thormetalart/.env

mkdir -p ""

echo "Backing up thormetalart_wp..."
docker exec "" mysqldump \
    -u root -p"" \
    --single-transaction \
    --routines \
    --triggers \
    "" | gzip > "/thormetalart_.sql.gz"

echo "Backup saved: /thormetalart_.sql.gz"
ls -lh "/thormetalart_.sql.gz"

# Mantener solo los ultimos 10 backups
cd "" && ls -t *.sql.gz 2>/dev/null | tail -n +11 | xargs -r rm --
echo "Done."
