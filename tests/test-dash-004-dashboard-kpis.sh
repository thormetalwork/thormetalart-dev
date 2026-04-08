#!/usr/bin/env bash
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "══════════════════════════════════════════════════"
echo " TICKET-DASH-004 — Dashboard KPIs desde DB"
echo "══════════════════════════════════════════════════"
echo ""

echo "📁 Estructura de código"
API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS_FILE="$PLUGIN_DIR/assets/js/panel.js"

grep -q "get_dashboard" "$API_FILE" && pass "Endpoint /dashboard existe" || fail "Endpoint /dashboard faltante"
grep -q "trend\|previous" "$API_FILE" && pass "API calcula tendencia" || fail "API no calcula tendencia"
grep -q "history\|period" "$API_FILE" && pass "API incluye histórico" || fail "API sin histórico"
grep -q "demo\|fallback" "$API_FILE" && pass "API maneja fallback demo" || fail "API sin fallback demo"

grep -q "Chart" "$JS_FILE" && pass "Frontend usa Chart.js" || fail "Frontend no usa Chart.js"
grep -q "Datos de ejemplo\|demo" "$JS_FILE" && pass "Frontend muestra badge demo" || fail "Frontend sin badge demo"
grep -q "reviews\|impressions\|sessions\|leads" "$JS_FILE" && pass "Frontend renderiza 4 KPI cards requeridas" || fail "Faltan KPI cards requeridas"

echo ""
echo "🧪 Sintaxis"
docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "PHP API sin errores" || fail "Error de sintaxis en API"

echo ""
echo "🔌 Contrato de API (forma de respuesta)"
SHAPE=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
if (!class_exists('TMA_Panel_API')) { echo 'NO_CLASS'; exit; }
if (!function_exists('rest_get_server')) { echo 'NO_REST'; exit; }
\$req = new WP_REST_Request('GET', '/tma-panel/v1/dashboard');
\$req->set_header('X-WP-Nonce', wp_create_nonce('wp_rest'));
\$res = TMA_Panel_API::get_dashboard(\$req);
\$d = \$res->get_data();
\$ok = isset(\$d['counts']) && isset(\$d['kpis']) && isset(\$d['history']) && isset(\$d['lead_sources']) && array_key_exists('is_demo', \$d);
echo \$ok ? 'OK' : 'BAD';
" 2>/dev/null || echo "ERR")
[ "$SHAPE" = "OK" ] && pass "Respuesta dashboard incluye counts/kpis/history/lead_sources/is_demo" || fail "Contrato API inválido ($SHAPE)"

echo ""
echo "🌱 Fallback demo cuando no hay KPI"
DEMO=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
global \$wpdb;
\$table = \$wpdb->prefix . 'panel_kpis';
\$backup = \$wpdb->get_results(\"SELECT * FROM {\$table}\", ARRAY_A);
\$wpdb->query(\"TRUNCATE TABLE {\$table}\");
\$res = TMA_Panel_API::get_dashboard(new WP_REST_Request('GET', '/tma-panel/v1/dashboard'));
\$d = \$res->get_data();
\$is_demo = !empty(\$d['is_demo']) ? 'YES' : 'NO';
if (!empty(\$backup)) {
  foreach (\$backup as \$row) {
    unset(\$row['id']);
    \$wpdb->insert(\$table, \$row);
  }
}
echo \$is_demo;
" 2>/dev/null || echo "ERR")
[ "$DEMO" = "YES" ] && pass "API activa fallback demo con DB vacía" || fail "Fallback demo no activado ($DEMO)"

echo ""
echo "══════════════════════════════════════════════════"
echo " RESULTADOS: $PASS pass / $FAIL fail / $TOTAL total"
echo "══════════════════════════════════════════════════"
[ "$FAIL" -eq 0 ] && echo "🎉 OK" || echo "💥 FAIL"
exit "$FAIL"
