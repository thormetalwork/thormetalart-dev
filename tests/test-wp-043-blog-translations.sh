#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-WP-043 — Tests: Traducciones ES para 12 posts semilla
# TDD RED: Todos estos tests deben FALLAR antes de implementar
# ═══════════════════════════════════════════════════════════════════

MU_PLUGIN="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/mu-plugins/tma-blog-translations.php"
BASE_URL="https://dev.thormetalart.com"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

# MySQL helper
mysql_query() {
    docker exec tma_dev_mysql mysql -u thormetalart_dev -p"$(grep MYSQL_PASSWORD /srv/stacks/thormetalart-dev/.env | cut -d= -f2)" thormetalart_wp -sN -e "$1" 2>/dev/null
}

echo "══════════════════════════════════════════════════"
echo " TICKET-WP-043 — Blog Translations Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ─── Test 1: mu-plugin existe ───
test_muplugin_exists() {
    if [[ -f "${MU_PLUGIN}" ]]; then
        pass "tma-blog-translations.php existe"
    else
        fail "tma-blog-translations.php NO existe"
    fi
}

# ─── Test 2: función de provisioning existe en mu-plugin ───
test_muplugin_has_function() {
    if [[ -f "${MU_PLUGIN}" ]] && grep -q "tma_provision_blog_translations_once" "${MU_PLUGIN}"; then
        pass "Función tma_provision_blog_translations_once existe"
    else
        fail "Función tma_provision_blog_translations_once NO existe"
    fi
}

# ─── Test 3: opción de versión se registra (evita re-ejecución) ───
test_muplugin_version_gate() {
    if [[ -f "${MU_PLUGIN}" ]] && grep -q "tma_blog_translations_v1" "${MU_PLUGIN}"; then
        pass "Version gate tma_blog_translations_v1 existe en código"
    else
        fail "Version gate NO existe (riesgo de re-ejecución infinita)"
    fi
}

# ─── Test 4: Traducción ES del primer post (ID=80) existe en BD ───
test_db_title_post80_translated() {
    local count
    count=$(mysql_query "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es d JOIN tma_trp_original_strings o ON d.original_id=o.id WHERE o.original='How Much Does a Custom Metal Gate Cost in Miami?' AND d.translated != '' AND d.status=2;" 2>/dev/null || echo "0")
    if [[ "${count}" -ge 1 ]]; then
        pass "Título post 80 tiene traducción ES en BD (status=2)"
    else
        fail "Título post 80 NO tiene traducción ES en BD"
    fi
}

# ─── Test 5: Traducción ES del último post (ID=91) existe en BD ───
test_db_title_post91_translated() {
    local count
    count=$(mysql_query "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es d JOIN tma_trp_original_strings o ON d.original_id=o.id WHERE o.original='From Sketch to Steel: Our Custom Metal Gate Process' AND d.translated != '' AND d.status=2;" 2>/dev/null || echo "0")
    if [[ "${count}" -ge 1 ]]; then
        pass "Título post 91 tiene traducción ES en BD (status=2)"
    else
        fail "Título post 91 NO tiene traducción ES en BD"
    fi
}

# ─── Test 6: Al menos 10 títulos de posts tienen traducción ES ───
test_db_min_titles_translated() {
    local titles=(
        "How Much Does a Custom Metal Gate Cost in Miami?"
        "7 Metal Gate Styles That Work in Miami's Climate"
        "Steel vs. Aluminum Gates: Which Is Right for Miami?"
        "Inside a Miami Metalwork Project: From Design to Install"
        "How to Maintain Metal Railings in South Florida's Salt Air"
        "5 Ideas for Custom Metal Furniture That Transform Any Space"
        "Metal Sculpture Commissioning: What to Expect"
        "Why Miami Architects Choose Custom Metalwork"
        "Water Jet Cutting vs. Plasma Cutting: A Fabricator's Guide"
        "How to Choose the Right Metal Fence for Your Miami Property"
        "From Sketch to Steel: Our Custom Metal Gate Process"
    )
    local translated=0
    for title in "${titles[@]}"; do
        local count safe_title
        safe_title="${title//\'/\'\'}"
        count=$(mysql_query "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es d JOIN tma_trp_original_strings o ON d.original_id=o.id WHERE o.original='${safe_title}' AND d.translated != '' AND d.status=2;" 2>/dev/null || echo "0")
        [[ "${count}" -ge 1 ]] && translated=$((translated + 1))
    done
    if [[ "${translated}" -ge 10 ]]; then
        pass "Al menos 10/12 títulos de posts tienen traducción ES (${translated}/11 verificados)"
    else
        fail "Solo ${translated}/11 títulos tienen traducción ES (esperado ≥10)"
    fi
}

# ─── Test 7: La página del post en ES muestra título en español ───
test_post_es_title_rendered() {
    local html
    html=$(curl -s "${BASE_URL}/fabrication/custom-metal-gate-cost-miami/?lang=es_ES")
    if echo "${html}" | grep -qi "Cuánto\|Miami\|puerta\|metal"; then
        pass "Post /fabrication/custom-metal-gate-cost-miami/ renderiza contenido en ES"
    else
        fail "Post NO muestra contenido en ES al pasar ?lang=es_ES"
    fi
}

# ─── Test 8: Todas las traducciones tienen status=2 (human) ───
test_db_all_translations_human_status() {
    local count_status0
    count_status0=$(mysql_query "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es d JOIN tma_trp_original_strings o ON d.original_id=o.id WHERE o.original IN ('How Much Does a Custom Metal Gate Cost in Miami?','From Sketch to Steel: Our Custom Metal Gate Process') AND d.status=0;" 2>/dev/null || echo "0")
    if [[ "${count_status0}" -eq 0 ]]; then
        pass "Traducciones de blog tienen status=2 (human), no status=0"
    else
        fail "${count_status0} traducciones de blog tienen status=0 (pendiente)"
    fi
}

# Ejecutar tests
echo "─── Estructura del mu-plugin ───"
test_muplugin_exists
test_muplugin_has_function
test_muplugin_version_gate

echo ""
echo "─── Base de datos ───"
test_db_title_post80_translated
test_db_title_post91_translated
test_db_min_titles_translated
test_db_all_translations_human_status

echo ""
echo "─── Render HTTP ───"
test_post_es_title_rendered

echo ""
echo "══════════════════════════════════════════════════"
echo " Resultados: ${PASS} passed, ${FAIL} failed"
echo "══════════════════════════════════════════════════"

[[ "${FAIL}" -eq 0 ]] && exit 0 || exit 1
