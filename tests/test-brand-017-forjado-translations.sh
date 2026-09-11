#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
PLUGIN="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-brand-translations.php"
PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

echo "TICKET-BRAND-017 - Lujo Forjado translations"

if grep -q '\$upsert_text' "${PLUGIN}" && grep -q 'tma_brand_translations_v2' "${PLUGIN}" && \
  ! grep -qE '\$upsert\([0-9]+' "${PLUGIN}"; then
  pass "translation migration uses portable original-text keys"
else
  fail "translation migration still depends only on environment-specific IDs"
fi

home=$(curl -kfsS 'https://dev.thormetalart.com/es/')
if ! grep -Eq 'Made to order|Every project|Built for spaces across South Florida|>Residential<|>Commercial<|>Hospitality<|>Architectural<|>Public Art<|>Custom Commissions<' <<<"${home}"; then
  pass "Spanish homepage contains no new English proof strings"
else
  fail "Spanish homepage still contains untranslated proof strings"
fi

for path in contact art-commissions how-we-work; do
  page=$(curl -kfsS "https://dev.thormetalart.com/es/${path}/")
  if ! grep -Eq '>Get in Touch<|>Art and commissions<|>Our process<|>Metal as Art<|>How We Work<' <<<"${page}"; then
    pass "${path} hero is translated"
  else
    fail "${path} hero still contains English copy"
  fi
done

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
