#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-002 — Tests: logos SVG oficiales en header/footer
# TDD RED: antes de implementar, header/footer usan wp:site-logo /
# wp:site-title en vez del SVG oficial de la marca.
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
UPLOADS_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/uploads/2026/07"
HEADER="${THEME_DIR}/parts/header.html"
FOOTER="${THEME_DIR}/parts/footer.html"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-002 — Logos SVG Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_svg_files_exist() {
    if [[ -f "${UPLOADS_DIR}/logo-thor-metal-art-black.svg" && -f "${UPLOADS_DIR}/logo-thor-metal-art-white.svg" ]]; then
        pass "Logos SVG negro y blanco subidos a uploads/2026/07/"
    else
        fail "Faltan uno o ambos logos SVG en uploads/2026/07/"
    fi
}

test_svg_no_script_tags() {
    if grep -qi "<script\|onload=\|onerror=" "${UPLOADS_DIR}/logo-thor-metal-art-black.svg" "${UPLOADS_DIR}/logo-thor-metal-art-white.svg"; then
        fail "Los SVG contienen patrones potencialmente peligrosos (script/onload/onerror)"
    else
        pass "Los SVG no contienen scripts ni handlers inline (seguros para servir)"
    fi
}

test_header_uses_white_logo() {
    if grep -q "logo-thor-metal-art-white.svg" "${HEADER}" && grep -q 'alt="Thor Metal Art"' "${HEADER}"; then
        pass "header.html usa el SVG blanco con alt=\"Thor Metal Art\""
    else
        fail "header.html NO usa el SVG blanco oficial"
    fi
}

test_footer_uses_logo() {
    if grep -q "logo-thor-metal-art-white.svg" "${FOOTER}"; then
        pass "footer.html usa el logo SVG oficial"
    else
        fail "footer.html NO usa el logo SVG oficial"
    fi
}

test_homepage_renders_logo() {
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "${html}" | grep -q "logo-thor-metal-art-white.svg"; then
        pass "Homepage renderiza el logo SVG oficial"
    else
        fail "Homepage NO renderiza el logo SVG oficial"
    fi
}

test_logo_svg_reachable() {
    local status
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/wp-content/uploads/2026/07/logo-thor-metal-art-white.svg")
    if [[ "${status}" == "200" ]]; then
        pass "El SVG del logo blanco responde HTTP 200"
    else
        fail "El SVG del logo blanco responde HTTP ${status} (esperado 200)"
    fi
}

test_svg_files_exist
test_svg_no_script_tags
test_header_uses_white_logo
test_footer_uses_logo
test_homepage_renders_logo
test_logo_svg_reachable

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultados: ${PASS} passed, ${FAIL} failed"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
