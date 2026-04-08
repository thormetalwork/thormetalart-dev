---
description: "Use when editing .env, .env.example, or environment configuration files. Covers required variables, security validation, and format consistency."
applyTo: ["**/.env*", "**/docker-compose.yml"]
---
# Environment Configuration Guidelines

## Required Variables
All `.env` files MUST define these variables (see `.env.example` for template):
- `MYSQL_ROOT_PASSWORD` — strong, unique, never reuse
- `MYSQL_DATABASE` — must be `thormetalart_wp`
- `MYSQL_USER` — must be `thormetalart`
- `MYSQL_PASSWORD` — strong, unique
- `WORDPRESS_DB_HOST` — must be `mysql` (Docker service name)
- `WORDPRESS_DB_NAME` — must match `MYSQL_DATABASE`
- `WORDPRESS_DB_USER` — must match `MYSQL_USER`
- `WORDPRESS_DB_PASSWORD` — must match `MYSQL_PASSWORD`
- `WORDPRESS_TABLE_PREFIX` — must be `tma_`

## Security Rules
- NEVER commit `.env` — it's in `.gitignore`
- NEVER use default passwords like `CHANGE_ME`, `password`, `root`, `admin`
- NEVER echo credentials in scripts — use `set +x` around sensitive sections
- Passwords MUST be 16+ characters with mixed case, numbers, symbols

## Validation Pattern
Shell scripts that read `.env` must validate before operations:
```bash
set -e
[[ -f .env ]] || { echo "ERROR: .env not found"; exit 1; }
source .env
[[ -n "$MYSQL_PASSWORD" ]] || { echo "ERROR: MYSQL_PASSWORD not set"; exit 1; }
```
