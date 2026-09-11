#!/usr/bin/env bash
# test-brand-011-service-pages.sh
# TDD: verifies the generated service and core pages include the shared Lujo Forjado shell content.
set -euo pipefail

PASS=0
FAIL=0
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_FILE="${ROOT_DIR}/data/wordpress/wp-content/mu-plugins/tma-service-pages.php"

pass() { echo "[PASS] $1"; PASS=$((PASS+1)); }
fail() { echo "[FAIL] $1"; FAIL=$((FAIL+1)); }

if grep -q 'tma-page-shell' "$PLUGIN_FILE" && grep -q 'tma-page-hero' "$PLUGIN_FILE"; then
    pass "mu-plugin service page content includes the page shell structure"
else
    fail "mu-plugin service page content is missing the page shell structure"
fi

if grep -q 'tma-final-cta' "$PLUGIN_FILE"; then
    pass "service page content keeps the final CTA block"
else
    fail "service page content is missing the final CTA block"
fi

if python3 - "$PLUGIN_FILE" <<'PY'
import sys
from pathlib import Path
text = Path(sys.argv[1]).read_text()
if 'function tma_page_shell_markup' in text and "'Metal as Art'" in text and "/wp-content/uploads/2026/04/tma-portfolio-fenix-sculpture.jpg" in text:
    print("[PASS] art-commissions core page keeps the hero cover and heading")
    raise SystemExit(0)
print("[FAIL] art-commissions core page is missing the hero cover or heading")
raise SystemExit(1)
PY
then
    pass "art-commissions core page keeps the hero cover and heading"
else
    fail "art-commissions core page is missing the hero cover or heading"
fi

if python3 - "$PLUGIN_FILE" <<'PY'
import sys
from pathlib import Path
text = Path(sys.argv[1]).read_text()
if "'How We Work'" in text and "/wp-content/uploads/2026/04/tma-karel-welding.jpg" in text:
    print("[PASS] how-we-work core page keeps the hero cover and heading")
    raise SystemExit(0)
print("[FAIL] how-we-work core page is missing the hero cover or heading")
raise SystemExit(1)
PY
then
    pass "how-we-work core page keeps the hero cover and heading"
else
    fail "how-we-work core page is missing the hero cover or heading"
fi

echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
