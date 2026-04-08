#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
API="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS="$PLUGIN_DIR/assets/js/panel.js"
pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-PORTAL-006 — Aprobación de documentos\n"

grep -q "/documents/(?P<id>.*)/status" "$API" && pass "Route POST /documents/{id}/status" || fail "Ruta status faltante"
grep -q "update_document_status" "$API" && pass "Callback update_document_status" || fail "Callback update_document_status faltante"
grep -q "approved\|changes_requested\|pending" "$API" && pass "API valida 3 estados" || fail "API no valida 3 estados"
grep -q "approved_by\|approved_at\|notes" "$API" && pass "API guarda metadata aprobación" || fail "API sin metadata"

grep -q "Aprobado" "$JS" && pass "UI botón Aprobado" || fail "UI botón Aprobado faltante"
grep -q "Con cambios" "$JS" && pass "UI botón Con cambios" || fail "UI botón Con cambios faltante"
grep -q "textarea\|10" "$JS" && pass "Nota obligatoria min 10 chars" || fail "Validación nota faltante"
grep -q "progress\|%\|/12" "$JS" && pass "Barra de progreso global" || fail "Barra de progreso faltante"
grep -q "Siguiente\|Anterior\|prev\|next" "$JS" && pass "Navegación prev/next en viewer" || fail "Prev/next faltante"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "API sin errores sintaxis" || fail "Error sintaxis API"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
