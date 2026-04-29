#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-WP-038 — Tests: archive.html (blog categories + date archive)
# TDD RED: Todos estos tests deben FALLAR antes de implementar
# ═══════════════════════════════════════════════════════════════════

THEMES_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart"
ARCHIVE="$THEMES_DIR/templates/archive.html"
BASE_URL="https://dev.thormetalart.com"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-WP-038 — Blog Archive Template Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ── Scenario 1: archive.html file exists ─────────────────────────

echo "▸ Scenario 1: Template file exists"

[[ -f "$ARCHIVE" ]] \
    && pass "archive.html exists" \
    || fail "archive.html missing"

# ── Scenario 2: Template structure ───────────────────────────────

echo ""
echo "▸ Scenario 2: Template structural blocks"

if [[ -f "$ARCHIVE" ]]; then
    grep -q 'slug":"header"' "$ARCHIVE" \
        && pass "header template-part included" \
        || fail "header template-part missing"

    grep -q 'slug":"footer"' "$ARCHIVE" \
        && pass "footer template-part included" \
        || fail "footer template-part missing"

    grep -q 'term-description\|archive-title\|query-title' "$ARCHIVE" \
        && pass "archive title/description block present" \
        || fail "archive title/description block missing"

    grep -q 'wp:query' "$ARCHIVE" \
        && pass "wp:query block present" \
        || fail "wp:query block missing"

    grep -q 'post-template' "$ARCHIVE" \
        && pass "wp:post-template present inside query" \
        || fail "wp:post-template missing"

    grep -q 'post-featured-image' "$ARCHIVE" \
        && pass "post-featured-image in loop" \
        || fail "post-featured-image missing from loop"

    grep -q 'post-title' "$ARCHIVE" \
        && pass "post-title in loop" \
        || fail "post-title missing from loop"

    grep -q 'post-excerpt\|post-date\|post-terms' "$ARCHIVE" \
        && pass "post meta (excerpt/date/terms) in loop" \
        || fail "post meta missing from loop"

    grep -q 'query-pagination\|query-no-results' "$ARCHIVE" \
        && pass "pagination or no-results block present" \
        || fail "pagination and no-results block missing"

    grep -q 'contact\|Get a Free Quote\|Ready for\|free quote' "$ARCHIVE" \
        && pass "CTA block present" \
        || fail "CTA block missing"
else
    for _ in $(seq 1 9); do
        fail "skipped — archive.html not found"
    done
fi

# ── Scenario 3: HTTP — category archives return 200 ──────────────

echo ""
echo "▸ Scenario 3: HTTP checks for blog category archives"

CATEGORIES=("fabrication" "design-ideas" "miami-projects" "care-tips" "metal-art")
for SLUG in "${CATEGORIES[@]}"; do
    STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 \
        "$BASE_URL/category/$SLUG/" 2>/dev/null || echo "000")
    if [[ "$STATUS" == "200" ]]; then
        pass "Category /$SLUG/ → HTTP 200"
    else
        fail "Category /$SLUG/ → HTTP $STATUS (expected 200)"
    fi
done

# ── Scenario 4: Blog index (/blog/) still returns 200 ────────────

echo ""
echo "▸ Scenario 4: /blog/ index still works"

STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 \
    "$BASE_URL/blog/" 2>/dev/null || echo "000")
[[ "$STATUS" == "200" ]] \
    && pass "/blog/ returns HTTP 200" \
    || fail "/blog/ returns HTTP $STATUS"

# ── Summary ───────────────────────────────────────────────────────

echo ""
echo "══════════════════════════════════════════════════"
echo " Results: $PASS passed, $FAIL failed"
echo "══════════════════════════════════════════════════"

if [[ $FAIL -gt 0 ]]; then
    exit 1
fi
exit 0
