#!/usr/bin/env bash
# test-brand-011-contact-shell.sh
# TDD: verifies the contact page uses the shared Lujo Forjado shell styling.
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CONTACT_TEMPLATE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/templates/page-contact.html"
STYLE_FILE="${ROOT_DIR}/data/wordpress/wp-content/themes/thormetalart/style.css"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

if grep -q 'tma-page-shell' "$CONTACT_TEMPLATE" && grep -q 'tma-page-hero' "$CONTACT_TEMPLATE" && grep -q 'tma-contact-layout' "$CONTACT_TEMPLATE"; then
    pass "page-contact.html uses the shared Lujo Forjado shell"
else
    fail "page-contact.html is missing the shared Lujo Forjado shell"
fi

if grep -q '.tma-contact-layout' "$STYLE_FILE"; then
    pass "style.css keeps the contact layout styling"
else
    fail "style.css is missing contact layout styling"
fi

if grep -q 'Get in Touch' "$CONTACT_TEMPLATE"; then
    pass "contact page uses the approved eyebrow"
else
    fail "contact page is missing the Get in Touch eyebrow"
fi

if grep -q 'font-family: var(--wp--preset--font-family--forjado-body)' "$STYLE_FILE"; then
    pass "contact controls use the Forjado body font"
else
    fail "contact controls do not use the Forjado body font"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
