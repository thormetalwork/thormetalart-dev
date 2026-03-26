#!/bin/bash
set -euo pipefail

# Thor Metal Art — Test Connections
echo "=== Testing Thor Metal Art Stack ==="

source /srv/stacks/thormetalart/.env

echo -n "MySQL............ "
docker exec thormetalart_mysql mysqladmin ping -u root -p"" 2>/dev/null && echo "OK" || echo "FAIL"

echo -n "Redis............ "
docker exec thormetalart_redis redis-cli ping 2>/dev/null || echo "FAIL"

echo -n "WordPress........ "
docker exec thormetalart_wordpress curl -sf http://localhost/wp-login.php -o /dev/null && echo "OK" || echo "FAIL (puede tardar en arrancar)"

echo -n "phpMyAdmin....... "
docker exec thormetalart_phpmyadmin curl -sf http://localhost/ -o /dev/null && echo "OK" || echo "FAIL"

echo ""
docker compose -f /srv/stacks/thormetalart/docker-compose.yml ps
