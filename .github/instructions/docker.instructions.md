---
description: "Use when editing Docker files: Dockerfile, docker-compose.yml, container configuration. Covers health checks, networking, resource limits, and multi-service orchestration."
applyTo: ["docker-compose.yml", "docker/**", "**/Dockerfile"]
---
# Docker Guidelines — Thor Metal Art

## Services Architecture
- **MySQL 8.0**: Internal only (`127.0.0.1:3311`), persistent volume at `data/mysql/`
- **Redis 7-alpine**: 128MB max, LRU eviction, WP Object Cache
- **WordPress**: Custom Dockerfile with PECL Redis, PHP 8.1-apache
- **phpMyAdmin**: Admin access via Traefik
- **Dashboard (Nginx)**: Static HTML dashboard, Phase 2

## Required Patterns
- Every service MUST have a `healthcheck` with `interval`, `timeout`, `retries`
- Use `depends_on: { service: { condition: service_healthy } }` — never bare `depends_on`
- Container names use prefix `thormetalart_` (e.g., `thormetalart_wordpress`)
- MySQL MUST NOT be exposed to `0.0.0.0` — use `127.0.0.1:PORT` only
- Resource limits: set `mem_limit` for Redis and phpMyAdmin
- Networks: `thormetalart_network` (internal), `traefik-public` (external)

## Traefik Labels
```yaml
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.SERVICE.rule=Host(`DOMAIN`)"
  - "traefik.docker.network=traefik-public"
```

## Environment
- All secrets via `.env` file (never hardcode)
- WordPress DB prefix: `tma_`
- Reference variables: `${MYSQL_ROOT_PASSWORD}`, `${MYSQL_DATABASE}`, `${MYSQL_USER}`, `${MYSQL_PASSWORD}`
