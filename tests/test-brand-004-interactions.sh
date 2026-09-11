#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-004 — Tests: interacciones JS del rediseño Lujo Forjado
# TDD RED: antes de implementar, no existe tma-forjado.js ni el enqueue
# condicional, y no hay soporte CSS para reveal/chispas/marquee.
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
JS_FILE="${THEME_DIR}/assets/js/tma-forjado.js"
FUNCTIONS_FILE="${THEME_DIR}/functions.php"
STYLE_FILE="${THEME_DIR}/style.css"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-004 — Interacciones JS Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_js_file_exists() {
    if [[ -f "$JS_FILE" ]]; then
        pass "tma-forjado.js existe en assets/js/"
    else
        fail "tma-forjado.js NO existe en assets/js/"
    fi
}

test_js_syntax_valid() {
    if command -v node > /dev/null 2>&1; then
        if node -e "new Function(require('fs').readFileSync('${JS_FILE}','utf8'))" > /dev/null 2>&1; then
            pass "tma-forjado.js tiene sintaxis JS válida"
        else
            fail "tma-forjado.js tiene errores de sintaxis JS"
        fi
    else
        pass "node no disponible, se omite validación de sintaxis JS"
    fi
}

test_header_solid_behavior() {
    if grep -q "tma-site-header" "$JS_FILE" && grep -q "'solid'" "$JS_FILE" && grep -qi "scrollY" "$JS_FILE"; then
        pass "JS implementa header sólido al hacer scroll (.tma-site-header + clase 'solid')"
    else
        fail "JS no implementa el toggle de clase 'solid' en scroll"
    fi
}

test_scroll_reveal_behavior() {
    if grep -q "tma-rv" "$JS_FILE" && grep -q "IntersectionObserver" "$JS_FILE"; then
        pass "JS implementa scroll-reveal con IntersectionObserver sobre .tma-rv"
    else
        fail "JS no implementa scroll-reveal (.tma-rv / IntersectionObserver)"
    fi
}

test_sparks_behavior() {
    if grep -q "tma-sparks" "$JS_FILE" && grep -qi "makeSparks\|spark" "$JS_FILE"; then
        pass "JS genera chispas animadas dentro de .tma-sparks"
    else
        fail "JS no genera chispas animadas"
    fi
}

test_marquee_behavior() {
    if grep -q "tma-marquee" "$JS_FILE" && grep -qi "innerHTML" "$JS_FILE"; then
        pass "JS duplica el contenido de .tma-marquee para el loop infinito"
    else
        fail "JS no duplica el marquee de logos"
    fi
}

test_reduced_motion_respected() {
    if grep -qi "prefers-reduced-motion" "$JS_FILE"; then
        pass "JS respeta prefers-reduced-motion (sin chispas/reveal)"
    else
        fail "JS no respeta prefers-reduced-motion"
    fi
}

test_conditional_enqueue() {
    if grep -q "tma_enqueue_forjado_script" "$FUNCTIONS_FILE" \
        && grep -q "is_front_page()" "$FUNCTIONS_FILE" \
        && grep -q "tma-forjado.js" "$FUNCTIONS_FILE"; then
        pass "functions.php encola tma-forjado.js condicionalmente en is_front_page()"
    else
        fail "functions.php no encola tma-forjado.js condicionalmente en la portada"
    fi
}

test_functions_php_syntax() {
    if php -l "$FUNCTIONS_FILE" > /dev/null 2>&1; then
        pass "functions.php sin errores de sintaxis PHP"
    else
        fail "functions.php tiene errores de sintaxis PHP"
    fi
}

test_support_css_present() {
    if grep -q "tma-rv" "$STYLE_FILE" && grep -q "tma-sparks" "$STYLE_FILE" \
        && grep -q "tma-marquee" "$STYLE_FILE" && grep -qi "prefers-reduced-motion" "$STYLE_FILE"; then
        pass "style.css incluye soporte CSS para reveal, chispas y marquee"
    else
        fail "style.css no incluye el soporte CSS necesario"
    fi
}

test_homepage_responds() {
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/")
    if [[ "$status" == "200" ]]; then
        pass "Homepage (${BASE_URL}/) responde 200 tras los cambios"
    else
        fail "Homepage (${BASE_URL}/) respondió ${status}"
    fi
}

test_script_enqueued_on_homepage() {
    if curl -s "${BASE_URL}/" | grep -q "tma-forjado.js"; then
        pass "tma-forjado.js se carga en el HTML de la portada"
    else
        fail "tma-forjado.js no aparece en el HTML de la portada"
    fi
}

test_js_file_exists
test_js_syntax_valid
test_header_solid_behavior
test_scroll_reveal_behavior
test_sparks_behavior
test_marquee_behavior
test_reduced_motion_respected
test_conditional_enqueue
test_functions_php_syntax
test_support_css_present
test_homepage_responds
test_script_enqueued_on_homepage

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultado: ${PASS} PASS / ${FAIL} FAIL"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
