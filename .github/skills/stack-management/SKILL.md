---
name: stack-management
description: "Manage the Thor Metal Art Docker stack lifecycle: start, stop, rebuild, backup, restore, test connections, clear cache, monitor logs. Use when performing infrastructure operations, troubleshooting containers, or managing deployments."
argument-hint: "Operation to perform (e.g., deploy, backup, troubleshoot redis)"
---

# Stack Management — Thor Metal Art

## When to Use
- Starting, stopping, or restarting the Docker stack
- Rebuilding containers after Dockerfile or compose changes
- Creating or restoring database backups
- Testing service connectivity
- Clearing Redis cache
- Troubleshooting container issues
- Monitoring logs for errors

## Stack Services

| Service | Container | Health Check | Port |
|---------|-----------|-------------|------|
| MySQL 8.0 | tma_dev_mysql | `mysqladmin ping` | 127.0.0.1:3311 |
| Redis 7 | tma_dev_redis | `redis-cli ping` | internal |
| WordPress 6.9 | tma_dev_wordpress | `curl wp-login.php` | via Traefik |
| phpMyAdmin | tma_dev_phpmyadmin | `curl /` | via Traefik |

## Procedures

### Start Stack
```bash
make up
make test    # Verify all healthy
```

### Safe Deploy (with backup)
```bash
make backup  # Always backup first
make build   # Rebuild without cache
make test    # Verify connections
make logs    # Check for errors
```

### Troubleshoot Service
1. Check status: `make status`
2. Check logs: `make logs` or `make logs-wp` / `make logs-mysql`
3. Enter container: `make shell-wp` or `make shell-mysql`
4. Test connections: `make test`
5. If needed, restart: `make restart`

### Database Backup & Restore
- Backup: `make backup` → Creates `/backups/thormetalart_wp_YYYYMMDD_HHMMSS.sql.gz`
- Restore: `bash scripts/restore-database.sh /backups/FILENAME.sql.gz`
- Rotation: Keeps last 10 backups automatically

### Clear Cache
```bash
bash scripts/clear-cache.sh    # Flushes all Redis cache
```

### Emergency Recovery
1. `make backup` (if MySQL is accessible)
2. `make down`
3. Check `docker-compose.yml` and `.env` for issues
4. `make build`
5. `make test`
6. If DB corrupt: `bash scripts/restore-database.sh /backups/LATEST.sql.gz`

## Reference Files
- [Docker Compose configuration](../../docker-compose.yml)
- [WordPress Dockerfile](../../docker/wordpress/Dockerfile)
- [Backup script](./scripts/backup-check.sh)
- [Connection test reference](./references/service-endpoints.md)
