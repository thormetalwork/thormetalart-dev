#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → TICKET-WP-032: Excluir panel de TranslatePress
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="tma_dev_wordpress"
PLUGIN_MAIN="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel/tma-panel.php"
PANEL_URL="https://panel-dev.thormetalart.com/login"
MAIN_URL="https://dev.thormetalart.com"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-WP-032 — Excluir panel de TranslatePress            ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. CÓDIGO: filtro registrado en tma-panel.php
# ─────────────────────────────────────────────────────────────────
echo "🔍 Verificación de código fuente"

grep -q "trp_stop_translating_page" "$PLUGIN_MAIN" \
  && pass "Filtro trp_stop_translating_page presente en tma-panel.php" \
  || fail "Filtro trp_stop_translating_page NO encontrado en tma-panel.php"

grep -q "tma_panel_current_route" "$PLUGIN_MAIN" \
  && pass "Usa tma_panel_current_route() en el filtro TP" \
  || fail "tma_panel_current_route() no se usa para el filtro TP"

# El filtro debe retornar true para rutas del panel (no solo false)
grep -A5 "trp_stop_translating_page" "$PLUGIN_MAIN" | grep -q "null !== tma_panel_current_route\|tma_panel_current_route.*!== null" \
  && pass "Lógica del filtro verifica tma_panel_current_route() !== null" \
  || fail "Lógica del filtro incorrecta — debe retornar true cuando es ruta panel"

# ─────────────────────────────────────────────────────────────────
# 2. PHP: sintaxis válida
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 Validación PHP"

docker exec "$WP_CONTAINER" php -l /var/www/html/wp-content/plugins/tma-panel/tma-panel.php 2>&1 \
  | grep -q "No syntax errors" \
  && pass "tma-panel.php sin errores de sintaxis" \
  || fail "tma-panel.php tiene errores de sintaxis"

# ─────────────────────────────────────────────────────────────────
# 3. HTTP: panel NO tiene atributos data-trp- en el HTML
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 Verificación HTTP — panel sin procesamiento TP"

PANEL_HTML=$(curl -s -k --max-time 10 "$PANEL_URL" 2>/dev/null || echo "CURL_FAILED")

if [ "$PANEL_HTML" = "CURL_FAILED" ]; then
  fail "No se pudo conectar a $PANEL_URL"
else
  echo "$PANEL_HTML" | grep -q "data-trp-" \
    && fail "Panel contiene atributos data-trp- (TranslatePress sigue procesando)" \
    || pass "Panel NO contiene atributos data-trp- (TP correctamente excluido)"

  echo "$PANEL_HTML" | grep -qi "panel ejecutivo\|tma-panel\|tma_panel" \
    && pass "Panel responde con contenido correcto" \
    || fail "Panel no responde con contenido esperado"
fi

# ─────────────────────────────────────────────────────────────────
# 4. HTTP: sitio principal SÍ tiene TP activo
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 Verificación HTTP — sitio principal con TP activo"

MAIN_HTML=$(curl -s -k --max-time 10 "$MAIN_URL" 2>/dev/null || echo "CURL_FAILED")

if [ "$MAIN_HTML" = "CURL_FAILED" ]; then
  fail "No se pudo conectar a $MAIN_URL"
else
  # TranslatePress agrega el language switcher o el selector de idioma
  echo "$MAIN_HTML" | grep -qi "trp-language-switcher\|data-trp-\|trp_language" \
    && pass "Sitio principal tiene marcadores de TranslatePress activos" \
    || fail "Sitio principal NO tiene marcadores TP (TP puede estar inactivo)"
fi

# ─────────────────────────────────────────────────────────────────
# RESULTADO
# ─────────────────────────────────────────────────────────────────
echo ""
echo "──────────────────────────────────────────────────────────────"
echo "  Resultados: ${PASS} pasaron / ${FAIL} fallaron / ${TOTAL} total"
echo "──────────────────────────────────────────────────────────────"

if [ "$FAIL" -gt 0 ]; then
  exit 1
fi
exit 0
