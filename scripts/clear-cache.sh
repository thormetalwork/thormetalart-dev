#!/bin/bash
set -euo pipefail

# Thor Metal Art — Clear Redis Cache
echo "Limpiando cache Redis..."
docker exec thormetalart_redis redis-cli FLUSHALL
echo "Cache limpiado."
