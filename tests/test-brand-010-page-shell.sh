#!/usr/bin/env bash
# test-brand-010-page-shell.sh
# TDD: verifies the shared internal page shell uses the Lujo Forjado visual treatment.
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PAGE_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/page.html"
STYLE_FILE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/style.css"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

if grep -q 'tma-page-shell' "$PAGE_TEMPLATE" && grep -q 'tma-page-hero' "$PAGE_TEMPLATE" && grep -q 'tma-page-content' "$PAGE_TEMPLATE"; then
    pass "page.html includes the new Lujo Forjado shell classes"
else
    fail "page.html is missing the new Lujo Forjado shell classes"
fi

if grep -q '.tma-page-hero' "$STYLE_FILE" && grep -q '.tma-page-content' "$STYLE_FILE"; then
    pass "style.css defines the page shell visual styles"
else
    fail "style.css is missing the page shell visual styles"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
