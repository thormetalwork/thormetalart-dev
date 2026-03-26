#!/usr/bin/env bash
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "══════════════════════════════════════════════════"
echo " TICKET-DASH-005 — Cron APIs externas"
echo "══════════════════════════════════════════════════"
echo ""

CRON_FILE="$PLUGIN_DIR/includes/class-tma-panel-cron.php"
MAIN_FILE="$PLUGIN_DIR/tma-panel.php"

echo "📁 Archivos"
[ -f "$CRON_FILE" ] && pass "class-tma-panel-cron.php existe" || fail "class-tma-panel-cron.php faltante"

grep -q "class-tma-panel-cron.php" "$MAIN_FILE" && pass "Bootstrap incluye clase cron" || fail "Bootstrap no incluye clase cron"

grep -q "tma_panel_sync_external_kpis" "$MAIN_FILE" && pass "Hook cron registrado" || fail "Hook cron no registrado"

echo ""
echo "🔧 Clase cron"
if [ -f "$CRON_FILE" ]; then
  grep -q "class TMA_Panel_Cron" "$CRON_FILE" && pass "Clase TMA_Panel_Cron definida" || fail "Clase TMA_Panel_Cron faltante"
  grep -q "schedule_event" "$CRON_FILE" && pass "Método schedule_event" || fail "Falta schedule_event"
  grep -q "sync_all_sources" "$CRON_FILE" && pass "Método sync_all_sources" || fail "Falta sync_all_sources"
  grep -q "GBP_API_KEY\|GA4\|IG_ACCESS_TOKEN" "$CRON_FILE" && pass "Lee API keys configurables" || fail "No maneja API keys"
  grep -q "error_log\|warning\|WARN" "$CRON_FILE" && pass "Registra warning si faltan keys" || fail "No registra warnings"
  grep -q "panel_kpis" "$CRON_FILE" && pass "Guarda métricas en panel_kpis" || fail "No guarda en panel_kpis"
else
  fail "Tests de clase omitidos (archivo no existe)"
  fail "Tests de clase omitidos (archivo no existe)"
  fail "Tests de clase omitidos (archivo no existe)"
  fail "Tests de clase omitidos (archivo no existe)"
  fail "Tests de clase omitidos (archivo no existe)"
  fail "Tests de clase omitidos (archivo no existe)"
fi

echo ""
echo "🧪 Sintaxis PHP"
for f in "includes/class-tma-panel-cron.php" "tma-panel.php"; do
  if [ -f "$PLUGIN_DIR/$f" ]; then
    docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" && pass "Syntax OK: $f" || fail "Syntax error: $f"
  else
    fail "Archivo faltante para sintaxis: $f"
  fi
done

echo ""
echo "⏰ Programación cron"
CRON_SCHEDULED=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
if (!class_exists('TMA_Panel_Cron')) { echo 'NO_CLASS'; exit; }
TMA_Panel_Cron::schedule_event();
echo wp_next_scheduled('tma_panel_sync_external_kpis') ? 'YES' : 'NO';
" 2>/dev/null || echo "ERR")
[ "$CRON_SCHEDULED" = "YES" ] && pass "Evento cron diario programado" || fail "Evento cron no programado ($CRON_SCHEDULED)"

echo ""
echo "🛡️ Keys faltantes no rompen sincronización"
NOFAIL=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
if (!class_exists('TMA_Panel_Cron')) { echo 'NO_CLASS'; exit; }
putenv('GBP_API_KEY=');
putenv('GA4_API_KEY=');
putenv('IG_ACCESS_TOKEN=');
try {
  TMA_Panel_Cron::sync_all_sources();
  echo 'OK';
} catch (Throwable $e) {
  echo 'FAIL';
}
" 2>/dev/null || echo "ERR")
[ "$NOFAIL" = "OK" ] && pass "Sin keys: no falla y mantiene datos" || fail "Sin keys: falló ($NOFAIL)"

echo ""
echo "💾 Inserción de datos cuando hay keys"
INSERT_OK=$(docker exec "$WP_CONTAINER" php -r "
require '/var/www/html/wp-load.php';
global \$wpdb;
if (!class_exists('TMA_Panel_Cron')) { echo 'NO_CLASS'; exit; }
putenv('GBP_API_KEY=test');
\$before = (int) \$wpdb->get_var('SELECT COUNT(*) FROM ' . \$wpdb->prefix . 'panel_kpis');
TMA_Panel_Cron::sync_source('gbp');
\$after = (int) \$wpdb->get_var('SELECT COUNT(*) FROM ' . \$wpdb->prefix . 'panel_kpis');
echo \$after > \$before ? 'YES' : 'NO';
" 2>/dev/null || echo "ERR")
[ "$INSERT_OK" = "YES" ] && pass "Con key: inserta métricas en kpis" || fail "Con key: no insertó métricas ($INSERT_OK)"

echo ""
echo "══════════════════════════════════════════════════"
echo " RESULTADOS: $PASS pass / $FAIL fail / $TOTAL total"
echo "══════════════════════════════════════════════════"
[ "$FAIL" -eq 0 ] && echo "🎉 OK" || echo "💥 FAIL"
exit "$FAIL"
