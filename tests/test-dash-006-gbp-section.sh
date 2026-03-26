#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"

pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-DASH-006 — Sección GBP\n"

API="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS="$PLUGIN_DIR/assets/js/panel.js"

grep -q "gbp" "$API" && pass "API incluye datos GBP" || fail "API no incluye datos GBP"
grep -q "impressions_search\|impressions_maps\|split" "$API" && pass "API incluye split Search vs Maps" || fail "API sin split Search vs Maps"

grep -q "renderGBP\|gbp" "$JS" && pass "Frontend incluye sección GBP" || fail "Frontend no incluye sección GBP"
grep -q "rating\|reviews\|impressions\|actions" "$JS" && pass "Frontend muestra KPIs GBP requeridos" || fail "Frontend sin KPIs GBP"
grep -q "stacked\|bar" "$JS" && pass "Frontend define gráfico stacked bar" || fail "Frontend sin gráfico stacked bar"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "PHP API sin errores" || fail "Error sintaxis API"

echo ""
GBP_SHAPE=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
\$res=TMA_Panel_API::get_dashboard(new WP_REST_Request('GET','/tma-panel/v1/dashboard'));
\$d=\$res->get_data();
\$ok=isset(\$d['gbp']) && isset(\$d['gbp']['rating']) && isset(\$d['gbp']['impressions_split']);
echo \$ok?'OK':'BAD';
" 2>/dev/null || echo "ERR")
[ "$GBP_SHAPE" = "OK" ] && pass "Contrato dashboard incluye bloque gbp" || fail "Bloque gbp no presente ($GBP_SHAPE)"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
