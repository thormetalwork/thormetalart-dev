#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-008 — QA visual, accesibilidad y performance e2e
# del rediseño "Lujo Forjado".
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-008 — QA / A11y / Performance Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ─── H1 único por página ─────────────────────────────────────────
test_single_h1_all_pages() {
    local failed=0
    local pages=(
        "/"
        "/custom-metal-gates-miami/"
        "/metal-railings-miami/"
        "/portfolio/"
        "/blog/"
        "/how-we-work/"
        "/contact/"
    )
    for path in "${pages[@]}"; do
        count=$(curl -s "${BASE_URL}${path}" | grep -o '<h1' | wc -l)
        if [[ "$count" -ne 1 ]]; then
            fail "H1 count=${count} en ${BASE_URL}${path} (esperado 1)"
            failed=1
        fi
    done
    [[ "$failed" -eq 0 ]] && pass "Un solo <h1> en todas las páginas auditadas (7/7)"
}

# ─── Contraste WCAG AA ───────────────────────────────────────────
test_wcag_aa_contrast() {
    result=$(python3 - <<'PYEOF'
def relative_luminance(rgb):
    r, g, b = [x/255 for x in rgb]
    def lin(c): return c/12.92 if c<=0.04045 else ((c+0.055)/1.055)**2.4
    return 0.2126*lin(r)+0.7152*lin(g)+0.0722*lin(b)

def cr(c1,c2):
    l1,l2=sorted([relative_luminance(c1),relative_luminance(c2)],reverse=True)
    return (l1+0.05)/(l2+0.05)

# texto normal (≥4.5:1) — pares de colores activos tras el fix BRAND-008
pairs=[
    ((0xF0,0x87,0x3A),(0x0C,0x0E,0x12)),  # ember on obsidian (eyebrow)
    ((0x9A,0xA1,0xA9),(0x0C,0x0E,0x12)),  # ash on obsidian
    ((0xF4,0xF1,0xEC),(0x0C,0x0E,0x12)),  # titanium on obsidian
    ((0x56,0x5B,0x62),(0xF7,0xF5,0xF1)),  # ink-mute on paper
    ((0x16,0x18,0x1C),(0xF7,0xF5,0xF1)),  # ink on paper
]
fails=[cr(a,b) for a,b in pairs if cr(a,b)<4.5]
print("PASS" if not fails else f"FAIL:{fails}")
PYEOF
)
    if [[ "$result" == "PASS" ]]; then
        pass "Contraste WCAG AA (≥4.5:1): ember/ash/titanium/ink-mute/ink cumplen todos"
    else
        fail "Algún par de colores no cumple WCAG AA: ${result}"
    fi
}

# ─── prefers-reduced-motion en style.css ──────────────────────────
test_reduced_motion() {
    if grep -qF '@media (prefers-reduced-motion: reduce)' "${THEME_DIR}/style.css"; then
        pass "style.css respeta @media (prefers-reduced-motion: reduce)"
    else
        fail "style.css no tiene @media (prefers-reduced-motion: reduce)"
    fi
}

# ─── Sin errores PHP en el HTML renderizado ───────────────────────
test_no_php_errors_in_homepage() {
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "$html" | grep -qiE "Fatal error|Parse error|Warning.*in.*on line"; then
        fail "El homepage renderizado contiene errores PHP"
    else
        pass "Homepage renderizado sin errores PHP (Fatal/Parse/Warning)"
    fi
}

# ─── Bilingüe /es/ ───────────────────────────────────────────────
test_es_homepage_responds() {
    local status
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/es/")
    if [[ "$status" == "200" ]]; then
        pass "Homepage en español (${BASE_URL}/es/) responde 200"
    else
        fail "Homepage en español respondió ${status}"
    fi
}

# ─── Viewport meta para móvil ────────────────────────────────────
test_viewport_meta() {
    local html
    html=$(curl -s "${BASE_URL}/")
    if echo "$html" | grep -q 'name="viewport"'; then
        pass "La página incluye meta viewport (requerido para responsive)"
    else
        fail "La página NO incluye meta viewport"
    fi
}

# ─── Eyebrows NO usan forge en fondos oscuros ─────────────────────
test_no_forge_eyebrow_on_dark() {
    # forge sobre obsidian solo cumple WCAG AA-Large (3.95:1), no AA normal
    local found=0
    for f in "${THEME_DIR}/patterns/hero-forjado.php" "${THEME_DIR}/patterns/cta-forjado.php" "${THEME_DIR}/patterns/quote-band.php"; do
        if grep -q 'fontSize":"11px".*forge\|forge.*fontSize":"11px"' "$f" 2>/dev/null; then
            found=1
        fi
        # Busca también en líneas adyacentes (sed two-line context)
        if sed -n '/fontSize":"11px"/{N;p}' "$f" | grep -q '"textColor":"forge"'; then
            found=1
        fi
    done
    if [[ "$found" -eq 0 ]]; then
        pass "Los eyebrows de 11px en secciones oscuras usan ember (≥4.5:1), no forge (3.95:1)"
    else
        fail "Encontrado textColor:forge en eyebrow de 11px sobre fondo oscuro — no cumple WCAG AA normal"
    fi
}

# ─── PHP syntax en todos los patrones ────────────────────────────
test_patterns_php_syntax() {
    local failed=0
    for f in "${THEME_DIR}/patterns/"*.php; do
        if ! php -l "$f" > /dev/null 2>&1; then
            fail "Error de sintaxis PHP en $(basename $f)"
            failed=1
        fi
    done
    [[ "$failed" -eq 0 ]] && pass "Todos los patrones .php pasan php -l sin errores"
}

# ─── functions.php y theme.json válidos ──────────────────────────
test_functions_php_syntax() {
    if php -l "${THEME_DIR}/functions.php" > /dev/null 2>&1; then
        pass "functions.php sin errores de sintaxis PHP"
    else
        fail "functions.php tiene errores de sintaxis PHP"
    fi
}

test_theme_json_valid() {
    if python3 -c "import json; json.load(open('${THEME_DIR}/theme.json'))" 2>/dev/null; then
        pass "theme.json es JSON válido"
    else
        fail "theme.json tiene un error de sintaxis JSON"
    fi
}

# ─── Homepage usa los 8 patrones ─────────────────────────────────
test_all_8_patterns_render() {
    local html
    html=$(curl -s "${BASE_URL}/")
    local classes=("tma-forjado-hero" "tma-forjado-disciplines" "tma-forjado-selected-work" "tma-forjado-atelier" "tma-forjado-quote-band" "tma-forjado-process" "tma-forjado-cta" "tma-forjado-client-logos")
    local failed=0
    for cls in "${classes[@]}"; do
        if ! echo "$html" | grep -qF "$cls"; then
            fail "Sección '${cls}' no encontrada en el HTML renderizado"
            failed=1
        fi
    done
    [[ "$failed" -eq 0 ]] && pass "Las 8 secciones Lujo Forjado renderizan en el homepage"
}

test_single_h1_all_pages
test_wcag_aa_contrast
test_reduced_motion
test_no_php_errors_in_homepage
test_es_homepage_responds
test_viewport_meta
test_no_forge_eyebrow_on_dark
test_patterns_php_syntax
test_functions_php_syntax
test_theme_json_valid
test_all_8_patterns_render

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultado: ${PASS} PASS / ${FAIL} FAIL"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
