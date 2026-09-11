#!/usr/bin/env bash
# test-wp-040-blog-sidebar.sh
# Tests for TICKET-WP-040: Blog sidebar (recent posts, categories, CTA)
# TDD: RED state — all tests should FAIL before implementation
set -euo pipefail

PASS=0
FAIL=0
BASE_URL="${BASE_URL:-https://dev.thormetalart.com}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="${SCRIPT_DIR}/../data/wordpress/wp-content/themes/thormetalart"
SIDEBAR_FILE="${THEME_DIR}/parts/sidebar-blog.html"
SINGLE_FILE="${THEME_DIR}/templates/single.html"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }
info() { echo "[INFO] TEST: $1"; }

# ── 1. Template part file exists ──────────────────────────────────
info "sidebar-blog.html template part exists"
if [[ -f "$SIDEBAR_FILE" ]]; then
    pass "sidebar-blog.html exists at ${SIDEBAR_FILE}"
else
    fail "sidebar-blog.html NOT found at ${SIDEBAR_FILE}"
fi

# ── 2. Sidebar contains wp:latest-posts block ─────────────────────
info "sidebar contains wp:latest-posts"
if [[ -f "$SIDEBAR_FILE" ]] && grep -q 'wp:latest-posts' "$SIDEBAR_FILE"; then
    pass "sidebar-blog.html contains wp:latest-posts"
else
    fail "sidebar-blog.html missing wp:latest-posts"
fi

# ── 3. Sidebar shows at least 5 recent posts ─────────────────────
info "sidebar recent posts: postsToShow >= 5"
if [[ -f "$SIDEBAR_FILE" ]] && grep -qE '"postsToShow":[5-9]|"postsToShow":1[0-9]' "$SIDEBAR_FILE"; then
    pass "wp:latest-posts shows ≥5 posts"
else
    fail "wp:latest-posts postsToShow not set to ≥5"
fi

# ── 4. Sidebar contains wp:categories block ───────────────────────
info "sidebar contains wp:categories"
if [[ -f "$SIDEBAR_FILE" ]] && grep -q 'wp:categories' "$SIDEBAR_FILE"; then
    pass "sidebar-blog.html contains wp:categories"
else
    fail "sidebar-blog.html missing wp:categories"
fi

# ── 5. Categories show post counts ────────────────────────────────
info "sidebar categories show post counts"
if [[ -f "$SIDEBAR_FILE" ]] && grep -q '"showPostCounts":true' "$SIDEBAR_FILE"; then
    pass "wp:categories has showPostCounts:true"
else
    fail "wp:categories missing showPostCounts:true"
fi

# ── 6. Sidebar contains CTA button to /contact/ ───────────────────
info "sidebar contains CTA link to /contact/"
if [[ -f "$SIDEBAR_FILE" ]] && grep -q '/contact/' "$SIDEBAR_FILE"; then
    pass "sidebar-blog.html contains CTA link to /contact/"
else
    fail "sidebar-blog.html missing CTA link to /contact/"
fi

# ── 7. Sidebar CTA has tma-blog-sidebar class ─────────────────────
info "sidebar root group has tma-blog-sidebar class"
if [[ -f "$SIDEBAR_FILE" ]] && grep -q 'tma-blog-sidebar' "$SIDEBAR_FILE"; then
    pass "sidebar has tma-blog-sidebar class"
else
    fail "sidebar missing tma-blog-sidebar class"
fi

# ── 8. single.html uses 2-column layout (main + sidebar) ─────────
info "single.html uses wp:columns layout"
if grep -q 'wp:columns' "$SINGLE_FILE" 2>/dev/null; then
    pass "single.html has wp:columns block"
else
    fail "single.html missing wp:columns block"
fi

# ── 9. single.html references sidebar-blog template-part ─────────
info "single.html references sidebar-blog part"
if grep -q 'sidebar-blog' "$SINGLE_FILE" 2>/dev/null; then
    pass "single.html references sidebar-blog template-part"
else
    fail "single.html missing sidebar-blog reference"
fi

# ── 10. sidebar-blog.html uses Cormorant Garamond for headings ────
info "sidebar headings use Cormorant Garamond font"
if [[ -f "$SIDEBAR_FILE" ]] && grep -q 'cormorant-garamond\|Cormorant Garamond' "$SIDEBAR_FILE"; then
    pass "sidebar uses Cormorant Garamond for headings"
else
    fail "sidebar missing Cormorant Garamond font"
fi

# ── 11. sidebar-blog.html uses accent color for heading border ────
info "sidebar uses accent color #B8860B"
if [[ -f "$SIDEBAR_FILE" ]] && grep -qi 'B8860B\|b8860b' "$SIDEBAR_FILE"; then
    pass "sidebar uses accent color #B8860B"
else
    fail "sidebar missing accent color #B8860B"
fi

# ── 12. sidebar has dark CTA block (bg #1A1A1A) ───────────────────
info "sidebar CTA has dark background #1A1A1A"
if [[ -f "$SIDEBAR_FILE" ]] && grep -qi '1A1A1A\|1a1a1a' "$SIDEBAR_FILE"; then
    pass "sidebar CTA has dark background #1A1A1A"
else
    fail "sidebar CTA missing dark background #1A1A1A"
fi

# ── 13. HTTP — single post returns 200 ───────────────────────────
info "HTTP — single post page returns 200"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/fabrication/custom-metal-gate-cost-miami/" 2>/dev/null)
if [[ "$HTTP_CODE" == "200" ]]; then
    pass "Single post HTTP 200 — /fabrication/custom-metal-gate-cost-miami/"
else
    fail "Single post HTTP ${HTTP_CODE} — expected 200"
fi

# ── 14. HTTP — rendered page contains tma-blog-sidebar ───────────
info "HTTP — rendered post page contains sidebar class"
BODY=$(curl -sL "${BASE_URL}/fabrication/custom-metal-gate-cost-miami/" 2>/dev/null)
if echo "$BODY" | grep -q 'tma-blog-sidebar'; then
    pass "Rendered post contains tma-blog-sidebar class"
else
    fail "Rendered post missing tma-blog-sidebar class"
fi

# ── 15. HTTP — archive page still works ──────────────────────────
info "HTTP — /fabrication/ archive returns 200"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}/fabrication/" 2>/dev/null)
if [[ "$HTTP_CODE" == "200" ]]; then
    pass "/fabrication/ archive HTTP 200"
else
    fail "/fabrication/ archive HTTP ${HTTP_CODE} — expected 200"
fi

# ── Summary ──────────────────────────────────────────────────────
echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
