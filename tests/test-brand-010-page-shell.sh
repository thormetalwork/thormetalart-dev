#!/usr/bin/env bash
# test-brand-010-page-shell.sh
# TDD: verifies the shared internal page shell uses the Lujo Forjado visual treatment.
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PAGE_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/page.html"
PAGE_PATTERN="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/patterns/page-banner-forjado.php"
STYLE_FILE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/style.css"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

if [[ -f "$PAGE_PATTERN" ]] && grep -q 'thormetalart/page-banner-forjado' "$PAGE_TEMPLATE"; then
    pass "page.html consumes the reusable Lujo Forjado banner"
else
    fail "page.html does not consume a reusable Lujo Forjado banner"
fi

if [[ -f "$PAGE_PATTERN" ]] && grep -q 'tma-page-kicker' "$PAGE_PATTERN" && grep -q 'wp:post-title' "$PAGE_PATTERN" && grep -q 'tma_breadcrumbs' "$PAGE_PATTERN"; then
    pass "banner contains eyebrow, dynamic H1 and breadcrumbs"
else
    fail "banner is missing eyebrow, dynamic H1 or breadcrumbs"
fi

if grep -q 'background: var(--wp--preset--color--obsidian)' "$STYLE_FILE" && grep -q 'background: var(--wp--preset--color--paper)' "$STYLE_FILE"; then
    pass "page shell uses Lujo Forjado color tokens"
else
    fail "page shell still relies on legacy literal backgrounds"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
