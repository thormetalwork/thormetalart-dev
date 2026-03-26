---
description: "Use when writing or editing shell scripts: backup, restore, deployment, testing, cache management. Covers bash best practices, .env validation, and Docker CLI patterns."
applyTo: "scripts/**"
---
# Shell Script Guidelines — Thor Metal Art

## Required Patterns
- Always start with `#!/bin/bash` and `set -e`
- Quote all variables: `"${VAR}"` not `$VAR`
- Validate `.env` exists before sourcing: `[[ -f .env ]] || { echo "Missing .env"; exit 1; }`
- Use `docker compose` (v2 syntax, not `docker-compose`)

## Existing Scripts
| Script | Purpose |
|--------|---------|
| `backup-database.sh` | MySQL dump → gzip, 10-file rotation in `/backups/` |
| `restore-database.sh` | Restore from `.sql.gz` with confirmation prompt |
| `clear-cache.sh` | Redis FLUSHALL |
| `test-connections.sh` | Test MySQL, Redis, WordPress, phpMyAdmin connectivity |

## Docker Commands
- Container names: `thormetalart_mysql`, `thormetalart_redis`, `thormetalart_wordpress`
- MySQL exec: `docker compose exec mysql mysqladmin -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" ping`
- Redis exec: `docker compose exec redis redis-cli ping`
- WordPress check: `curl -sf http://localhost/wp-login.php`

## Backup Convention
- Filename: `thormetalart_wp_YYYYMMDD_HHMMSS.sql.gz`
- Location: `/backups/` (project root)
- Rotation: Keep last 10, delete older

## Error Handling
- Use colored output: green for success, red for errors, yellow for warnings
- Exit codes: 0 success, 1 general error, 2 missing dependencies
- Log operations to stdout with timestamps
