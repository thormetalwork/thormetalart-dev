#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → Test suite for TICKET-PANEL-008: Audit log system
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

wp_php() {
  docker exec "$WP_CONTAINER" php -r "require '/var/www/html/wp-load.php'; $1" 2>/dev/null
}

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-008 — Audit log system                       ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence"

[ -f "$PLUGIN_DIR/includes/class-tma-panel-audit.php" ] \
  && pass "class-tma-panel-audit.php exists" \
  || fail "class-tma-panel-audit.php missing"

# ─────────────────────────────────────────────────────────────────
# 2. CLASS STRUCTURE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🏗️  Class structure"

AUDIT="$PLUGIN_DIR/includes/class-tma-panel-audit.php"

if [ -f "$AUDIT" ]; then
  grep -q 'class TMA_Panel_Audit' "$AUDIT" \
    && pass "TMA_Panel_Audit class defined" \
    || fail "Missing TMA_Panel_Audit class"

  # log method
  grep -q 'function log\|static.*log' "$AUDIT" \
    && pass "Has log() method" \
    || fail "Missing log() method"

  # get_entries / get_recent
  grep -q 'function get_entries\|function get_recent\|function get_log' "$AUDIT" \
    && pass "Has entries retrieval method" \
    || fail "Missing entries retrieval method"

  # cleanup / rotate method
  grep -q 'function cleanup\|function rotate\|function purge' "$AUDIT" \
    && pass "Has cleanup/rotate method" \
    || fail "Missing cleanup/rotate method"

  # Records IP
  grep -q 'REMOTE_ADDR\|ip_address\|ip' "$AUDIT" \
    && pass "Records IP address" \
    || fail "Doesn't record IP"

  # Records user_agent (in details JSON or dedicated column)
  grep -q 'HTTP_USER_AGENT\|user_agent' "$AUDIT" \
    && pass "Records user agent" \
    || fail "Doesn't record user agent"

  # Insert via $wpdb
  grep -q 'wpdb.*insert\|insert.*wpdb' "$AUDIT" \
    && pass "Uses wpdb->insert" \
    || fail "Doesn't use wpdb->insert"
else
  for i in $(seq 1 7); do fail "audit class missing"; done
fi

# ─────────────────────────────────────────────────────────────────
# 3. INTEGRATED IN MAIN PLUGIN
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔗 Plugin integration"

MAIN="$PLUGIN_DIR/tma-panel.php"

grep -q 'class-tma-panel-audit' "$MAIN" \
  && pass "Main plugin requires audit class" \
  || fail "Main plugin doesn't require audit class"

# ─────────────────────────────────────────────────────────────────
# 4. PHP SYNTAX
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax"

if [ -f "$AUDIT" ]; then
  docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-audit.php" 2>&1 | grep -q "No syntax errors" \
    && pass "Syntax OK: class-tma-panel-audit.php" \
    || fail "Syntax error: class-tma-panel-audit.php"
else
  fail "Syntax check skipped (file missing)"
fi

# ─────────────────────────────────────────────────────────────────
# 5. AUDIT TABLE EXISTS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🗄️  Database"

RESULT=$(docker exec "$WP_CONTAINER" php -r '
  require "/var/www/html/wp-load.php";
  global $wpdb;
  $t = $wpdb->get_var("SHOW TABLES LIKE \"{$wpdb->prefix}panel_audit\"");
  echo $t ? "YES" : "NO";
' 2>/dev/null || echo "ERROR")
[ "$RESULT" = "YES" ] \
  && pass "panel_audit table exists" \
  || fail "panel_audit table missing ($RESULT)"

# ─────────────────────────────────────────────────────────────────
# 6. CAN LOG AN ACTION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📝 Functional tests"

LOG_RESULT=$(docker exec "$WP_CONTAINER" php -r '
  require "/var/www/html/wp-load.php";
  if (class_exists("TMA_Panel_Audit")) {
    TMA_Panel_Audit::log("test_action", "test", 0, 1);
    echo "LOGGED";
  } else {
    echo "CLASS_MISSING";
  }
' 2>/dev/null || echo "ERROR")
[ "$LOG_RESULT" = "LOGGED" ] \
  && pass "Can log an audit action" \
  || fail "Cannot log audit action ($LOG_RESULT)"

# Verify the logged entry exists
ENTRY_EXISTS=$(docker exec "$WP_CONTAINER" php -r '
  require "/var/www/html/wp-load.php";
  global $wpdb;
  $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}panel_audit WHERE action=\"test_action\" ORDER BY id DESC LIMIT 1");
  echo $row ? "YES" : "NO";
' 2>/dev/null || echo "ERROR")
[ "$ENTRY_EXISTS" = "YES" ] \
  && pass "Logged entry found in DB" \
  || fail "Logged entry not found ($ENTRY_EXISTS)"

# ─────────────────────────────────────────────────────────────────
# 7. CRON SCHEDULED FOR ROTATION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "⏰ Cron rotation"

CRON_EXISTS=$(docker exec "$WP_CONTAINER" php -r '
  require "/var/www/html/wp-load.php";
  echo wp_next_scheduled("tma_panel_audit_cleanup") ? "YES" : "NO";
' 2>/dev/null || echo "ERROR")
[ "$CRON_EXISTS" = "YES" ] \
  && pass "Cleanup cron scheduled" \
  || fail "Cleanup cron not scheduled ($CRON_EXISTS)"

# ─────────────────────────────────────────────────────────────────
# 8. CLEANUP REMOVES OLD ENTRIES
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧹 Cleanup"

CLEANUP_RESULT=$(docker exec "$WP_CONTAINER" php -r '
  require "/var/www/html/wp-load.php";
  global $wpdb;
  $table = $wpdb->prefix . "panel_audit";
  $wpdb->insert($table, array("user_id" => 1, "action" => "old_test", "entity_type" => "test", "entity_id" => 0, "ip_address" => "127.0.0.1", "user_agent" => "test", "created_at" => date("Y-m-d H:i:s", strtotime("-91 days"))));
  if (class_exists("TMA_Panel_Audit")) {
    TMA_Panel_Audit::cleanup();
    $row = $wpdb->get_row("SELECT * FROM $table WHERE action=\"old_test\"");
    echo $row ? "NOT_CLEANED" : "CLEANED";
  } else {
    echo "CLASS_MISSING";
  }
' 2>/dev/null || echo "ERROR")
[ "$CLEANUP_RESULT" = "CLEANED" ] \
  && pass "Cleanup removes entries >90 days" \
  || fail "Cleanup failed ($CLEANUP_RESULT)"

# Cleanup test entries
docker exec "$WP_CONTAINER" php -r '
  require "/var/www/html/wp-load.php";
  global $wpdb;
  $wpdb->query("DELETE FROM {$wpdb->prefix}panel_audit WHERE action IN (\"test_action\",\"old_test\")");
' >/dev/null 2>&1 || true

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
