#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → Test suite for TICKET-PANEL-007: i18n ES/EN
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-007 — i18n ES/EN con diccionario JS           ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence"

[ -f "$PLUGIN_DIR/assets/js/i18n.js" ] \
  && pass "i18n.js exists" \
  || fail "i18n.js missing"

# ─────────────────────────────────────────────────────────────────
# 2. i18n.js — DICTIONARY STRUCTURE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📖 Dictionary structure"

I18N="$PLUGIN_DIR/assets/js/i18n.js"

if [ -f "$I18N" ]; then
  # Has ES and EN translations
  grep -q "'es'\|\"es\"\|\bes:" "$I18N" \
    && pass "Has ES translations" \
    || fail "Missing ES translations"

  grep -q "'en'\|\"en\"\|\ben:" "$I18N" \
    && pass "Has EN translations" \
    || fail "Missing EN translations"

  # Navigation keys
  for key in "nav.dashboard" "nav.documents" "nav.leads" "nav.notes" "nav.audit"; do
    grep -q "$key" "$I18N" \
      && pass "Key: $key" \
      || fail "Missing key: $key"
  done

  # Common keys
  for key in "common.loading" "common.logout" "common.save" "common.cancel"; do
    grep -q "$key" "$I18N" \
      && pass "Key: $key" \
      || fail "Missing key: $key"
  done

  # Dashboard keys
  grep -q 'dashboard\.' "$I18N" \
    && pass "Has dashboard.* keys" \
    || fail "Missing dashboard.* keys"

  # Documents keys
  grep -q 'documents\.' "$I18N" \
    && pass "Has documents.* keys" \
    || fail "Missing documents.* keys"

  # Leads keys
  grep -q 'leads\.' "$I18N" \
    && pass "Has leads.* keys" \
    || fail "Missing leads.* keys"

  # Notes keys
  grep -q 'notes\.' "$I18N" \
    && pass "Has notes.* keys" \
    || fail "Missing notes.* keys"

  # Exports t() function
  grep -q 'function t\|function.*translate\|export.*function' "$I18N" \
    && pass "Exports t() or translate function" \
    || fail "Missing t() function export"

  # setLang function
  grep -q 'setLang\|setLanguage\|switchLang' "$I18N" \
    && pass "Has setLang function" \
    || fail "Missing setLang function"

  # localStorage persistence
  grep -q 'localStorage' "$I18N" \
    && pass "Uses localStorage for persistence" \
    || fail "Missing localStorage persistence"

  # Min string count (at least 30 unique keys)
  KEY_COUNT=$(grep -oE "'[a-z]+\.[a-z_]+'" "$I18N" | sort -u | wc -l)
  [ "$KEY_COUNT" -ge 20 ] \
    && pass "At least 20 unique i18n keys ($KEY_COUNT found)" \
    || fail "Too few i18n keys ($KEY_COUNT found, need 20+)"
else
  for i in $(seq 1 18); do fail "i18n.js missing"; done
fi

# ─────────────────────────────────────────────────────────────────
# 3. PANEL.JS — USES i18n
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔗 panel.js integration"

JS="$PLUGIN_DIR/assets/js/panel.js"

grep -q 't(' "$JS" \
  && pass "panel.js uses t() function" \
  || fail "panel.js doesn't use t()"

# ─────────────────────────────────────────────────────────────────
# 4. PANEL.PHP — LOADS i18n.js
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📄 panel.php loads i18n"

PANEL="$PLUGIN_DIR/templates/panel.php"

grep -q 'i18n.js\|i18n' "$PANEL" \
  && pass "panel.php loads i18n.js" \
  || fail "panel.php doesn't load i18n.js"

# ─────────────────────────────────────────────────────────────────
# 5. LANG SWITCH IN HTML
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 Language switch"

grep -q 'lang-switch' "$PANEL" \
  && pass "Language switch HTML present" \
  || fail "Language switch missing"

grep -q 'data-lang="es"' "$PANEL" \
  && pass "ES button has data-lang" \
  || fail "Missing ES data-lang"

grep -q 'data-lang="en"' "$PANEL" \
  && pass "EN button has data-lang" \
  || fail "Missing EN data-lang"

# ─────────────────────────────────────────────────────────────────
#  RESULTS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  RESULTS: $PASS passed / $FAIL failed / $TOTAL total"
echo "════════════════════════════════════════════════════════════"
echo ""

[ "$FAIL" -eq 0 ] && echo "🎉 ALL TESTS PASSED" || echo "💔 SOME TESTS FAILED"
exit "$FAIL"
