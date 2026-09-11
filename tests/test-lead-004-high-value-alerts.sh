#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="tma_dev_wordpress"
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

premium=$(docker exec "$WP_CONTAINER" php -r 'require "/var/www/html/wp-load.php"; echo json_encode(array_map("tma_is_high_value_service", ["custom-gates", "metal-art", "railings", "unknown"]));')
[ "$premium" = '[true,true,false,false]' ] && pass "Solo los valores canónicos premium activan alerta" || fail "Clasificación premium incorrecta: $premium"

grep -q "tma_is_high_value_service" "$CONTACT_FILE" && grep -q "tma_send_high_value" "$CONTACT_FILE" && pass "Alerta usa clasificación canónica" || fail "Alerta no usa clasificación canónica"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/mu-plugins/tma-contact-form.php 2>&1 | grep -q "No syntax errors" && pass "Contact form sin errores" || fail "Error sintaxis contact form"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
