#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-WP-037 — Tests: single.html template + BlogPosting schema
# TDD RED: Todos estos tests deben FALLAR antes de implementar
# ═══════════════════════════════════════════════════════════════════

THEMES_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
MU_PLUGINS="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/mu-plugins"
SINGLE="$THEMES_DIR/templates/single.html"
SCHEMA="$MU_PLUGINS/tma-schema.php"
BASE_URL="https://dev.thormetalart.com"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-WP-037 — Blog Single Template Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ── Scenario 1: single.html file exists ──────────────────────────

echo "▸ Scenario 1: Template file exists"

[[ -f "$SINGLE" ]] \
    && pass "single.html exists" \
    || fail "single.html missing"

# ── Scenario 2: Template has required structural blocks ───────────

echo ""
echo "▸ Scenario 2: Template structure"

if [[ -f "$SINGLE" ]]; then
    grep -q 'slug":"header"' "$SINGLE" \
        && pass "header template-part included" \
        || fail "header template-part missing"

    grep -q 'slug":"footer"' "$SINGLE" \
        && pass "footer template-part included" \
        || fail "footer template-part missing"

    grep -q 'post-title' "$SINGLE" \
        && pass "post-title block present" \
        || fail "post-title block missing"

    grep -q 'post-content' "$SINGLE" \
        && pass "post-content block present" \
        || fail "post-content block missing"

    grep -q 'post-featured-image' "$SINGLE" \
        && pass "post-featured-image block present" \
        || fail "post-featured-image block missing"

    grep -q 'post-author\|Karel\|by ' "$SINGLE" \
        && pass "author byline present" \
        || fail "author byline missing"

    grep -q 'post-date\|post-modified\|tma-post-meta' "$SINGLE" \
        && pass "post date/meta block present" \
        || fail "post date/meta block missing"

    grep -q 'contact\|Get a Free Quote\|Ready for' "$SINGLE" \
        && pass "CTA block present (links to /contact/)" \
        || fail "CTA block missing"

    grep -q 'post-terms\|category' "$SINGLE" \
        && pass "post category/terms block present" \
        || fail "post category/terms block missing"
else
    for _ in $(seq 1 8); do
        fail "skipped — single.html not found"
    done
fi

# ── Scenario 3: BlogPosting schema in tma-schema.php ─────────────

echo ""
echo "▸ Scenario 3: BlogPosting schema in tma-schema.php"

grep -q "BlogPosting\|tma_schema_blog_posting" "$SCHEMA" \
    && pass "BlogPosting schema function exists" \
    || fail "BlogPosting schema function missing"

grep -q "is_single\(\)" "$SCHEMA" \
    && pass "is_single() guard present" \
    || fail "is_single() guard missing"

grep -q "get_the_author\|author.*name\|Karel" "$SCHEMA" \
    && pass "author name in schema" \
    || fail "author name missing from schema"

grep -q "datePublished\|get_the_date\|get_post_time" "$SCHEMA" \
    && pass "datePublished in schema" \
    || fail "datePublished missing from schema"

grep -q "dateModified\|get_the_modified" "$SCHEMA" \
    && pass "dateModified in schema" \
    || fail "dateModified missing from schema"

grep -q "headline\|get_the_title" "$SCHEMA" \
    && pass "headline field in schema" \
    || fail "headline field missing from schema"

# ── Scenario 4: HTTP — blog post URL returns 200 ─────────────────

echo ""
echo "▸ Scenario 4: HTTP check for blog post"

# Use a known seed post URL (WP-039) — REST API requires auth in DEV
FIRST_POST_URL="${BASE_URL}/fabrication/custom-metal-gate-cost-miami/"

if [[ -n "$FIRST_POST_URL" ]]; then
    STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$FIRST_POST_URL" 2>/dev/null || echo "000")
    if [[ "$STATUS" == "200" ]]; then
        pass "Blog post URL returns 200: $FIRST_POST_URL"
    else
        fail "Blog post URL returns $STATUS: $FIRST_POST_URL"
    fi

    # Check BlogPosting schema in head
    HEAD_CONTENT=$(curl -s --max-time 10 "$FIRST_POST_URL" 2>/dev/null || echo "")
    echo "$HEAD_CONTENT" | grep -q "BlogPosting" \
        && pass "BlogPosting schema present in post HTML" \
        || fail "BlogPosting schema missing from post HTML"
else
    fail "Seed post URL is empty — check WP-039 deployment"
    fail "Cannot verify BlogPosting schema in HTML (no post)"
fi

# ── Summary ───────────────────────────────────────────────────────

echo ""
echo "══════════════════════════════════════════════════"
echo " Results: $PASS passed, $FAIL failed"
echo "══════════════════════════════════════════════════"

if [[ $FAIL -gt 0 ]]; then
    exit 1
fi
exit 0
