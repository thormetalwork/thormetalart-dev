---
description: "Use when editing the tma-panel WordPress plugin: architecture, REST API, migrations, roles, cron, audit, documents, exports. Covers the full plugin system beyond just leads."
applyTo: "data/wordpress/wp-content/plugins/tma-panel/**"
---

# TMA Panel Plugin — Architecture Guidelines

The `tma-panel` plugin (v0.4.1) is a client-facing admin panel with its own domain (`panel-dev.thormetalart.com`).

## Plugin Structure

| Class | Purpose |
|-------|---------|
| `TMA_Panel_Router` | Domain/route interception, security headers (CSP, X-Frame-Options) |
| `TMA_Panel_Roles` | 2 roles (`tma_admin`, `tma_client`), 8 capabilities |
| `TMA_Panel_Data` | Migration runner — `DB_VERSION = 3`, files in `migrations/NNN-desc.php` |
| `TMA_Panel_API` | REST API — namespace `tma-panel/v1`, 12 endpoints |
| `TMA_Panel_Audit` | Audit logging with 90-day retention cron |
| `TMA_Panel_Cron` | Daily sync: GA4, GSC, GBP, Instagram |
| `TMA_Panel_Docs` | Document pipeline — 12 cached HTML files |
| `TMA_Panel_Leads` | Lead CRUD — see [leads.instructions.md](leads.instructions.md) |
| `TMA_Panel_Export` | Text summary export generation |
| `TMA_Panel_Google_Auth` | OAuth2 token management (transient-based) |

## Database (6 tables)

All prefixed `{wp_prefix}panel_*`. Schema managed via numbered migrations in `migrations/`.

| Table | Key Columns | Notes |
|-------|-------------|-------|
| `panel_leads` | name, email, phone, source, status, lead_value | Status flow: new → contacted → quoted → won/lost |
| `panel_lead_history` | lead_id, action, old_status, new_status | Audit trail for lead changes |
| `panel_notes` | user_id, title, content, visibility, module, item_id | Bidirectional notes (internal/client) |
| `panel_kpis` | metric, value, period, category | Populated by cron sync |
| `panel_audit` | user_id, action, entity_type, entity_id, ip_address | 90-day auto-cleanup |
| `panel_docs` | title, slug, doc_order, status, approved_by | Document approval workflow |

## Migrations

- Files: `migrations/NNN-description.php` (receive `$wpdb`, `$prefix`, `$charset_collate`)
- Runner: `TMA_Panel_Data::maybe_migrate()` — compares `tma_panel_db_version` option vs `DB_VERSION`
- Increment `DB_VERSION` constant in `class-tma-panel-data.php` when adding migrations
- Always use `dbDelta()` for CREATE TABLE, raw SQL for ALTER TABLE

## REST API Conventions

- Namespace: `tma-panel/v1`
- Every route MUST have `permission_callback` — never use `__return_true`
- Return `WP_REST_Response` with proper HTTP status codes
- Sanitize all input: `sanitize_text_field()`, `sanitize_email()`, `intval()`
- Validate with `WP_REST_Request::get_param()` — never access `$_GET`/`$_POST` directly

| Route | Method | Capability |
|-------|--------|------------|
| `/dashboard` | GET | `tma_view_panel` |
| `/documents` | GET | `tma_view_panel` |
| `/documents/{code}/content` | GET | `tma_view_panel` |
| `/documents/{id}/status` | POST | `tma_view_panel` |
| `/leads` | GET | `tma_view_panel` |
| `/leads/{id}` | POST | `tma_view_panel` |
| `/leads/{id}/history` | GET | `tma_view_panel` |
| `/notes` | GET | `tma_view_panel` |
| `/notes` | POST | `tma_manage_notes` |
| `/audit` | GET | `tma_view_audit` |
| `/export` | GET | `tma_export` |

## Roles & Capabilities

- `tma_admin`: All 8 capabilities (full access including audit, kpi management, visibility toggle)
- `tma_client`: 4 capabilities (`tma_view_panel`, `tma_manage_docs`, `tma_manage_leads`, `tma_manage_notes`, `tma_export`)
- Admin bar hidden for both roles; wp-admin blocked with redirect to panel

## Security

- Session timeout: 12 hours (`auth_cookie_expiration` filter)
- Security headers via `TMA_Panel_Router::send_security_headers()`: CSP, X-Frame-Options DENY, nosniff, strict-origin referrer
- CORS restricted to panel domain + home_url
- All DB queries via `$wpdb->prepare()`

## Testing

- Test files: `tests/test-panel-*.sh`, `tests/test-lead-*.sh`
- Run: `make test-panel`, `make test-lead`
- Test API endpoints via `docker exec` with WP-CLI or direct PHP evaluation
