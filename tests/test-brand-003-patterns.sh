#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-003 — Tests: 8 patrones FSE nuevos + huérfanos retirados
# TDD RED: antes de implementar, los 8 patrones nuevos no existen y
# los 7 patrones huérfanos siguen presentes.
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
PATTERNS_DIR="${THEME_DIR}/patterns"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-003 — Patrones Lujo Forjado Tests"
echo "══════════════════════════════════════════════════"
echo ""

NEW_PATTERNS=(
    "hero-forjado.php:thormetalart/hero-forjado"
    "disciplines.php:thormetalart/disciplines"
    "selected-work.php:thormetalart/selected-work"
    "atelier.php:thormetalart/atelier"
    "quote-band.php:thormetalart/quote-band"
    "process-forjado.php:thormetalart/process-forjado"
    "cta-forjado.php:thormetalart/cta-forjado"
    "client-logos.php:thormetalart/client-logos"
)

ORPHANED_PATTERNS=(
    "hero-section.php"
    "service-card.php"
    "testimonial-card.php"
    "trust-bar.php"
    "cta-banner.php"
    "process-step.php"
    "faq-item.php"
)

test_new_patterns_exist_with_slug() {
    for entry in "${NEW_PATTERNS[@]}"; do
        file="${entry%%:*}"
        slug="${entry##*:}"
        path="${PATTERNS_DIR}/${file}"
        if [[ -f "$path" ]] && grep -q "Slug: ${slug}" "$path"; then
            pass "${file} existe con Slug: ${slug}"
        else
            fail "${file} no existe o no declara Slug: ${slug}"
        fi
    done
}

test_new_patterns_categories() {
    for entry in "${NEW_PATTERNS[@]}"; do
        file="${entry%%:*}"
        path="${PATTERNS_DIR}/${file}"
        if [[ -f "$path" ]] && grep -q "Categories: thormetalart" "$path"; then
            pass "${file} declara Categories: thormetalart"
        else
            fail "${file} no declara Categories: thormetalart"
        fi
    done
}

test_orphaned_patterns_removed() {
    for file in "${ORPHANED_PATTERNS[@]}"; do
        path="${PATTERNS_DIR}/${file}"
        if [[ ! -f "$path" ]]; then
            pass "Patrón huérfano retirado: ${file}"
        else
            fail "Patrón huérfano todavía presente: ${file}"
        fi
    done
}

test_php_syntax() {
    for entry in "${NEW_PATTERNS[@]}"; do
        file="${entry%%:*}"
        path="${PATTERNS_DIR}/${file}"
        if [[ -f "$path" ]] && php -l "$path" > /dev/null 2>&1; then
            pass "${file} sin errores de sintaxis PHP"
        else
            fail "${file} tiene errores de sintaxis PHP"
        fi
    done
}

test_dark_light_alternation() {
    if grep -q "obsidian" "${PATTERNS_DIR}/hero-forjado.php" 2>/dev/null; then
        pass "hero-forjado.php usa fondo oscuro (obsidian)"
    else
        fail "hero-forjado.php no usa token obsidian"
    fi

    if grep -q "paper" "${PATTERNS_DIR}/disciplines.php" 2>/dev/null; then
        pass "disciplines.php usa fondo claro (paper)"
    else
        fail "disciplines.php no usa token paper"
    fi

    if grep -q "obsidian" "${PATTERNS_DIR}/selected-work.php" 2>/dev/null; then
        pass "selected-work.php usa fondo oscuro (obsidian)"
    else
        fail "selected-work.php no usa token obsidian"
    fi

    if grep -q "paper" "${PATTERNS_DIR}/atelier.php" 2>/dev/null; then
        pass "atelier.php usa fondo claro (paper)"
    else
        fail "atelier.php no usa token paper"
    fi

    if grep -q "paper" "${PATTERNS_DIR}/process-forjado.php" 2>/dev/null; then
        pass "process-forjado.php usa fondo claro (paper)"
    else
        fail "process-forjado.php no usa token paper"
    fi
}

test_real_images_used() {
    if grep -qE 'uploads/2026/04/' "${PATTERNS_DIR}/hero-forjado.php" "${PATTERNS_DIR}/disciplines.php" \
        "${PATTERNS_DIR}/selected-work.php" "${PATTERNS_DIR}/atelier.php" "${PATTERNS_DIR}/quote-band.php" \
        "${PATTERNS_DIR}/process-forjado.php" "${PATTERNS_DIR}/cta-forjado.php" 2>/dev/null; then
        pass "Los patrones referencian fotos reales de uploads/2026/04/ (no placeholders base64)"
    else
        fail "Los patrones no referencian fotos reales de uploads/2026/04/"
    fi
}

test_hooks_for_js_css_present() {
    if grep -q "tma-sparks" "${PATTERNS_DIR}/hero-forjado.php" 2>/dev/null \
        && grep -q "tma-rv" "${PATTERNS_DIR}/hero-forjado.php" 2>/dev/null; then
        pass "hero-forjado.php incluye hooks tma-sparks / tma-rv para TICKET-BRAND-004"
    else
        fail "hero-forjado.php no incluye hooks tma-sparks / tma-rv"
    fi

    if grep -q "tma-marquee" "${PATTERNS_DIR}/client-logos.php" 2>/dev/null; then
        pass "client-logos.php incluye hook tma-marquee para TICKET-BRAND-004"
    else
        fail "client-logos.php no incluye hook tma-marquee"
    fi
}

test_homepage_still_responds() {
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/")
    if [[ "$status" == "200" ]]; then
        pass "Homepage (${BASE_URL}/) responde 200 tras los cambios"
    else
        fail "Homepage (${BASE_URL}/) respondió ${status}"
    fi
}

test_new_patterns_exist_with_slug
test_new_patterns_categories
test_orphaned_patterns_removed
test_php_syntax
test_dark_light_alternation
test_real_images_used
test_hooks_for_js_css_present
test_homepage_still_responds

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultado: ${PASS} PASS / ${FAIL} FAIL"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
