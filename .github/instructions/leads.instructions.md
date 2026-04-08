---
description: "Use when editing the lead management system: CRUD operations, lead history, status tracking, REST API endpoints, high-value alerts. Covers tma_panel_leads and tma_panel_lead_history tables."
applyTo: "data/wordpress/wp-content/plugins/tma-panel/**"
---

# Lead System Guidelines — Thor Metal Art

## Database Schema

| Table | Purpose |
|-------|---------|
| `tma_panel_leads` | Main leads: id, name, email, phone, source, status, notes, lead_value, created_at, updated_at |
| `tma_panel_lead_history` | Audit trail: id, lead_id, field_changed, old_value, new_value, changed_by, changed_at |

## Lead Statuses

Valid status flow: `new` → `contacted` → `quoted` → `won` / `lost`

Never skip statuses without logging the transition in `tma_panel_lead_history`.

## Code Conventions

- Static methods on `TMA_Panel_Leads` class: `create_from_contact()`, `update_lead()`, `log_status_change()`, `get_lead_history()`
- All DB queries via `$wpdb->prepare()` — never interpolate user input
- Sanitize inputs: `sanitize_text_field()`, `sanitize_email()`, `intval()` for lead_value
- Prefix all functions and hooks with `tma_`
- High-value threshold: leads with `lead_value >= 5000` trigger alert hooks

## REST API

- Namespace: `tma-panel/v1`
- Endpoints follow WordPress REST conventions with `permission_callback` on every route
- Return `WP_REST_Response` with proper HTTP status codes

## Testing

- Test files: `tests/test-lead-*.sh`
- Pattern: bash scripts with PASS/FAIL counters
- Test CRUD operations via `docker exec` PHP evaluation
