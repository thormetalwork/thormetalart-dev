# Service Endpoints — Thor Metal Art

## Internal Services (Docker Network)

| Service | Internal Host | Port | Protocol |
|---------|--------------|------|----------|
| MySQL | `mysql` | 3306 | TCP |
| Redis | `redis` | 6379 | TCP |
| WordPress | `wordpress` | 80 | HTTP |

## External Access (via Traefik)

| Service | URL | Notes |
|---------|-----|-------|
| WordPress | `dev.thormetalart.com` | Main website |
| phpMyAdmin | `pma-thormetalart.server-dev` | Database admin |
| Dashboard | `dashboard.thormetalart.server-dev` | Client dashboard |

## Local Access

| Service | Host | Port | Notes |
|---------|------|------|-------|
| MySQL | `127.0.0.1` | 3311 | Local only, NOT 0.0.0.0 |

## Health Check Endpoints

| Service | Command/URL | Expected Response |
|---------|------------|-------------------|
| MySQL | `mysqladmin ping` | `mysqld is alive` |
| Redis | `redis-cli ping` | `PONG` |
| WordPress | `curl http://localhost/wp-login.php` | HTTP 200 |
| phpMyAdmin | `curl http://localhost/` | HTTP 200 |
| Dashboard | `curl http://localhost/` | HTTP 200 |
