---
description: "Use when editing Redis configuration, cache scripts, eviction policies, or memory tuning. Covers Redis 7 with password auth and WordPress object cache integration."
applyTo: ["docker-compose.yml", "scripts/clear-cache.sh"]
---
# Redis Cache — Thor Metal Art

## Current Configuration
- **Image**: `redis:7-alpine`
- **Memory**: 64MB max (`--maxmemory 64mb`)
- **Eviction**: `allkeys-lru` — evicts least recently used keys when full
- **Auth**: `--requirepass ${REDIS_PASSWORD}` (from `.env`)
- **Container**: `tma_dev_redis`
- **Health check**: `redis-cli -a $REDIS_PASSWORD ping | grep PONG`
- **Volume**: `redis_data:/data` (persistent)

## Rules

### Cache Flushing
- **Always** use `FLUSHDB` (current database 0 only)
- **Never** use `FLUSHALL` — blocked by security hook
- Flush script: `scripts/clear-cache.sh` (validates `REDIS_PASSWORD` before executing)

### Memory Tuning
- Keep `allkeys-lru` unless workload is mostly write-heavy (then consider `volatile-lru`)
- Monitor hit rate before changing maxmemory: `docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO stats`
- If hit rate < 50%, increase `--maxmemory` in `docker-compose.yml` redis command

### WordPress Integration
- WP-Redis plugin uses `WP_REDIS_HOST`, `WP_REDIS_PORT`, `WP_REDIS_PASSWORD` from `wp-config.php`
- These are set via `WORDPRESS_CONFIG_EXTRA` in `.env`
- Object cache stored in DB 0 — don't change without updating both Redis and WP configs

### Security
- Redis is **never** exposed externally — internal network only
- Always use `--no-auth-warning` flag in CLI commands to suppress password in logs
- Password comes from `.env` via `REDIS_PASSWORD` — never hardcode

### Diagnostics
```bash
# Quick health check
docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning ping

# Memory and hit rate
docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO memory
docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO stats

# Key count
docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning DBSIZE
```
