#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → TICKET-WP-033: Traducciones ES completas en TranslatePress
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
MYSQL_CMD="docker exec tma_dev_mysql mysql --default-character-set=utf8mb4 -u thormetalart_dev -pQHUTkbfZ27Pcfgk5AOlvAjMcgqSN9tnQ thormetalart_wp"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }
db() { $MYSQL_CMD -sN -e "$1" 2>/dev/null; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-WP-033 — Traducciones ES completas en TranslatePress ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. ESTADO GENERAL: sin traducir < 10 (solo los inevitables)
# ─────────────────────────────────────────────────────────────────
echo "📊 Estado general de traducciones"

UNTRANSLATED=$(db "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es WHERE status = 0;")
echo "  → Strings sin traducir: $UNTRANSLATED"
[ "$UNTRANSLATED" -lt 10 ] \
  && pass "Menos de 10 strings sin traducir en diccionario (target: < 10)" \
  || fail "Aún hay $UNTRANSLATED strings sin traducir (target: < 10)"

# ─────────────────────────────────────────────────────────────────
# 2. STRINGS CRÍTICOS DE UI — deben tener traducción
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔑 Strings críticos de UI traducidos"

check_translated() {
  local original="$1"
  local expected="$2"
  local actual
  actual=$(db "SELECT translated FROM tma_trp_dictionary_en_us_es_es WHERE original = '$original' AND status > 0 LIMIT 1;")
  if [ -n "$actual" ]; then
    pass "\"$original\" → \"$actual\""
  else
    fail "\"$original\" sin traducción (esperado: \"$expected\")"
  fi
}

check_translated "View Project" "Ver Proyecto"
check_translated "Send message" "Enviar mensaje"
check_translated "Back to Portfolio" "Volver al Portafolio"
check_translated "Your name" "Tu nombre"
check_translated "Message *" "Mensaje *"
check_translated "— Select a service —" "— Selecciona un servicio —"
check_translated "Ready to Start Your Project?" "¿Listo para Comenzar tu Proyecto?"
check_translated "Tell us about your project..." "Cuéntanos sobre tu proyecto..."
check_translated "Metal Furniture" "Muebles de Metal"
check_translated "Ornamental Fences" "Cercas Ornamentales"
check_translated "Terms of Service" "Términos de Servicio"
check_translated "Stainless steel" "Acero inoxidable"
check_translated "Wrought iron" "Hierro forjado"
check_translated "Railings &amp; Handrails" "Barandas y Pasamanos"
check_translated "Every piece is custom. Every project is unique." "Cada pieza es personalizada. Cada proyecto es único."

# ─────────────────────────────────────────────────────────────────
# 3. JUNK/SQL ya no aparece como sin traducir
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧹 SQL junk marcado como procesado (no untranslated)"

SQL_JUNK=$(db "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es WHERE status = 0 AND (original LIKE 'INSERT INTO%' OR original LIKE 'SELECT %');")
[ "$SQL_JUNK" -eq 0 ] \
  && pass "0 strings SQL junk sin traducir" \
  || fail "$SQL_JUNK strings SQL junk aún sin traducir"

# ─────────────────────────────────────────────────────────────────
# 4. SITIO /es/ RESPONDE CON CONTENIDO EN ESPAÑOL
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 Verificación HTTP /es/"

ES_HTML=$(curl -s -k --max-time 10 "https://dev.thormetalart.com/es/" 2>/dev/null || echo "CURL_FAILED")

if [ "$ES_HTML" = "CURL_FAILED" ]; then
  fail "No se pudo conectar a /es/"
else
  echo "$ES_HTML" | grep -qi 'lang="es\|lang=.es' \
    && pass "HTML tiene lang=es en respuesta de /es/" \
    || fail "HTML no tiene atributo lang=es"

  echo "$ES_HTML" | grep -qi "portones\|servicios\|contacto\|nuestros\|personalizado" \
    && pass "Contenido en español presente en /es/" \
    || fail "Contenido en español no detectado en /es/"
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
