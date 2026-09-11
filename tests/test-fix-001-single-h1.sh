#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-FIX-001 — Tests: Un solo <h1> por página de servicio/contenido
# TDD RED: Antes de implementar, cada página reporta 2x <h1>
# ═══════════════════════════════════════════════════════════════════

BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-FIX-001 — Single H1 Tests (${BASE_URL})"
echo "══════════════════════════════════════════════════"
echo ""

SLUGS=(
    "custom-metal-gates-miami"
    "metal-railings-miami"
    "metal-fences-miami"
    "custom-metal-furniture-miami"
    "metal-stairs-miami"
    "art-commissions"
    "how-we-work"
)

test_single_h1() {
    local slug="$1"
    local count
    count=$(curl -s "${BASE_URL}/${slug}/" | grep -oc '<h1[ >]')
    if [[ "${count}" -eq 1 ]]; then
        pass "/${slug}/ tiene exactamente 1 <h1>"
    else
        fail "/${slug}/ tiene ${count} etiquetas <h1> (esperado 1)"
    fi
}

for slug in "${SLUGS[@]}"; do
    test_single_h1 "${slug}"
done

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultados: ${PASS} passed, ${FAIL} failed"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
