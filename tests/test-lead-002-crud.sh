#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
CONTACT_FILE="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/mu-plugins/tma-contact-form.php"
API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS_FILE="$PLUGIN_DIR/assets/js/panel.js"
LEADS_CLASS="$PLUGIN_DIR/includes/class-tma-panel-leads.php"
pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-LEAD-002 — Leads CRUD\n"

[ -f "$LEADS_CLASS" ] && pass "class-tma-panel-leads.php existe" || fail "class-tma-panel-leads.php faltante"
grep -q "class TMA_Panel_Leads" "$LEADS_CLASS" && pass "Clase leads definida" || fail "Clase leads faltante"
grep -q "migrate_from_tma_leads" "$LEADS_CLASS" && pass "Migración desde tma_leads" || fail "Método de migración faltante"
grep -q "pipeline\|value" "$LEADS_CLASS" && pass "Cálculo pipeline value" || fail "Pipeline value faltante"

grep -q "update_lead\|/leads/(?P<id>.*)" "$API_FILE" && pass "API actualización de lead" || fail "API update lead faltante"

grep -q "pipeline value\|pipeline\|leads by channel\|tma-chart-lead-sources" "$JS_FILE" && pass "UI pipeline + gráfico canal" || fail "UI pipeline/canal faltante"

grep -q "do_action\|tma_panel_create_lead\|panel_leads" "$CONTACT_FILE" && pass "Formulario crea lead en panel_leads" || fail "Hook formulario→panel faltante"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-leads.php 2>&1 | grep -q "No syntax errors" && pass "Leads class sin errores" || fail "Error sintaxis leads class"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
