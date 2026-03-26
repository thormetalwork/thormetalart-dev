#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"
pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-DASH-008 — Instagram section\n"
API="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS="$PLUGIN_DIR/assets/js/panel.js"

grep -q "instagram\|ig" "$API" && pass "API incluye bloque instagram" || fail "API no incluye instagram"
grep -q "followers\|reach\|engagement" "$API" && pass "API incluye KPIs IG" || fail "API sin KPIs IG"
grep -q "reach_history\|sparkline" "$API" && pass "API incluye historial reach" || fail "API sin historial reach"

grep -q "renderInstagram\|Instagram" "$JS" && pass "Frontend incluye sección Instagram" || fail "Frontend no incluye sección Instagram"
grep -q "followers\|reach\|engagement" "$JS" && pass "Frontend muestra KPIs IG" || fail "Frontend sin KPIs IG"
grep -q "tma-chart-instagram-reach\|line" "$JS" && pass "Frontend define sparkline/chart reach" || fail "Frontend sin chart reach"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "PHP API sin errores" || fail "Error sintaxis API"

SHAPE=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
\$d=TMA_Panel_API::get_dashboard(new WP_REST_Request('GET','/tma-panel/v1/dashboard'))->get_data();
\$ok=isset(\$d['instagram']) && isset(\$d['instagram']['followers']) && isset(\$d['instagram']['reach_history']);
echo \$ok?'OK':'BAD';
" 2>/dev/null || echo "ERR")
[ "$SHAPE" = "OK" ] && pass "Contrato dashboard incluye bloque instagram" || fail "Bloque instagram faltante ($SHAPE)"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
