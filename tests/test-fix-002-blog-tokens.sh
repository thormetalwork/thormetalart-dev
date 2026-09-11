#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-FIX-002 — Tests: tokens de fuente rotos en plantillas de blog
# TDD RED: antes del fix, archive.html/single.html referencian slugs
# de fuente inexistentes (--cormorant-garamond / --dm-sans) en vez de
# los slugs reales definidos en theme.json (--heading / --body).
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
ARCHIVE="${THEME_DIR}/templates/archive.html"
SINGLE="${THEME_DIR}/templates/single.html"
THEME_JSON="${THEME_DIR}/theme.json"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-FIX-002 — Blog Font Tokens Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_no_broken_slugs() {
    if grep -qE "cormorant-garamond|dm-sans" "${ARCHIVE}" "${SINGLE}"; then
        fail "Quedan referencias a slugs inexistentes (cormorant-garamond/dm-sans)"
    else
        pass "No quedan referencias a slugs de fuente inexistentes"
    fi
}

test_real_slugs_defined_in_theme_json() {
    if grep -q '"slug": *"heading"' "${THEME_JSON}" && grep -q '"slug": *"body"' "${THEME_JSON}"; then
        pass "theme.json define los slugs reales 'heading' y 'body'"
    else
        fail "theme.json NO define los slugs 'heading'/'body' esperados"
    fi
}

test_archive_uses_real_slugs() {
    if grep -q -- '--wp--preset--font-family--heading' "${ARCHIVE}" && grep -q -- '--wp--preset--font-family--body' "${ARCHIVE}"; then
        pass "archive.html usa var(--wp--preset--font-family--heading/body)"
    else
        fail "archive.html NO usa los slugs reales de fuente"
    fi
}

test_single_uses_real_slugs() {
    if grep -q -- '--wp--preset--font-family--heading' "${SINGLE}" && grep -q -- '--wp--preset--font-family--body' "${SINGLE}"; then
        pass "single.html usa var(--wp--preset--font-family--heading/body)"
    else
        fail "single.html NO usa los slugs reales de fuente"
    fi
}

test_no_broken_slugs
test_real_slugs_defined_in_theme_json
test_archive_uses_real_slugs
test_single_uses_real_slugs

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultados: ${PASS} passed, ${FAIL} failed"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
