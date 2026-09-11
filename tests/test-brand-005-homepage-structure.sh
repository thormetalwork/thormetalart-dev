#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-005 — Tests: front-page.html reconstruido con los 8
# patrones "Lujo Forjado" en el orden correcto.
# TDD RED: antes de implementar, front-page.html usa el homepage
# antiguo (tma-hero-section, tma-services-grid, etc.).
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
FRONT_PAGE="${THEME_DIR}/templates/front-page.html"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-005 — Homepage Structure Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_template_uses_8_patterns_in_order() {
    local expected=(
        "thormetalart/hero-forjado"
        "thormetalart/disciplines"
        "thormetalart/selected-work"
        "thormetalart/atelier"
        "thormetalart/quote-band"
        "thormetalart/process-forjado"
        "thormetalart/cta-forjado"
        "thormetalart/client-logos"
    )
    local actual
    actual=$(grep -o '"slug":"thormetalart/[a-z-]*"' "$FRONT_PAGE" | sed -E 's/"slug":"(.*)"/\1/')
    local actual_arr=()
    while IFS= read -r line; do actual_arr+=("$line"); done <<< "$actual"

    if [[ "${expected[*]}" == "${actual_arr[*]}" ]]; then
        pass "front-page.html referencia los 8 patrones en el orden correcto"
    else
        fail "Orden/contenido de patrones incorrecto. Esperado: ${expected[*]} — Obtenido: ${actual_arr[*]}"
    fi
}

test_header_and_footer_present() {
    if grep -q '"slug":"header"' "$FRONT_PAGE" && grep -q '"slug":"footer"' "$FRONT_PAGE"; then
        pass "front-page.html incluye header y footer template parts"
    else
        fail "front-page.html no incluye header/footer template parts"
    fi
}

test_homepage_renders_200() {
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/")
    if [[ "$status" == "200" ]]; then
        pass "Homepage (${BASE_URL}/) responde 200"
    else
        fail "Homepage (${BASE_URL}/) respondió ${status}"
    fi
}

test_single_h1_with_expected_copy() {
    local html
    html=$(curl -s "${BASE_URL}/")
    local h1_count
    h1_count=$(echo "$html" | grep -o "<h1" | wc -l)
    if [[ "$h1_count" -eq 1 ]] && echo "$html" | grep -q "Steel, shaped"; then
        pass "Existe exactamente un <h1> con el copy 'Steel, shaped...intent.'"
    else
        fail "H1 count=${h1_count} o copy incorrecto (esperado 1 con 'Steel, shaped')"
    fi
}

test_sections_render_in_dom_order() {
    local html
    html=$(curl -s "${BASE_URL}/")
    local ids
    ids=$(echo "$html" | grep -o 'id="\(hero\|disciplines\|work\|atelier\|philosophy\|process\|contact\)"')
    local expected='id="hero"
id="disciplines"
id="work"
id="atelier"
id="philosophy"
id="process"
id="contact"'
    if [[ "$ids" == "$expected" ]]; then
        pass "Las secciones ancladas aparecen en el orden correcto en el DOM"
    else
        fail "Orden de secciones incorrecto en el DOM. Obtenido: ${ids}"
    fi
}

test_no_orphaned_old_homepage_classes() {
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "$html" | grep -q "tma-hero-section\|tma-services-grid\|tma-about-snippet"; then
        fail "El homepage todavía contiene marcado del diseño antiguo"
    else
        pass "El homepage ya no contiene marcado del diseño antiguo (tma-hero-section, tma-services-grid, etc.)"
    fi
}

test_real_photos_remapped() {
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "$html" | grep -q "uploads/2026/04/"; then
        pass "Las imágenes apuntan a archivos ya existentes en wp-content/uploads/2026/04/"
    else
        fail "No se encontraron referencias a uploads/2026/04/ en el homepage renderizado"
    fi
}

test_template_uses_8_patterns_in_order
test_header_and_footer_present
test_homepage_renders_200
test_single_h1_with_expected_copy
test_sections_render_in_dom_order
test_no_orphaned_old_homepage_classes
test_real_photos_remapped

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultado: ${PASS} PASS / ${FAIL} FAIL"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
