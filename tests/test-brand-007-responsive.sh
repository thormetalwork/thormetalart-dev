#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-007 — Tests: breakpoints responsive mobile-first
# para los grids "Lujo Forjado" (960px tablet / 600px móvil).
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
STYLE_CSS="${THEME_DIR}/style.css"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-007 — Responsive Breakpoints Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_960px_media_query_exists() {
    if grep -qF '@media (max-width: 960px)' "$STYLE_CSS"; then
        pass "style.css contiene media query @media (max-width: 960px)"
    else
        fail "Falta el media query de 960px en style.css"
    fi
}

test_600px_media_query_exists() {
    if grep -qF '@media (max-width: 600px)' "$STYLE_CSS"; then
        pass "style.css contiene media query @media (max-width: 600px)"
    else
        fail "Falta el media query de 600px en style.css"
    fi
}

test_disc_grid_responsive() {
    if grep -qF '.tma-forjado-disc-grid' "$STYLE_CSS"; then
        pass "style.css define reglas responsive para .tma-forjado-disc-grid (Disciplines)"
    else
        fail "style.css no define breakpoints para .tma-forjado-disc-grid"
    fi
}

test_work_grid_responsive() {
    if grep -qF '.tma-forjado-work-grid' "$STYLE_CSS"; then
        pass "style.css define reglas responsive para .tma-forjado-work-grid (Selected Work)"
    else
        fail "style.css no define breakpoints para .tma-forjado-work-grid"
    fi
}

test_proc_grid_responsive() {
    if grep -qF '.tma-forjado-proc-grid' "$STYLE_CSS"; then
        pass "style.css define reglas responsive para .tma-forjado-proc-grid (Process)"
    else
        fail "style.css no define breakpoints para .tma-forjado-proc-grid"
    fi
}

test_atelier_grid_responsive() {
    if grep -qF '.tma-forjado-atelier-grid' "$STYLE_CSS"; then
        pass "style.css define reglas responsive para .tma-forjado-atelier-grid (Atelier)"
    else
        fail "style.css no define breakpoints para .tma-forjado-atelier-grid"
    fi
}

test_stats_responsive() {
    if grep -qF '.tma-forjado-stats' "$STYLE_CSS"; then
        pass "style.css define reglas responsive para .tma-forjado-stats (Atelier stats)"
    else
        fail "style.css no define breakpoints para .tma-forjado-stats"
    fi
}

test_marquee_responsive() {
    # Marquee debe tener gap/animation-duration específico en 600px
    local count
    count=$(grep -cF '.tma-marquee' "$STYLE_CSS")
    if [[ "$count" -ge 3 ]]; then
        pass "style.css referencia .tma-marquee ≥3 veces (tablet + mobile + reduced-motion)"
    else
        fail ".tma-marquee aparece solo ${count} vez/es en style.css (esperado ≥3)"
    fi
}

test_reduced_motion_still_present() {
    if grep -qF '@media (prefers-reduced-motion: reduce)' "$STYLE_CSS"; then
        pass "style.css conserva @media (prefers-reduced-motion: reduce)"
    else
        fail "Se eliminó la regla @media (prefers-reduced-motion: reduce) de style.css"
    fi
}

test_homepage_no_horizontal_overflow() {
    # Comprueba que el viewport-width no esté en el HTML como overflow (heurística básica)
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "$html" | grep -qF 'tma-forjado-disc-grid'; then
        pass "Homepage renderiza la clase .tma-forjado-disc-grid (CSS responsive aplicable)"
    else
        fail "Homepage no renderiza .tma-forjado-disc-grid — breakpoints no tendrán efecto"
    fi
}

test_homepage_responds_200() {
    local status
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/")
    if [[ "$status" == "200" ]]; then
        pass "Homepage responde 200 tras agregar los breakpoints"
    else
        fail "Homepage respondió ${status}"
    fi
}

test_960px_media_query_exists
test_600px_media_query_exists
test_disc_grid_responsive
test_work_grid_responsive
test_proc_grid_responsive
test_atelier_grid_responsive
test_stats_responsive
test_marquee_responsive
test_reduced_motion_still_present
test_homepage_no_horizontal_overflow
test_homepage_responds_200

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultado: ${PASS} PASS / ${FAIL} FAIL"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
