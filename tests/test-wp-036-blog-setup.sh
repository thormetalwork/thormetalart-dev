#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-WP-036 — Tests: Blog page, categories, WordPress settings
# TDD RED: Todos estos tests deben FALLAR antes de implementar
# ═══════════════════════════════════════════════════════════════════

WP_DIR="/srv/stacks/thormetalart-dev/data/wordpress"
MU_PLUGINS="$WP_DIR/wp-content/mu-plugins"
SERVICE_PAGES="$MU_PLUGINS/tma-service-pages.php"
BASE_URL="https://dev.thormetalart.com"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-WP-036 — Blog Setup Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ── Scenario 1: Blog provisioner exists in tma-service-pages.php ─

echo "▸ Scenario 1: Blog provisioner in tma-service-pages.php"

grep -q "tma_provision_blog" "$SERVICE_PAGES" \
    && pass "tma_provision_blog function exists" \
    || fail "tma_provision_blog function missing"

grep -q "tma_provision_blog_categories" "$SERVICE_PAGES" \
    && pass "tma_provision_blog_categories function exists" \
    || fail "tma_provision_blog_categories function missing"

grep -q "'blog'" "$SERVICE_PAGES" \
    && pass "blog page slug referenced" \
    || fail "blog page slug missing"

# ── Scenario 2: 5 blog categories defined ────────────────────────

echo ""
echo "▸ Scenario 2: 5 blog categories defined in provisioner"

for slug in "fabrication" "design-ideas" "miami-projects" "care-tips" "metal-art"; do
    grep -q "'$slug'" "$SERVICE_PAGES" \
        && pass "Category '$slug' defined" \
        || fail "Category '$slug' missing"
done

# ── Scenario 3: WordPress settings applied via provisioner ────────

echo ""
echo "▸ Scenario 3: WordPress settings configured"

grep -q "page_for_posts" "$SERVICE_PAGES" \
    && pass "page_for_posts option set" \
    || fail "page_for_posts option missing"

grep -q "show_on_front" "$SERVICE_PAGES" \
    && pass "show_on_front option set" \
    || fail "show_on_front option missing"

grep -q "permalink_structure\|/%category%/%postname%/" "$SERVICE_PAGES" \
    && pass "permalink_structure configured" \
    || fail "permalink_structure missing"

# ── Scenario 4: Blog page provisioner is idempotent ──────────────

echo ""
echo "▸ Scenario 4: Blog provision version gating"

grep -q "tma_blog_version\|tma_pages_version.*v3\|blog_v1" "$SERVICE_PAGES" \
    && pass "Blog provisioner has version gate" \
    || fail "Blog provisioner missing version gate (idempotency risk)"

# ── Scenario 5: HTTP check — /blog/ returns 200 ──────────────────

echo ""
echo "▸ Scenario 5: HTTP endpoints"

STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$BASE_URL/blog/" 2>/dev/null || echo "000")
if [[ "$STATUS" == "200" ]]; then
    pass "/blog/ returns 200"
else
    fail "/blog/ returns $STATUS (expected 200)"
fi

# Category pages (only check if blog provisioner has run)
for slug in "fabrication" "design-ideas" "miami-projects" "care-tips" "metal-art"; do
    STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$BASE_URL/category/$slug/" 2>/dev/null || echo "000")
    if [[ "$STATUS" == "200" ]]; then
        pass "/category/$slug/ returns 200"
    else
        fail "/category/$slug/ returns $STATUS (expected 200)"
    fi
done

# ── Scenario 6: Karel Frometa as author in provisioner ───────────

echo ""
echo "▸ Scenario 6: Blog page author set to Karel (ID=3)"

grep -q "post_author.*3\|3.*post_author" "$SERVICE_PAGES" \
    && pass "Karel (ID=3) used as post_author for blog" \
    || fail "Karel (ID=3) not set as post_author in blog provisioner"

# ── Summary ───────────────────────────────────────────────────────

echo ""
echo "══════════════════════════════════════════════════"
echo " Results: $PASS passed, $FAIL failed"
echo "══════════════════════════════════════════════════"

if [[ $FAIL -gt 0 ]]; then
    exit 1
fi
exit 0
