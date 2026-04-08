#!/bin/bash
set -euo pipefail

# Thor Metal Art — Database Restore
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
CONTAINER="tma_dev_mysql"
BACKUP_FILE="${1:-}"

if [[ -z "${BACKUP_FILE}" ]]; then
    echo "Uso: $0 <archivo.sql.gz>"
    echo "Backups disponibles:"
    ls -lht "${PROJECT_DIR}/backups/"*.sql.gz 2>/dev/null || echo "  (ninguno)"
    exit 1
fi

[[ -f "${PROJECT_DIR}/.env" ]] || { echo "ERROR: .env not found at ${PROJECT_DIR}/.env"; exit 1; }
source "${PROJECT_DIR}/.env"

echo "ATENCION: Esto sobreescribira la base de datos ${MYSQL_DATABASE}"
read -p "Continuar? (y/N): " confirm
if [[ "${confirm}" != "y" ]]; then
    echo "Cancelado."
    exit 0
fi

echo "Restaurando desde ${BACKUP_FILE}..."
gunzip -c "${BACKUP_FILE}" | docker exec -i "${CONTAINER}" mysql \
    -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}"

echo "Restauracion completada."
