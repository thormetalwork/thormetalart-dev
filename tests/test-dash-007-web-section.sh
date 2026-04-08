#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-DASH-007 — Web Analytics\n"
API="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS="$PLUGIN_DIR/assets/js/panel.js"

grep -q "ga4\|web" "$API" && pass "API incluye bloque web/ga4" || fail "API no incluye bloque web/ga4"
grep -q "top_pages\|pages" "$API" && pass "API incluye top pages" || fail "API sin top pages"
grep -q "sessions" "$API" && pass "API incluye sessions" || fail "API sin sessions"

grep -q "renderWeb\|Web Analytics\|GA4" "$JS" && pass "Frontend incluye sección web" || fail "Frontend no incluye sección web"
grep -q "sessions\|users\|conversion\|forms\|avg" "$JS" && pass "Frontend muestra KPIs GA4" || fail "Frontend sin KPIs GA4"
grep -q "tma-chart-web-sessions\|line" "$JS" && pass "Frontend define chart de sesiones" || fail "Frontend sin chart sesiones"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "PHP API sin errores" || fail "Error sintaxis API"

SHAPE=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
\$d=TMA_Panel_API::get_dashboard(new WP_REST_Request('GET','/tma-panel/v1/dashboard'))->get_data();
\$ok=isset(\$d['web']) && isset(\$d['web']['sessions']) && isset(\$d['web']['top_pages']) && isset(\$d['web']['sessions_history']);
echo \$ok?'OK':'BAD';
" 2>/dev/null || echo "ERR")
[ "$SHAPE" = "OK" ] && pass "Contrato dashboard incluye bloque web" || fail "Bloque web faltante ($SHAPE)"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
