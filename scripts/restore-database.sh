#!/bin/bash
set -euo pipefail

# Thor Metal Art — Database Restore
CONTAINER="thormetalart_mysql"
BACKUP_FILE=""

if [ -z "" ]; then
    echo "Uso:  <archivo.sql.gz>"
    echo "Backups disponibles:"
    ls -lht /srv/stacks/thormetalart/backups/*.sql.gz 2>/dev/null || echo "  (ninguno)"
    exit 1
fi

source /srv/stacks/thormetalart/.env

echo "ATENCION: Esto sobreescribira la base de datos "
read -p "Continuar? (y/N): " confirm
if [ "" != "y" ]; then
    echo "Cancelado."
    exit 0
fi

echo "Restaurando desde ..."
gunzip -c "" | docker exec -i "" mysql \
    -u root -p"" ""

echo "Restauracion completada."
