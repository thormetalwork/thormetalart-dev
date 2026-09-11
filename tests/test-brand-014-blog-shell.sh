#!/usr/bin/env bash
# test-brand-014-blog-shell.sh
# TDD: verifies the blog archive and single templates use the shared Lujo Forjado shell.
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ARCHIVE_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/archive.html"
SINGLE_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/single.html"
PORTFOLIO_TAX_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/taxonomy-tma_project_type.html"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

if grep -q 'tma-page-shell' "$ARCHIVE_TEMPLATE" && grep -q 'tma-page-hero' "$ARCHIVE_TEMPLATE"; then
    pass "archive.html uses the shared page shell"
else
    fail "archive.html is missing the shared page shell"
fi

if grep -q 'tma-page-shell' "$SINGLE_TEMPLATE" && grep -q 'tma-page-hero' "$SINGLE_TEMPLATE"; then
    pass "single.html uses the shared page shell"
else
    fail "single.html is missing the shared page shell"
fi

if grep -q 'tma-page-shell' "$PORTFOLIO_TAX_TEMPLATE" && grep -q 'tma-page-hero' "$PORTFOLIO_TAX_TEMPLATE"; then
    pass "portfolio taxonomy template uses the shared page shell"
else
    fail "portfolio taxonomy template is missing the shared page shell"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
