#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
ARCHIVE="${ROOT}/data/wordpress/wp-content/themes/thormetalart/templates/archive-tma_portfolio.html"
TAXONOMY="${ROOT}/data/wordpress/wp-content/themes/thormetalart/templates/taxonomy-tma_project_type.html"
SINGLE="${ROOT}/data/wordpress/wp-content/themes/thormetalart/templates/single-tma_portfolio.html"
PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

echo "TICKET-BRAND-015 - Portfolio Lujo Forjado"

for template in "${ARCHIVE}" "${TAXONOMY}"; do
  if grep -q 'tma-forjado-card' "${template}"; then
    pass "$(basename "${template}") uses Forjado cards"
  else
    fail "$(basename "${template}") still uses legacy cards"
  fi
done

if grep -q 'tma-portfolio-hero' "${SINGLE}" && grep -q 'tma-portfolio-hero__image' "${SINGLE}"; then
  pass "portfolio single places the featured image in its hero"
else
  fail "portfolio single hero does not own the featured image"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
