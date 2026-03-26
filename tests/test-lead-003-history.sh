#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"
DATA_FILE="$PLUGIN_DIR/includes/class-tma-panel-data.php"
API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
LEADS_FILE="$PLUGIN_DIR/includes/class-tma-panel-leads.php"
JS_FILE="$PLUGIN_DIR/assets/js/panel.js"
MIGRATION_FILE="$PLUGIN_DIR/migrations/002-lead-history.php"

pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-LEAD-003 — Historial por lead\n"

[ -f "$MIGRATION_FILE" ] && pass "Migración 002 existe" || fail "Falta migración 002-lead-history.php"
grep -q "panel_lead_history" "$MIGRATION_FILE" && pass "Migración crea tabla panel_lead_history" || fail "Migración historial incompleta"

grep -q "DB_VERSION = 2" "$DATA_FILE" && pass "DB version incrementada" || fail "DB version no incrementada"

grep -q "log_status_change\|lead_history" "$LEADS_FILE" && pass "Servicio leads registra historial" || fail "Registro de historial faltante"

grep -q "/leads/(?P<id>.*)/history\|get_lead_history" "$API_FILE" && pass "API historial de lead disponible" || fail "Endpoint historial faltante"

grep -q "lead-history\|timeline\|Ver historial\|view-history" "$JS_FILE" && pass "UI muestra timeline de historial" || fail "Timeline UI faltante"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "API class sin errores" || fail "Error sintaxis API class"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
