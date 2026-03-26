# Thor Metal Art — Project Guidelines

## Project Overview

Thor Metal Art is a Docker-based production stack for a custom metal fabrication and sculpture business (Miami-Dade, FL). Client: Karel Frometa / Thor Metal Art LLC.

**Stack:** WordPress 6.9 + PHP 8.1 + MySQL 8.0 + Redis 7 + Nginx Dashboard + Traefik reverse proxy.

## Architecture

```
┌─────────────┐     ┌──────────────────────────────────────────┐
│   Traefik   │────▶│  thormetalart_network (internal)         │
│  (external) │     │  ┌──────────┐ ┌───────┐ ┌─────────────┐ │
└──────┬──────┘     │  │ WordPress│─│ MySQL │─│   Redis     │ │
       │            │  │ :80      │ │ :3306 │ │   :6379     │ │
       │            │  └──────────┘ └───────┘ └─────────────┘ │
       │            └──────────────────────────────────────────┘
       │
       ├──▶ dev.thormetalart.com           → WordPress
       ├──▶ pma-thormetalart.server-dev    → phpMyAdmin
       └──▶ dashboard.thormetalart.server-dev → Dashboard (Nginx)
```

**Key decisions:**
- MySQL exposed only on `127.0.0.1:3311` (local access)
- Redis: 128MB limit, LRU eviction policy for WP Object Cache
- WordPress custom Dockerfile with PECL Redis extension
- Dashboard is Phase 2 (currently static demo data)
- All services have health checks with retries

## Code Style & Conventions

- **Language:** Bilingual EN/ES — all user-facing content must support both languages
- **PHP:** WordPress coding standards (WPCS)
- **Shell scripts:** Use `set -e`, quote variables, validate `.env` before operations
- **HTML/CSS/JS:** Vanilla JS, no frameworks; Chart.js 4.x for visualizations
- **Docker:** Use health checks, depend on `service_healthy`, limit resources

## Branding

| Element | Value |
|---------|-------|
| Primary | `#1A1A1A` (Negro) |
| Accent | `#B8860B` (Oro/DarkGoldenrod) |
| Background | `#F5F5F0` (Blanco roto) |
| Display font | Cormorant Garamond |
| Body font | DM Sans / Inter |
| Tone | Directo, técnico-accesible |

## Build and Test

```bash
make up          # Start stack
make down        # Stop stack
make build       # Rebuild without cache
make test        # Test all connections
make backup      # Backup database
make logs        # Tail all logs
make shell-wp    # WordPress container shell
make shell-mysql # MySQL container shell
```

## Environment

- Secrets in `.env` (never commit — in `.gitignore`)
- Database: `thormetalart_wp`, user: `thormetalart`, prefix: `tma_`
- Backups: `/backups/` with 10-file rotation

## File Structure

| Path | Purpose |
|------|---------|
| `docker-compose.yml` | Service orchestration |
| `docker/wordpress/Dockerfile` | Custom WP image with Redis |
| `dashboard/` | Client executive dashboard (Nginx) |
| `scripts/` | Operational scripts (backup, restore, test, cache) |
| `data/wordpress/` | WordPress files (volume mount) |
| `data/mysql/` | MySQL data (volume mount) |
| `docs/` | Project documentation and client deliverables |

## Security

- Never expose database credentials in code or logs
- MySQL only on localhost, external access via phpMyAdmin + Traefik
- WordPress table prefix `tma_` (non-default)
- All `.env`, `data/`, `backups/`, `*.sql.gz` in `.gitignore`
