#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-BRAND-006 — Tests: extensión del sistema de marca "Lujo
# Forjado" al resto de plantillas (page/archive/single/portfolio).
# ═══════════════════════════════════════════════════════════════════

THEME_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
MU_PLUGINS_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/mu-plugins"
BASE_URL="${TMA_BASE_URL:-https://dev.thormetalart.com}"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-BRAND-006 — Brand System Extension Tests"
echo "══════════════════════════════════════════════════"
echo ""

test_theme_json_heading_is_forjado() {
    if grep -q "'Archivo Expanded', Arial, sans-serif" "${THEME_DIR}/theme.json" && \
       grep -A2 '"slug": "heading"' "${THEME_DIR}/theme.json" | grep -q "Archivo Expanded"; then
        pass "theme.json: la fuente 'heading' apunta a Archivo Expanded (Lujo Forjado)"
    else
        fail "theme.json: la fuente 'heading' no apunta a Archivo Expanded"
    fi
}

test_theme_json_valid() {
    if python3 -c "import json; json.load(open('${THEME_DIR}/theme.json'))" 2>/dev/null; then
        pass "theme.json sigue siendo JSON válido"
    else
        fail "theme.json tiene un error de sintaxis JSON"
    fi
}

test_no_loose_hex_in_archive_single() {
    local hex_found=0
    for f in archive.html single.html; do
        if grep -qE '#[0-9a-fA-F]{3,6}' "${THEME_DIR}/templates/${f}"; then
            hex_found=1
            fail "templates/${f} todavía contiene colores hex sueltos"
        fi
    done
    [[ "$hex_found" -eq 0 ]] && pass "archive.html y single.html no contienen colores hex sueltos (usan var(--wp--preset--color--*))"
}

test_archive_single_use_forjado_palette() {
    if grep -q "wp--preset--color--obsidian" "${THEME_DIR}/templates/archive.html" && \
       grep -q "wp--preset--color--forge" "${THEME_DIR}/templates/archive.html" && \
       grep -q "wp--preset--color--obsidian" "${THEME_DIR}/templates/single.html"; then
        pass "archive.html y single.html referencian la paleta obsidian/forge de Lujo Forjado"
    else
        fail "archive.html/single.html no referencian la paleta Lujo Forjado"
    fi
}

test_service_pages_use_obsidian_overlay() {
    if grep -q '"overlayColor":"obsidian"' "${MU_PLUGINS_DIR}/tma-service-pages.php" && \
       ! grep -q '"overlayColor":"primary"' "${MU_PLUGINS_DIR}/tma-service-pages.php"; then
        pass "tma-service-pages.php usa overlayColor obsidian en los hero covers (sin 'primary' legado)"
    else
        fail "tma-service-pages.php todavía usa el overlay 'primary' legado"
    fi
}

test_service_pages_php_syntax() {
    if php -l "${MU_PLUGINS_DIR}/tma-service-pages.php" > /dev/null 2>&1; then
        pass "tma-service-pages.php sin errores de sintaxis PHP"
    else
        fail "tma-service-pages.php tiene errores de sintaxis PHP"
    fi
}

test_portfolio_templates_no_loose_hex() {
    local hex_found=0
    for f in archive-tma_portfolio.html single-tma_portfolio.html taxonomy-tma_project_type.html; do
        if grep -qE '#[0-9a-fA-F]{3,6}' "${THEME_DIR}/templates/${f}"; then
            hex_found=1
            fail "templates/${f} contiene colores hex sueltos"
        fi
    done
    [[ "$hex_found" -eq 0 ]] && pass "Plantillas de portfolio (archive/single/taxonomy) no contienen colores hex sueltos"
}

test_service_page_renders_forjado_heading() {
    local html
    html=$(curl -s "${BASE_URL}/custom-metal-gates-miami/")
    if echo "$html" | grep -q "Custom Metal Gates Miami" && [[ -n "$html" ]]; then
        pass "Página de servicio (custom-metal-gates-miami) responde con el heading esperado"
    else
        fail "Página de servicio no respondió con el contenido esperado"
    fi
}

test_service_page_uses_obsidian_in_db() {
    local status
    status=$(docker exec -u www-data tma_dev_wordpress php -r '
        define("WP_USE_THEMES", false);
        require "/var/www/html/wp-load.php";
        $p = get_page_by_path("custom-metal-gates-miami");
        echo $p && strpos($p->post_content, "obsidian") !== false ? "OK" : "MISSING";
    ' 2>/dev/null)
    if [[ "$status" == "OK" ]]; then
        pass "La página generada 'custom-metal-gates-miami' fue re-provisionada con el overlay obsidian"
    else
        fail "La página generada no refleja el overlay obsidian (status=${status})"
    fi
}

test_blog_archive_renders_200() {
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/blog/")
    if [[ "$status" == "200" ]]; then
        pass "Blog archive (${BASE_URL}/blog/) responde 200"
    else
        fail "Blog archive respondió ${status}"
    fi
}

test_theme_json_heading_is_forjado
test_theme_json_valid
test_no_loose_hex_in_archive_single
test_archive_single_use_forjado_palette
test_service_pages_use_obsidian_overlay
test_service_pages_php_syntax
test_portfolio_templates_no_loose_hex
test_service_page_renders_forjado_heading
test_service_page_uses_obsidian_in_db
test_blog_archive_renders_200

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultado: ${PASS} PASS / ${FAIL} FAIL"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
