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
- All services have health checks with retries

## Code Style & Conventions

- **Language:** Bilingual EN/ES — all user-facing content must support both languages
- **PHP:** WordPress coding standards (WPCS)
- **Shell scripts:** Use `set -e`, quote variables, validate `.env` before operations
- **HTML/CSS/JS:** Vanilla JS, no frameworks; Chart.js 4.x for visualizations
- **Docker:** Use health checks, depend on `service_healthy`, limit resources

## Branding

See `docs/README.md` for full branding guide (colors, fonts, tone, site sections). Key values: Primary `#1A1A1A`, Accent `#B8860B`, Fonts: Cormorant Garamond / DM Sans.

## Build and Test

```bash
# Stack management
make up          # Start stack
make down        # Stop stack
make restart     # Stop + start
make build       # Rebuild without cache
make status      # Show container status
make clean       # Down + remove volumes

# Logs
make logs        # Tail all logs
make logs-wp     # Tail WordPress only
make logs-mysql  # Tail MySQL only

# Database & cache
make backup      # Backup database (10-file rotation)
make shell-wp    # WordPress container shell
make shell-mysql # MySQL container shell

# Testing
make test        # Test all connections
make test-all    # Run ALL test suites
make test-panel  # Panel tests only
make test-dash   # Dashboard tests only
make test-lead   # Lead tests only
make test-portal # Portal tests only
make test-docker # Docker tests only

# Code quality
make lint        # Run all linters
make lint-php    # PHP syntax check
make lint-js     # ESLint
make lint-format # Prettier check
make lint-phpcs  # WordPress coding standards
make lint-phpstan # Static analysis
make format      # Auto-fix formatting
make fix         # Auto-fix lint issues
```

## Development Workflow

- **Tickets:** `BACKLOG.md` is the single source of truth. Format: `TICKET-{SCOPE}-{NUM}` (14 scopes: WP, DOCK, DASH, SEO, LEAD, PANEL, etc.)
- **Branching:** `main` ← `dev` ← `feat/TICKET-XXX-short-desc` (also `fix/`, `hotfix/`)
- **Commits:** `{type}(TICKET-XXX): description` (types: feat, fix, refactor, test, docs, chore)
- **TDD mandatory:** RED → GREEN → REFACTOR for all features
- **Tests:** Bash scripts in `tests/` using pass/fail counters pattern. Naming: `test-{scope}-{num}-{description}.sh`
- **Acceptance criteria:** Gherkin format (Given/When/Then) in every ticket

## Environment

- Secrets in `.env` (never commit — in `.gitignore`)
- Database: `thormetalart_wp`, user: `thormetalart`, prefix: `tma_`
- Backups: `/backups/` with 10-file rotation

## File Structure

| Path | Purpose |
|------|---------|
| `docker-compose.yml` | Service orchestration (4 services) |
| `docker/wordpress/Dockerfile` | Custom WP image with Redis PECL |
| `Makefile` | 28 operational targets (stack, test, lint) |
| `.env` / `.env.example` | Secrets (gitignored) / variable template |
| `scripts/` | Operational scripts (backup, restore, test, cache) |
| `tests/` | Bash test scripts (TDD, integration) |
| `data/wordpress/` | WordPress files (volume mount) |
| `data/mysql/` | MySQL data (volume mount) |
| `docs/` | Project docs and client deliverables — see `docs/README.md` for branding |
| `.github/` | AI ecosystem (instructions, agents, skills, prompts, hooks, CI) |
| `package.json` / `composer.json` | JS + PHP dependencies and QA tools |
| `_archive/` | Archived prototypes (dashboard v1, portal v1) |
| `BACKLOG.md` | All tickets with status, priorities, and dependencies |

## Security

- Never expose database credentials in code or logs
- MySQL only on localhost, external access via phpMyAdmin + Traefik
- WordPress table prefix `tma_` (non-default)
- All `.env`, `data/`, `backups/`, `*.sql.gz` in `.gitignore`

## AI Customization Ecosystem

This project has a comprehensive `.github/` setup — see files before creating new ones:

| Primitive | Count | Location |
|-----------|-------|----------|
| Instructions | 11 | `.github/instructions/` — auto-loaded by `applyTo` file patterns |
| Agents | 11 | `.github/agents/` — domain-specific with restricted tool sets |
| Skills | 6 | `.github/skills/` — reusable workflows (TDD, code-review, ship-feature, stack-mgmt, tickets, WP) |
| Prompts | 15 | `.github/prompts/` — quick-action slash commands |
| Hooks | 3 | `.github/hooks/` — safety-checks.json + php-lint-check.sh + format-on-save.sh |
