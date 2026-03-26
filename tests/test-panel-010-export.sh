#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → TICKET-PANEL-010: Export system
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-010 — Export del proyecto                     ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence checks"

[ -f "$PLUGIN_DIR/includes/class-tma-panel-export.php" ] \
  && pass "class-tma-panel-export.php exists" \
  || fail "class-tma-panel-export.php missing"

# ─────────────────────────────────────────────────────────────────
# 2. CODE PATTERNS IN EXPORT CLASS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔍 Code patterns in export class"

EXPORT_FILE="$PLUGIN_DIR/includes/class-tma-panel-export.php"
if [ -f "$EXPORT_FILE" ]; then
  grep -q 'class TMA_Panel_Export' "$EXPORT_FILE" \
    && pass "TMA_Panel_Export class defined" \
    || fail "TMA_Panel_Export class not found"

  grep -q 'generate_summary\|generate' "$EXPORT_FILE" \
    && pass "Has generate method" \
    || fail "generate method missing"

  grep -q 'panel_docs\|documents' "$EXPORT_FILE" \
    && pass "Includes documents section" \
    || fail "Documents section missing"

  grep -q 'panel_leads\|leads' "$EXPORT_FILE" \
    && pass "Includes leads section" \
    || fail "Leads section missing"

  grep -q 'panel_kpis\|kpis' "$EXPORT_FILE" \
    && pass "Includes KPIs section" \
    || fail "KPIs section missing"

  grep -q 'panel_notes\|notes' "$EXPORT_FILE" \
    && pass "Includes notes section" \
    || fail "Notes section missing"
else
  for t in "TMA_Panel_Export class" "generate method" "documents" "leads" "KPIs" "notes"; do
    fail "$t (file missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 3. BOOTSTRAP INTEGRATION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔌 Bootstrap integration"

MAIN="$PLUGIN_DIR/tma-panel.php"
grep -q 'class-tma-panel-export.php' "$MAIN" \
  && pass "Main plugin requires export class" \
  || fail "Main plugin doesn't require export class"

# ─────────────────────────────────────────────────────────────────
# 4. REST API ENDPOINT FOR EXPORT
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 REST endpoint"

API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
grep -q 'export' "$API_FILE" \
  && pass "API has export endpoint reference" \
  || fail "API missing export endpoint"

# Verify route is registered
EXPORT_ROUTE=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  \$server = rest_get_server();
  \$routes = \$server->get_routes('tma-panel/v1');
  foreach (\$routes as \$path => \$r) {
    if (str_contains(\$path, 'export')) { echo 'FOUND'; exit; }
  }
  echo 'NOT_FOUND';
" 2>/dev/null || echo "ERROR")

[ "$EXPORT_ROUTE" = "FOUND" ] \
  && pass "REST route /export registered" \
  || fail "REST route /export not registered (got: $EXPORT_ROUTE)"

# ─────────────────────────────────────────────────────────────────
# 5. FRONTEND — Export button in dashboard
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🖥️  Frontend export button"

PANEL_JS="$PLUGIN_DIR/assets/js/panel.js"
grep -qi 'export\|exportar' "$PANEL_JS" \
  && pass "panel.js references export" \
  || fail "panel.js has no export reference"

grep -qi 'clipboard\|navigator.clipboard\|execCommand' "$PANEL_JS" \
  && pass "panel.js has clipboard/copy logic" \
  || fail "panel.js missing clipboard logic"

# ─────────────────────────────────────────────────────────────────
# 6. PHP SYNTAX CHECK
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax validation"

for f in "includes/class-tma-panel-export.php" "tma-panel.php" "includes/class-tma-panel-api.php"; do
  if [ -f "$PLUGIN_DIR/$f" ]; then
    docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" \
      && pass "Syntax OK: $f" \
      || fail "Syntax error: $f"
  else
    fail "Syntax check skipped (missing): $f"
  fi
done

# ─────────────────────────────────────────────────────────────────
# 7. FUNCTIONAL — Export generates content
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📦 Functional export test"

EXPORT_OUTPUT=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  if (class_exists('TMA_Panel_Export')) {
    \$result = TMA_Panel_Export::generate_summary();
    echo strlen(\$result) > 50 ? 'HAS_CONTENT' : 'TOO_SHORT';
  } else {
    echo 'NO_CLASS';
  }
" 2>/dev/null || echo "ERROR")

[ "$EXPORT_OUTPUT" = "HAS_CONTENT" ] \
  && pass "Export generates meaningful content" \
  || fail "Export output insufficient (got: $EXPORT_OUTPUT)"

# Check export includes date header
EXPORT_DATE=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  if (class_exists('TMA_Panel_Export')) {
    \$r = TMA_Panel_Export::generate_summary();
    echo str_contains(\$r, date('Y')) ? 'HAS_DATE' : 'NO_DATE';
  } else { echo 'NO_CLASS'; }
" 2>/dev/null || echo "ERROR")

[ "$EXPORT_DATE" = "HAS_DATE" ] \
  && pass "Export includes date header" \
  || fail "Export missing date header (got: $EXPORT_DATE)"

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
