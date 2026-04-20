---
description: "Run pending database migrations for the tma-panel plugin, verify schema, and report status."
agent: "agent"
---
# Database Migration Runner

Execute pending database migrations for the TMA Panel plugin.

## Steps

1. **Check current DB version** — Run in the WordPress container:
   ```bash
   docker exec tma_dev_wordpress wp option get tma_panel_db_version --allow-root 2>/dev/null || echo "0"
   ```

2. **Check target version** — Read `DB_VERSION` constant from:
   [class-tma-panel-data.php](../../data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-data.php)

3. **List pending migrations** — Compare current vs target. Migration files:
   - `migrations/001-initial.php` → tables: panel_leads, panel_notes, panel_kpis, panel_audit, panel_docs
   - `migrations/002-lead-history.php` → table: panel_lead_history
   - `migrations/003-add-missing-columns.php` → columns: approved_by, approved_at, change_notes, module, item_id, lead_value

4. **Create backup before migrating**:
   ```bash
   make backup
   ```

5. **Trigger migration** — Visit any wp-admin page (auto-triggers via `admin_init`) or force:
   ```bash
   docker exec tma_dev_wordpress wp eval "TMA_Panel_Data::maybe_migrate();" --allow-root
   ```

6. **Verify schema** — Check all tables exist:
   ```bash
   docker exec tma_dev_mysql mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" \
     -e "SHOW TABLES LIKE 'tma_panel_%';"
   ```

7. **Report** — Display: current version → new version, tables created/modified, any errors.

## Migration Pattern Reference

New migrations go in `migrations/NNN-description.php`:
- Receive `$wpdb`, `$prefix`, `$charset_collate` as globals
- Use `dbDelta()` for CREATE TABLE (idempotent)
- Use `SHOW COLUMNS` + `ALTER TABLE` for column additions (idempotent)
- Update `DB_VERSION` constant in `class-tma-panel-data.php` after adding
