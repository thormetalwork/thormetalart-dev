#!/bin/bash
set -euo pipefail

# Thor Metal Art — Clear Redis Cache (database 0 only)

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="${SCRIPT_DIR}/../.env"

if [[ -f "$ENV_FILE" ]]; then
  # shellcheck source=/dev/null
  source "$ENV_FILE"
fi

REDIS_PASS="${REDIS_PASSWORD:-}"

if [[ -z "$REDIS_PASS" ]]; then
  echo "ERROR: REDIS_PASSWORD not set in .env" >&2
  exit 1
fi

echo "Limpiando cache Redis (DB 0)..."
docker exec tma_dev_redis redis-cli -a "$REDIS_PASS" --no-auth-warning FLUSHDB
echo "Cache limpiado."
