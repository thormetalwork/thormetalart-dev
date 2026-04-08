#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
CACHE_DIR="$PLUGIN_DIR/cache/html"

pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-PORTAL-005 — Document pipeline\n"

DOCS_CLASS="$PLUGIN_DIR/includes/class-tma-panel-docs.php"
API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
JS_FILE="$PLUGIN_DIR/assets/js/panel.js"

[ -f "$DOCS_CLASS" ] && pass "class-tma-panel-docs.php existe" || fail "class-tma-panel-docs.php faltante"
[ -d "$CACHE_DIR" ] && pass "cache/html existe" || fail "cache/html faltante"

if [ -d "$CACHE_DIR" ]; then
  COUNT=$(find "$CACHE_DIR" -maxdepth 1 -type f -name '*.html' | wc -l | tr -d ' ')
  [ "$COUNT" -ge 12 ] && pass "cache/html tiene >=12 docs" || fail "cache/html tiene <12 docs ($COUNT)"
else
  fail "no se pudo contar docs en cache"
fi

grep -q "/documents/(?P<code>" "$API_FILE" && pass "API route /documents/{code}/content registrada" || fail "Ruta /documents/{code}/content faltante"
grep -q "TMA_Panel_Docs" "$API_FILE" && pass "API usa TMA_Panel_Docs" || fail "API no usa TMA_Panel_Docs"

grep -q "class TMA_Panel_Docs" "$DOCS_CLASS" && pass "Clase docs definida" || fail "Clase docs faltante"
grep -q "get_document_content" "$DOCS_CLASS" && pass "Método get_document_content" || fail "Método get_document_content faltante"
grep -q "migrate_portal_docs_to_cache" "$DOCS_CLASS" && pass "Método migrate_portal_docs_to_cache" || fail "Método migrate_portal_docs_to_cache faltante"

grep -q "shadowRoot\|attachShadow" "$JS_FILE" && pass "Viewer usa Shadow DOM" || fail "Viewer sin Shadow DOM"
grep -q "watermark\|user-select:\s*none" "$JS_FILE" && pass "Viewer aplica watermark + anti-copy" || fail "Viewer sin watermark/anti-copy"
grep -q "btn-view-doc\|Ver" "$JS_FILE" && pass "Cards con botón Ver" || fail "Botón Ver faltante"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-docs.php 2>&1 | grep -q "No syntax errors" && pass "PHP docs class sin errores" || fail "Error sintaxis docs class"

SHAPE=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
if (!class_exists('TMA_Panel_Docs')) { echo 'NO_CLASS'; exit; }
\$res = TMA_Panel_Docs::get_document_content('01_metodologia_maestra');
if (is_wp_error(\$res)) { echo 'ERR'; exit; }
\$ok = isset(\$res['html']) && isset(\$res['updated_at']) && strlen(\$res['html']) > 50;
echo \$ok ? 'OK' : 'BAD';
" 2>/dev/null || echo "FAIL")
[ "$SHAPE" = "OK" ] && pass "Contenido de documento servido desde cache" || fail "Lectura de documento falló ($SHAPE)"

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
