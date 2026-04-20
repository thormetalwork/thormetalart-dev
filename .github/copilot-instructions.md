# Thor Metal Art — Project Guidelines

## Project Overview

Thor Metal Art is a Docker-based production stack for a custom metal fabrication and sculpture business (Miami-Dade, FL). Client: Karel Frometa / Thor Metal Art LLC.

**Stack:** WordPress 6.9 + PHP 8.1 + MySQL 8.0 + Redis 7 + phpMyAdmin + Traefik reverse proxy.

## Architecture

```
┌─────────────┐     ┌──────────────────────────────────────────┐
│   Traefik   │────▶│  tma-dev-network (internal)              │
│  (external) │     │  ┌──────────┐ ┌───────┐ ┌─────────────┐ │
└──────┬──────┘     │  │ WordPress│─│ MySQL │─│   Redis     │ │
       │            │  │ :80      │ │ :3306 │ │   :6379     │ │
       │            │  └──────────┘ └───────┘ └─────────────┘ │
       │            └──────────────────────────────────────────┘
       │
       ├──▶ dev.thormetalart.com           → WordPress
       ├──▶ panel-dev.thormetalart.com     → Admin Panel (WordPress plugin)
       └──▶ pma-dev.thormetalart.com       → phpMyAdmin
```

**Key decisions:**
- MySQL exposed only on `127.0.0.1:3311` (local access)
- Redis: 64MB limit, LRU eviction policy for WP Object Cache
- WordPress custom Dockerfile with PECL Redis extension
- All services have health checks with retries and `depends_on: service_healthy`

## Code Style & Conventions

- **Language:** Bilingual EN/ES — all user-facing content must support both languages
- **PHP:** WordPress coding standards (WPCS) — see [.github/instructions/wordpress.instructions.md](.github/instructions/wordpress.instructions.md)
- **Shell scripts:** `set -e`, quote variables, validate `.env` — see [.github/instructions/scripts.instructions.md](.github/instructions/scripts.instructions.md)
- **HTML/CSS/JS:** Vanilla JS, no frameworks; Chart.js 4.x — see [.github/instructions/dashboard.instructions.md](.github/instructions/dashboard.instructions.md)
- **Docker:** Health checks, resource limits — see [.github/instructions/docker.instructions.md](.github/instructions/docker.instructions.md)
- **Security:** OWASP Top 10 compliance — see [.github/instructions/security.instructions.md](.github/instructions/security.instructions.md)

## Branding

See [docs/README.md](docs/README.md) for full branding guide. Key values: Primary `#1A1A1A`, Accent `#B8860B`, Fonts: Cormorant Garamond / DM Sans.

## Build and Test

All operations use `make` targets. Key commands:

```bash
make up / make down / make restart   # Stack lifecycle
make build                           # Rebuild without cache
make backup                          # Database backup (10-file rotation)
make test-all                        # Run ALL test suites
make lint                            # Run all linters
make format                          # Auto-fix formatting
```

Run `make help` or see the [Makefile](Makefile) for all 28+ targets including per-service logs, shell access, and scoped test suites (`make test-panel`, `make test-dash`, `make test-lead`, etc.).

## Development Workflow

- **Tickets:** [BACKLOG.md](BACKLOG.md) is the single source of truth. Format: `TICKET-{SCOPE}-{NUM}`
- **Branching:** `main` ← `dev` ← `feat/TICKET-XXX-short-desc` (also `fix/`, `hotfix/`)
- **Commits:** `{type}(TICKET-XXX): description` (types: feat, fix, refactor, test, docs, chore)
- **TDD mandatory:** RED → GREEN → REFACTOR for all features
- **Tests:** Bash scripts in `tests/` with pass/fail counters. Naming: `test-{scope}-{num}-{description}.sh`

See [.github/instructions/workflows.instructions.md](.github/instructions/workflows.instructions.md) for full branching strategy, PR requirements, and quality gates.

## Environment

- Secrets in `.env` (never commit — in `.gitignore`). See [.github/instructions/env-validation.instructions.md](.github/instructions/env-validation.instructions.md)
- Database: `thormetalart_wp`, user: `thormetalart`, prefix: `tma_`
- Backups: `/backups/` with 10-file rotation

## File Structure

| Path | Purpose |
|------|---------|
| `docker-compose.yml` | Service orchestration (4 services) |
| `docker/wordpress/Dockerfile` | Custom WP image with Redis PECL |
| `Makefile` | 28+ operational targets (stack, test, lint) |
| `.env` / `.env.example` | Secrets (gitignored) / variable template |
| `scripts/` | Operational scripts (backup, restore, test, cache) |
| `tests/` | Bash test scripts (TDD, integration) |
| `data/wordpress/` | WordPress files (volume mount) |
| `data/mysql/` | MySQL data (volume mount) |
| `docs/` | Project docs and branding — see [docs/README.md](docs/README.md) |
| `.github/` | AI customization ecosystem — see [.github/README.md](.github/README.md) |
| `BACKLOG.md` | All tickets with status, priorities, and dependencies |
| `_archive/` | Archived prototypes (dashboard v1, portal v1) |

## CI Pipeline

GitHub Actions ([.github/workflows/ci.yml](.github/workflows/ci.yml)) runs on push to `main`, `dev`, `feat/**`, `fix/**`:
- **lint-php** — PHP syntax + PHPCS/WPCS (strict on `main`, warnings on branches)
- **lint-js** — ESLint + Prettier format check
- **php-static-analysis** — PHPStan (strict on `main`)
- **validate-docker** — `docker compose config` syntax validation

## AI Customization Ecosystem

This project has a comprehensive `.github/` setup — **check [.github/README.md](.github/README.md) before creating new files:**

| Primitive | Count | Location |
|-----------|-------|----------|
| Instructions | 13 | `.github/instructions/` — auto-loaded by `applyTo` file patterns |
| Agents | 12 | `.github/agents/` — domain-specific with restricted tool sets |
| Skills | 7 | `.github/skills/` — reusable workflows (TDD, code-review, ship-feature, stack-mgmt, tickets, WP, API) |
| Prompts | 21 | `.github/prompts/` — quick-action slash commands |
| Hooks | 4 | `.github/hooks/` — safety-checks.json + php-lint-check.sh + format-on-save.sh + sql-guard.sh |
