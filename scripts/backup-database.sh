#!/bin/bash
set -euo pipefail

# Thor Metal Art — Database Backup
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="${PROJECT_DIR}/backups"
CONTAINER="tma_dev_mysql"

[[ -f "${PROJECT_DIR}/.env" ]] || { echo "ERROR: .env not found at ${PROJECT_DIR}/.env"; exit 1; }
source "${PROJECT_DIR}/.env"

mkdir -p "${BACKUP_DIR}"

BACKUP_FILE="${BACKUP_DIR}/${MYSQL_DATABASE}_${TIMESTAMP}.sql.gz"

echo "Backing up ${MYSQL_DATABASE}..."
set -o pipefail
docker exec "${CONTAINER}" bash -c "MYSQL_PWD='${MYSQL_ROOT_PASSWORD}' mysqldump \
    -u root \
    --single-transaction \
    --routines \
    --triggers \
    '${MYSQL_DATABASE}'" | gzip > "${BACKUP_FILE}"

# Verify backup is not empty (minimum ~200 bytes for valid gzip)
BACKUP_SIZE=$(stat -c%s "${BACKUP_FILE}" 2>/dev/null || stat -f%z "${BACKUP_FILE}")
if [[ "${BACKUP_SIZE}" -lt 200 ]]; then
  echo "ERROR: Backup file is suspiciously small (${BACKUP_SIZE} bytes). Removing." >&2
  rm -f "${BACKUP_FILE}"
  exit 1
fi

echo "Backup saved: ${BACKUP_FILE}"
ls -lh "${BACKUP_FILE}"

# Mantener solo los ultimos 10 backups
cd "${BACKUP_DIR}" && ls -t *.sql.gz 2>/dev/null | tail -n +11 | xargs -r rm --
echo "Done."
