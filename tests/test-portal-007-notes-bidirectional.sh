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

echo "\nTICKET-PORTAL-007 — Notas bidireccionales\n"

grep -q "module\|item_id" "$API" && pass "API maneja módulo + item_id" || fail "API sin módulo/item_id"
grep -q "ORDER BY created_at DESC" "$API" && pass "API timeline cronológico" || fail "API sin timeline"
grep -q "visibility\|user_id" "$API" && pass "API filtra por rol/usuario" || fail "API sin filtro por rol"
grep -q "notes" "$JS" && pass "Frontend sección notas existe" || fail "Frontend notas faltante"
grep -q "timeline\|module\|item" "$JS" && pass "Frontend timeline muestra módulo/item" || fail "Frontend sin timeline módulo/item"
grep -q "Dejar nota\|note-form" "$JS" && pass "Formulario crear nota" || fail "Formulario nota faltante"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php 2>&1 | grep -q "No syntax errors" && pass "API sin errores de sintaxis" || fail "Error sintaxis API"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
