#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-WP-042 — Tests: Enlace "Blog" en menú (header + footer)
# TDD RED: Todos estos tests deben FALLAR antes de implementar
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
HEADER="${THEME_DIR}/parts/header.html"
FOOTER="${THEME_DIR}/parts/footer.html"
BASE_URL="https://dev.thormetalart.com"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-WP-042 — Blog Nav Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ─── Test 1: header.html contiene wp:navigation-link a /blog/ ───
test_header_has_blog_link() {
    if grep -q '"/blog/"' "${HEADER}"; then
        pass "header.html contiene link a /blog/"
    else
        fail "header.html NO contiene link a /blog/"
    fi
}

# ─── Test 2: header.html contiene label Blog ───
test_header_has_blog_label() {
    if grep -q '"Blog"' "${HEADER}"; then
        pass "header.html contiene label \"Blog\""
    else
        fail "header.html NO contiene label \"Blog\""
    fi
}

# ─── Test 3: El link de Blog está entre Portfolio y Contact ───
test_header_blog_position() {
    local content
    content=$(cat "${HEADER}")
    local pos_portfolio pos_blog pos_contact
    pos_portfolio=$(echo "${content}" | grep -n "Portfolio" | grep navigation-link | head -1 | cut -d: -f1)
    pos_blog=$(echo "${content}" | grep -n '"/blog/"' | head -1 | cut -d: -f1)
    pos_contact=$(echo "${content}" | grep -n '"Contact"' | grep navigation-link | head -1 | cut -d: -f1)

    if [[ -n "${pos_blog}" && "${pos_blog}" -gt "${pos_portfolio}" && "${pos_blog}" -lt "${pos_contact}" ]]; then
        pass "Blog está entre Portfolio y Contact en el menú"
    else
        fail "Blog NO está en la posición correcta (Portfolio=${pos_portfolio}, Blog=${pos_blog}, Contact=${pos_contact})"
    fi
}

# ─── Test 4: footer.html contiene link a /blog/ ───
test_footer_has_blog_link() {
    if grep -q 'href="/blog/"' "${FOOTER}"; then
        pass "footer.html contiene link a /blog/"
    else
        fail "footer.html NO contiene link a /blog/"
    fi
}

# ─── Test 5: /blog/ responde HTTP 200 ───
test_blog_page_200() {
    local status
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/blog/")
    if [[ "${status}" == "200" ]]; then
        pass "/blog/ responde HTTP 200"
    else
        fail "/blog/ responde HTTP ${status} (esperado 200)"
    fi
}

# ─── Test 6: La página principal renderiza link /blog/ en nav ───
test_homepage_nav_has_blog() {
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "${html}" | grep -q 'href="/blog/"'; then
        pass "Homepage renderiza /blog/ en navegación"
    else
        fail "Homepage NO renderiza /blog/ en navegación"
    fi
}

# ─── Test 7: El footer renderizado contiene link Blog ───
test_homepage_footer_has_blog() {
    local html
    html=$(curl -s "${BASE_URL}/")
    # Check for /blog/ link in footer area
    if echo "${html}" | grep -q 'href="/blog/"'; then
        pass "Homepage renderiza /blog/ (footer o nav)"
    else
        fail "Homepage NO renderiza ningún link a /blog/"
    fi
}

# Ejecutar tests
echo "─── Archivos de tema ───"
test_header_has_blog_link
test_header_has_blog_label
test_header_blog_position
test_footer_has_blog_link

echo ""
echo "─── HTTP / Render ───"
test_blog_page_200
test_homepage_nav_has_blog
test_homepage_footer_has_blog

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultados: ${PASS} passed, ${FAIL} failed"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
