#!/usr/bin/env bash
# test-brand-015-home-shell.sh
# Verifies template structure:
#   home.html  → blog posts index (wp:query with tma-blog-card)
#   index.html → ultimate fallback (Lujo Forjado patterns)
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
HOME_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/home.html"
INDEX_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/index.html"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

# home.html must be the blog posts-page template (query loop, not LP patterns)
if grep -q 'wp:query' "$HOME_TEMPLATE" && grep -q 'tma-blog-card' "$HOME_TEMPLATE"; then
    pass "home.html is the blog posts-page template (wp:query + tma-blog-card)"
else
    fail "home.html is missing the blog query loop (wp:query / tma-blog-card)"
fi

if grep -q 'thormetalart/hero-forjado' "$INDEX_TEMPLATE" && grep -q 'thormetalart/client-logos' "$INDEX_TEMPLATE"; then
    pass "index.html uses the Lujo Forjado patterns"
else
    fail "index.html is missing the Lujo Forjado pattern blocks"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
