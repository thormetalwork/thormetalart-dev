#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-001 — Tests: tokens de marca "Lujo Forjado" en theme.json
# TDD RED: antes de implementar, theme.json no tiene la nueva paleta
# ni las nuevas tipografías, y no se encolan las fuentes nuevas.
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
THEME_JSON="${THEME_DIR}/theme.json"
FUNCTIONS="${THEME_DIR}/functions.php"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-001 — Tokens de Marca Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_json_valid() {
    if python3 -m json.tool "${THEME_JSON}" > /dev/null 2>&1; then
        pass "theme.json es JSON válido"
    else
        fail "theme.json NO es JSON válido"
    fi
}

test_new_colors_present() {
    local missing=0
    for slug in obsidian graphite forge ember titanium ash paper ink; do
        if ! grep -q "\"slug\": \"${slug}\"" "${THEME_JSON}"; then
            fail "Falta el color '${slug}' en theme.json"
            missing=1
        fi
    done
    [[ "${missing}" -eq 0 ]] && pass "Los 8 colores nuevos (obsidian/graphite/forge/ember/titanium/ash/paper/ink) están en theme.json"
}

test_new_fonts_present() {
    if grep -q "Archivo Expanded" "${THEME_JSON}" && grep -q "Fraunces" "${THEME_JSON}" && grep -q "'Inter'" "${THEME_JSON}"; then
        pass "Archivo Expanded, Fraunces e Inter están registrados en theme.json"
    else
        fail "Faltan una o más tipografías nuevas en theme.json"
    fi
}

test_old_tokens_untouched() {
    if grep -q '"slug": "heading"' "${THEME_JSON}" && grep -q '"slug": "body"' "${THEME_JSON}" \
        && grep -q '"slug": "primary"' "${THEME_JSON}" && grep -q '"slug": "accent"' "${THEME_JSON}"; then
        pass "Tokens antiguos (heading/body/primary/accent) siguen intactos"
    else
        fail "Se rompieron tokens antiguos (heading/body/primary/accent)"
    fi
}

test_fonts_enqueued() {
    if grep -q "wp_enqueue_style" "${FUNCTIONS}" && grep -q "Archivo+Expanded" "${FUNCTIONS}" && grep -q "Fraunces" "${FUNCTIONS}" && grep -q "Inter" "${FUNCTIONS}"; then
        pass "functions.php encola Archivo Expanded/Fraunces/Inter vía Google Fonts"
    else
        fail "functions.php NO encola las fuentes nuevas"
    fi
}

test_homepage_still_loads() {
    local status
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/")
    if [[ "${status}" == "200" ]]; then
        pass "Homepage sigue respondiendo HTTP 200 tras el cambio (retrocompatibilidad)"
    else
        fail "Homepage responde HTTP ${status} (esperado 200)"
    fi
}

test_json_valid
test_new_colors_present
test_new_fonts_present
test_old_tokens_untouched
test_fonts_enqueued
test_homepage_still_loads

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultados: ${PASS} passed, ${FAIL} failed"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
