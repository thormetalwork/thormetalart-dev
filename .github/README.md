# AI Customization Ecosystem

Central index for all AI agent customization primitives in this project.

> **Before creating new files**, check this index to avoid duplicates.

## Quick Reference

| Need to… | Use |
|----------|-----|
| Ship a feature end-to-end | `/ship TICKET-XXX` prompt or `Full Cycle Developer` agent |
| Create a ticket | `/ticket` prompt or `Ticket Manager` agent |
| Review code quality | `/code-review` prompt or `code-review` skill |
| Run TDD cycle | `TDD Developer` agent or `tdd-workflow` skill |
| Manage Docker stack | `/deploy`, `/test-stack`, `/logs` prompts or `DevOps` agent |
| Backup/restore database | `/backup-database`, `/restore-database` prompts |
| Check cache health | `/cache-status`, `/clear-cache` prompts |
| Generate client report | `/client-report` prompt or `Client Reporter` agent |
| Write website content | `Content Writer` agent |
| Audit security | `/security-audit` prompt or `Security Auditor` agent |
| Audit SEO | `/seo-audit` prompt or `SEO Specialist` agent |
| Analyze performance | `/performance-check` prompt or `Performance Analyst` agent |
| Fix lint issues | `/lint-fix` prompt |
| Debug WordPress errors | `/wp-debug` prompt |
| Manage database/migrations | `Database Admin` agent or `/db-migrate` prompt |
| Build REST API endpoints | `api-development` skill |
| Create WordPress page | `/new-page` prompt or `WordPress Dev` agent |

## Primitives

### Instructions (13) — `.github/instructions/`

Auto-loaded by file pattern matching (`applyTo`). No manual action needed.

| File | Applies To | Domain |
|------|-----------|--------|
| `dashboard.instructions.md` | `docs/cliente/**/*.html`, `_archive/dashboard/**` | Chart.js, KPIs, vanilla JS |
| `docker.instructions.md` | `docker-compose.yml`, `docker/**`, `**/Dockerfile` | Health checks, networking, limits |
| `documentation.instructions.md` | `docs/**`, `*.md` | Bilingual docs, client tone |
| `env-validation.instructions.md` | `**/.env*`, `**/docker-compose.yml` | Required vars, secrets |
| `google-apis.instructions.md` | _(manual)_ | GA4, GBP, OAuth, schema markup |
| `leads.instructions.md` | `data/wordpress/wp-content/plugins/tma-panel/**` | Lead CRUD, audit history, alerts |
| `redis.instructions.md` | `docker-compose.yml`, `scripts/clear-cache.sh` | 64MB LRU, password auth |
| `scripts.instructions.md` | `scripts/**` | Bash best practices, `.env` validation |
| `security.instructions.md` | `**/*.php`, `**/*.js`, `**/*.sh`, Docker | OWASP Top 10, input validation |
| `testing.instructions.md` | `tests/**`, `**/test-*` | AAA pattern, TDD enforcement |
| `tma-panel.instructions.md` | `data/wordpress/wp-content/plugins/tma-panel/**` | Plugin architecture, REST API, migrations, roles |
| `wordpress.instructions.md` | `data/wordpress/**/*.php`, `wp-content/**` | WPCS, `tma_` prefix, nonces |
| `workflows.instructions.md` | `BACKLOG.md`, `.github/**` | Branching, PRs, quality gates |

### Agents (12) — `.github/agents/`

Domain-specific agents with restricted tool access.

| Agent | Tools | Purpose |
|-------|-------|---------|
| `Client Reporter` | read, search, web | Monthly reports and KPI analysis for Karel (Spanish) |
| `Content Writer` | read, search, web | Bilingual website copy, meta descriptions, CTAs |
| `Dashboard Dev` | read, edit, search, execute | Executive dashboard: Chart.js, APIs, responsive UI |
| `Database Admin` | execute, read, search | MySQL schema, migrations, query optimization, backup/restore |
| `DevOps` | execute, read, edit, search | Docker stack, Traefik, MySQL, Redis operations |
| `Full Cycle Developer` | read, edit, search, execute, todo | Complete ticket lifecycle: branch → TDD → review → deploy |
| `Performance Analyst` | read, search, execute | Redis/MySQL metrics, Core Web Vitals, resource analysis |
| `Security Auditor` | read, search, execute | OWASP compliance, infrastructure hardening audit |
| `SEO Specialist` | read, edit, search, web | Local SEO, GBP, schema markup, keyword research |
| `TDD Developer` | read, edit, search, execute | RED → GREEN → REFACTOR cycle enforcement |
| `Ticket Manager` | read, edit, search | BACKLOG.md ticket lifecycle and prioritization |
| `WordPress Dev` | read, edit, search, execute | Themes, plugins, CPTs, WooCommerce, bilingual content |

### Skills (7) — `.github/skills/`

Reusable multi-step workflows loaded via `SKILL.md`.

| Skill | Key Files | Purpose |
|-------|-----------|---------|
| `api-development/` | `SKILL.md`, `references/api-patterns.md` | REST endpoint development with TDD for tma-panel |
| `code-review/` | `SKILL.md` | 7-category structured code review checklist |
| `ship-feature/` | `SKILL.md` | 9-phase feature delivery: prep → TDD → review → deploy |
| `stack-management/` | `SKILL.md`, `references/service-endpoints.md` | Docker lifecycle, backup/restore, troubleshooting |
| `tdd-workflow/` | `SKILL.md`, `references/testing-patterns.md` | RED/GREEN/REFACTOR with PHP/JS/Bash examples |
| `ticket-management/` | `SKILL.md`, `references/ticket-templates.md` | Ticket CRUD in BACKLOG.md with templates |
| `wordpress-dev/` | `SKILL.md`, `references/wpcs-patterns.md` | Theme/plugin development, bilingual, schema |

### Prompts (21) — `.github/prompts/`

Slash-command quick actions.

| Prompt | Agent | Description |
|--------|-------|-------------|
| `/backup-database` | DevOps | Database backup with 10-file rotation |
| `/restore-database` | DevOps | Restore from backup with safety checks |
| `/clear-cache` | DevOps | Flush Redis + verify repopulation |
| `/cache-status` | Performance Analyst | Redis health: hit rate, memory, keys |
| `/deploy` | DevOps | Safe deploy: backup → build → test → verify |
| `/test-stack` | DevOps | Health check all services |
| `/logs` | DevOps | View/filter container logs by service |
| `/lint-fix` | DevOps | Run linters + auto-fix formatting |
| `/performance-check` | DevOps | TTFB, Redis, MySQL, container resources |
| `/ship` | Full Cycle Developer | Implement complete ticket end-to-end |
| `/code-review` | Full Cycle Developer | Structured review with severity table |
| `/tests` | TDD Developer | Generate failing tests (RED phase) |
| `/ticket` | Ticket Manager | Create new ticket in BACKLOG.md |
| `/backlog-status` | Ticket Manager | View backlog state and recommendations |
| `/new-page` | WordPress Dev | Create WordPress page with bilingual content |
| `/client-report` | Client Reporter | Monthly report in Spanish for Karel |
| `/security-audit` | Security Auditor | Infrastructure + WordPress security audit |
| `/seo-audit` | SEO Specialist | On-page, technical, and local SEO analysis |
| `/db-migrate` | _(default)_ | Run pending tma-panel database migrations |
| `/migrate-database` | DevOps | Migrate DB between environments |
| `/wp-debug` | DevOps | Toggle WP_DEBUG and analyze error log |

### Hooks (4) — `.github/hooks/`

Automated safety gates and formatting.

| Hook | Type | Trigger | Action |
|------|------|---------|--------|
| `safety-checks.json` | Config | PreToolUse, SessionStart, PostToolUse | Destructive command guard, TDD reminder, security alerts |
| `php-lint-check.sh` | PostToolUse | After PHP file edit | PHP syntax validation |
| `format-on-save.sh` | PostToolUse | After JS/CSS/JSON/PHP edit | Prettier or PHPCBF auto-format |
| `sql-guard.sh` | PreToolUse | Before destructive SQL | Block DROP/TRUNCATE without confirmation |

## Conventions

- **Naming:** kebab-case for all files (`my-agent.agent.md`, `my-prompt.prompt.md`)
- **Language:** Instructions/skills in English; client-facing prompts in Spanish when needed
- **Prefix:** All WordPress functions use `tma_` prefix
- **TDD:** Every feature requires tests _before_ implementation
- **Links:** Reference existing docs with markdown links — don't duplicate content
