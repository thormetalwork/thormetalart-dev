#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → Test suite for TICKET-PANEL-003: Custom tables + migrations
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart-wordpress-1"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"
DB_PREFIX="tma_"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-003 — Custom tables + migration system        ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence checks"

[ -f "$PLUGIN_DIR/includes/class-tma-panel-data.php" ] \
  && pass "class-tma-panel-data.php exists" \
  || fail "class-tma-panel-data.php missing"

[ -f "$PLUGIN_DIR/migrations/001-initial.php" ] \
  && pass "migrations/001-initial.php exists" \
  || fail "migrations/001-initial.php missing"

# ─────────────────────────────────────────────────────────────────
# 2. CODE PATTERNS IN DATA CLASS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔍 Code patterns in class-tma-panel-data.php"

DATA_FILE="$PLUGIN_DIR/includes/class-tma-panel-data.php"
if [ -f "$DATA_FILE" ]; then
  grep -q 'class TMA_Panel_Data' "$DATA_FILE" \
    && pass "TMA_Panel_Data class defined" \
    || fail "TMA_Panel_Data class not found"

  grep -q 'dbDelta' "$DATA_FILE" \
    && pass "Uses dbDelta for table creation" \
    || fail "dbDelta not used"

  grep -q 'tma_panel_db_version' "$DATA_FILE" \
    && pass "References tma_panel_db_version option" \
    || fail "tma_panel_db_version option not referenced"

  grep -q 'run_migrations' "$DATA_FILE" \
    && pass "Has run_migrations method" \
    || fail "run_migrations method missing"

  grep -q 'DB_VERSION' "$DATA_FILE" \
    && pass "Defines DB_VERSION constant" \
    || fail "DB_VERSION constant missing"

  grep -q 'charset_collate' "$DATA_FILE" \
    && pass "Uses charset_collate for table creation" \
    || fail "charset_collate not used"
else
  for t in "TMA_Panel_Data class" "dbDelta" "tma_panel_db_version" "run_migrations" "DB_VERSION" "charset_collate"; do
    fail "$t (file missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 3. CODE PATTERNS IN MIGRATION FILE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔍 Code patterns in 001-initial.php"

MIGRATION_FILE="$PLUGIN_DIR/migrations/001-initial.php"
if [ -f "$MIGRATION_FILE" ]; then
  grep -q 'tma_panel_leads' "$MIGRATION_FILE" \
    && pass "Creates tma_panel_leads table" \
    || fail "tma_panel_leads table not in migration"

  grep -q 'tma_panel_notes' "$MIGRATION_FILE" \
    && pass "Creates tma_panel_notes table" \
    || fail "tma_panel_notes table not in migration"

  grep -q 'tma_panel_kpis' "$MIGRATION_FILE" \
    && pass "Creates tma_panel_kpis table" \
    || fail "tma_panel_kpis table not in migration"

  grep -q 'tma_panel_audit' "$MIGRATION_FILE" \
    && pass "Creates tma_panel_audit table" \
    || fail "tma_panel_audit table not in migration"

  grep -q 'tma_panel_docs' "$MIGRATION_FILE" \
    && pass "Creates tma_panel_docs table" \
    || fail "tma_panel_docs table not in migration"

  grep -q 'dbDelta' "$MIGRATION_FILE" \
    && pass "Migration uses dbDelta" \
    || fail "Migration should use dbDelta"

  # Seed data checks
  grep -q 'metodologia_maestra' "$MIGRATION_FILE" \
    && pass "Seeds doc: metodologia_maestra" \
    || fail "Missing seed: metodologia_maestra"

  grep -q 'diagnostico_auditoria' "$MIGRATION_FILE" \
    && pass "Seeds doc: diagnostico_auditoria" \
    || fail "Missing seed: diagnostico_auditoria"

  grep -qE '(KPI|kpi|revenue|ingresos)' "$MIGRATION_FILE" \
    && pass "Seeds KPI demo data" \
    || fail "Missing KPI seed data"
else
  for t in "leads table" "notes table" "kpis table" "audit table" "docs table" "dbDelta" "seed metodologia" "seed diagnostico" "KPI seed"; do
    fail "$t (migration file missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 4. BOOTSTRAP LOADS DATA CLASS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔌 Bootstrap integration"

MAIN_FILE="$PLUGIN_DIR/tma-panel.php"
grep -q 'class-tma-panel-data.php' "$MAIN_FILE" \
  && pass "Main plugin requires data class" \
  || fail "Main plugin doesn't require data class"

grep -q 'TMA_Panel_Data' "$MAIN_FILE" \
  && pass "Main plugin references TMA_Panel_Data" \
  || fail "Main plugin doesn't reference TMA_Panel_Data"

# ─────────────────────────────────────────────────────────────────
# 5. PHP SYNTAX CHECK
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax validation"

for f in "includes/class-tma-panel-data.php" "migrations/001-initial.php" "tma-panel.php"; do
  if [ -f "$PLUGIN_DIR/$f" ]; then
    docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" \
      && pass "Syntax OK: $f" \
      || fail "Syntax error: $f"
  else
    fail "Syntax check skipped (missing): $f"
  fi
done

# ─────────────────────────────────────────────────────────────────
# 6. DATABASE TABLES EXIST
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🗄️  Database table checks"

TABLES=("tma_panel_leads" "tma_panel_notes" "tma_panel_kpis" "tma_panel_audit" "tma_panel_docs")
for tbl in "${TABLES[@]}"; do
  EXISTS=$(docker exec "$WP_CONTAINER" wp db query "SHOW TABLES LIKE '${tbl}';" --allow-root 2>/dev/null | grep -c "$tbl" || true)
  [ "$EXISTS" -ge 1 ] \
    && pass "Table ${tbl} exists in DB" \
    || fail "Table ${tbl} not found in DB"
done

# ─────────────────────────────────────────────────────────────────
# 7. TABLE STRUCTURE (KEY COLUMNS)
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📊 Table structure checks"

# Leads table columns
LEADS_COLS=$(docker exec "$WP_CONTAINER" wp db query "DESCRIBE tma_panel_leads;" --allow-root 2>/dev/null || echo "")
if [ -n "$LEADS_COLS" ]; then
  echo "$LEADS_COLS" | grep -q 'name' \
    && pass "leads table has 'name' column" \
    || fail "leads table missing 'name' column"
  echo "$LEADS_COLS" | grep -q 'email' \
    && pass "leads table has 'email' column" \
    || fail "leads table missing 'email' column"
  echo "$LEADS_COLS" | grep -q 'status' \
    && pass "leads table has 'status' column" \
    || fail "leads table missing 'status' column"
else
  fail "leads table structure (table doesn't exist)"
  fail "leads name column (table doesn't exist)"
  fail "leads status column (table doesn't exist)"
fi

# Docs table columns
DOCS_COLS=$(docker exec "$WP_CONTAINER" wp db query "DESCRIBE tma_panel_docs;" --allow-root 2>/dev/null || echo "")
if [ -n "$DOCS_COLS" ]; then
  echo "$DOCS_COLS" | grep -q 'title' \
    && pass "docs table has 'title' column" \
    || fail "docs table missing 'title' column"
  echo "$DOCS_COLS" | grep -q 'slug' \
    && pass "docs table has 'slug' column" \
    || fail "docs table missing 'slug' column"
  echo "$DOCS_COLS" | grep -q 'status' \
    && pass "docs table has 'status' column" \
    || fail "docs table missing 'status' column"
else
  fail "docs title column (table doesn't exist)"
  fail "docs slug column (table doesn't exist)"
  fail "docs status column (table doesn't exist)"
fi

# KPIs table columns
KPI_COLS=$(docker exec "$WP_CONTAINER" wp db query "DESCRIBE tma_panel_kpis;" --allow-root 2>/dev/null || echo "")
if [ -n "$KPI_COLS" ]; then
  echo "$KPI_COLS" | grep -q 'metric' \
    && pass "kpis table has 'metric' column" \
    || fail "kpis table missing 'metric' column"
  echo "$KPI_COLS" | grep -q 'value' \
    && pass "kpis table has 'value' column" \
    || fail "kpis table missing 'value' column"
  echo "$KPI_COLS" | grep -q 'period' \
    && pass "kpis table has 'period' column" \
    || fail "kpis table missing 'period' column"
else
  fail "kpis metric column (table doesn't exist)"
  fail "kpis value column (table doesn't exist)"
  fail "kpis period column (table doesn't exist)"
fi

# ─────────────────────────────────────────────────────────────────
# 8. DB VERSION OPTION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔢 Migration version check"

DB_VER=$(docker exec "$WP_CONTAINER" wp option get tma_panel_db_version --allow-root 2>/dev/null || echo "")
[ -n "$DB_VER" ] \
  && pass "tma_panel_db_version option exists (value: $DB_VER)" \
  || fail "tma_panel_db_version option not set"

[ "$DB_VER" = "1" ] \
  && pass "DB version is 1 (migration 001 applied)" \
  || fail "DB version should be 1, got: '$DB_VER'"

# ─────────────────────────────────────────────────────────────────
# 9. SEED DATA VERIFICATION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌱 Seed data verification"

DOC_COUNT=$(docker exec "$WP_CONTAINER" wp db query "SELECT COUNT(*) FROM tma_panel_docs;" --allow-root 2>/dev/null | tail -1 || echo "0")
[ "$DOC_COUNT" -ge 12 ] 2>/dev/null \
  && pass "Docs table has >= 12 seed documents (got: $DOC_COUNT)" \
  || fail "Docs table should have >= 12 documents (got: $DOC_COUNT)"

KPI_COUNT=$(docker exec "$WP_CONTAINER" wp db query "SELECT COUNT(*) FROM tma_panel_kpis;" --allow-root 2>/dev/null | tail -1 || echo "0")
[ "$KPI_COUNT" -ge 6 ] 2>/dev/null \
  && pass "KPIs table has >= 6 seed records (got: $KPI_COUNT)" \
  || fail "KPIs table should have >= 6 records (got: $KPI_COUNT)"

# ─────────────────────────────────────────────────────────────────
#  RESULTS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  RESULTS: $PASS passed / $FAIL failed / $TOTAL total"
echo "════════════════════════════════════════════════════════════"
echo ""

[ "$FAIL" -eq 0 ] && echo "🎉 ALL TESTS PASSED" || echo "💔 SOME TESTS FAILED"
exit "$FAIL"
