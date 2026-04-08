#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS_FILE="$PLUGIN_DIR/assets/js/panel.js"
CONTACT_FILE="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/mu-plugins/tma-contact-form.php"

pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-LEAD-004 — Alertas leads alto valor\n"

grep -q "high_value\|requires_attention\|new_attention" "$API_FILE" && pass "Dashboard API incluye métrica de alerta" || fail "Métrica alerta faltante en API"
grep -q "status = 'new'.*lead_value > 0\|lead_value > 0.*status = 'new'" "$API_FILE" && pass "API filtra leads new con valor" || fail "Filtro de alerta faltante"

grep -q "requieren atención\|dashboard-alert\|high-value-alert" "$JS_FILE" && pass "Dashboard UI renderiza alerta" || fail "UI alerta faltante"

grep -q "Custom Gates\|Art & Commissions\|custom gates\|art & commissions" "$CONTACT_FILE" && pass "Formulario identifica servicios premium" || fail "Servicios premium faltantes"
grep -q "high value\|alto valor\|priority lead\|tma_send_high_value" "$CONTACT_FILE" && pass "Email alerta de alto valor implementado" || fail "Email alto valor faltante"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/mu-plugins/tma-contact-form.php 2>&1 | grep -q "No syntax errors" && pass "Contact form sin errores" || fail "Error sintaxis contact form"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
