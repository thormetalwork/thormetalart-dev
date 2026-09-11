#!/usr/bin/env bash
# test-brand-011-service-pages.sh
# TDD: verifies the generated service and core pages include the shared Lujo Forjado shell content.
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_FILE="${ROOT_DIR}/data/wordpress/wp-content/mu-plugins/tma-service-pages.php"
TEMPLATE_FILE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/page-service.html"
SALES_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/page-sales.html"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

if grep -q 'tma-page-shell' "$TEMPLATE_FILE" && grep -q 'tma_service_hero' "$TEMPLATE_FILE"; then
    pass "dedicated service template owns the page shell structure"
else
    fail "dedicated service template is missing the page shell structure"
fi

if grep -q 'tma-forjado-cta' "$PLUGIN_FILE" && ! grep -q 'tma-final-cta' "$PLUGIN_FILE"; then
    pass "service page content uses the Lujo Forjado CTA"
else
    fail "service page content still uses the legacy CTA"
fi

if [[ -f "$SALES_TEMPLATE" ]] && grep -q 'tma_sales_hero' "$SALES_TEMPLATE" && grep -q 'wp:post-content' "$SALES_TEMPLATE"; then
    pass "sales template owns one dynamic hero and the page content"
else
    fail "sales pages do not have a dedicated hero/content template"
fi

if grep -q 'function tma_shortcode_sales_hero' "$PLUGIN_FILE" && grep -q "'art-commissions'" "$PLUGIN_FILE" && grep -q "/wp-content/uploads/2026/04/tma-portfolio-fenix-sculpture.jpg" "$PLUGIN_FILE"; then
    pass "art commissions has a Forjado sales hero"
else
    fail "art commissions is missing its Forjado sales hero"
fi

if grep -q 'thormetalart/process-forjado' "$PLUGIN_FILE" && grep -q 'thormetalart/cta-forjado' "$PLUGIN_FILE"; then
    pass "sales content reuses process and CTA patterns"
else
    fail "sales content does not reuse process and CTA patterns"
fi

if ! grep -q 'function tma_page_shell_markup' "$PLUGIN_FILE"; then
    pass "generated content no longer embeds a duplicate page shell"
else
    fail "legacy generated page shell is still present"
fi

if grep -q 'function tma_get_legacy_generated_content_hashes' "$PLUGIN_FILE" && \
   grep -q 'isset(\$legacy_hashes\[\$slug\]) && hash_equals(\$legacy_hashes\[\$slug\], \$current_hash)' "$PLUGIN_FILE" && \
   grep -q "'' !== \$stored_hash && hash_equals(\$stored_hash, \$current_hash)" "$PLUGIN_FILE" && \
   ! grep -q 'is_legacy_sales_content' "$PLUGIN_FILE"; then
    pass "generated shells migrate only with stored or allowlisted legacy hashes"
else
    fail "generated sales migration can overwrite content without a matching hash"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
