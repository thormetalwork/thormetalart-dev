---
description: "View and filter container logs for a specific service or all services"
agent: "devops"
argumentDescription: "Service name: wp, mysql, redis, pma, traefik (or 'all')"
---
Show container logs for the Thor Metal Art stack:

1. If a service is specified, show the last 50 lines:
   - `wp` → `make logs-wp`
   - `mysql` → `make logs-mysql`
   - `redis` → `docker compose logs --tail=50 redis`
   - `pma` → `docker compose logs --tail=50 phpmyadmin`
   - `all` → `make logs` (tail all services, last 30 lines each)

2. Scan logs for issues:
   - **Errors**: grep for `ERROR`, `Fatal`, `Warning`, `failed`
   - **Slow queries**: MySQL slow query indicators
   - **OOM**: Redis memory warnings
   - **HTTP 5xx**: WordPress/PHP fatal errors

3. Report: summary of recent activity, any errors/warnings found, and suggested actions

If no service is specified, default to `all` with error highlighting.
