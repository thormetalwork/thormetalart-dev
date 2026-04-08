#!/bin/bash
set -euo pipefail

# Thor Metal Art — Test Connections
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

echo "=== Testing Thor Metal Art Stack ==="

[[ -f "${PROJECT_DIR}/.env" ]] || { echo "ERROR: .env not found at ${PROJECT_DIR}/.env"; exit 1; }
source "${PROJECT_DIR}/.env"

echo -n "MySQL............ "
docker exec tma_dev_mysql bash -c "MYSQL_PWD='${MYSQL_ROOT_PASSWORD}' mysqladmin ping -u root --silent" 2>/dev/null && echo "OK" || echo "FAIL"

echo -n "Redis............ "
docker exec tma_dev_redis redis-cli -a "${REDIS_PASSWORD}" --no-auth-warning ping 2>/dev/null || echo "FAIL"

echo -n "WordPress........ "
docker exec tma_dev_wordpress curl -sf http://localhost/wp-login.php -o /dev/null && echo "OK" || echo "FAIL (puede tardar en arrancar)"

echo -n "phpMyAdmin....... "
docker exec tma_dev_phpmyadmin curl -sf http://localhost/ -o /dev/null && echo "OK" || echo "FAIL"

echo ""
docker compose -f "${PROJECT_DIR}/docker-compose.yml" ps
