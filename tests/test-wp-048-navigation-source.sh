#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
MU="${ROOT}/data/wordpress/wp-content/mu-plugins"
THEME="${ROOT}/data/wordpress/wp-content/themes/thormetalart"
BASE_URL="https://dev.thormetalart.com"
PASS=0
FAIL=0

pass() {
  PASS=$((PASS + 1))
  echo "  PASS: $1"
}

fail() {
  FAIL=$((FAIL + 1))
  echo "  FAIL: $1"
}

echo "TICKET-WP-048 - Canonical service catalog"

if grep -q '^function tma_get_service_catalog' "${MU}/tma-service-pages.php"; then
  pass "canonical service catalog is defined"
else
  fail "canonical service catalog is not defined"
fi

for consumer in tma-service-pages.php tma-navigation.php tma-schema.php tma-contact-form.php; do
  if grep -q 'tma_get_service_catalog' "${MU}/${consumer}"; then
    pass "${consumer} consumes the canonical catalog"
  else
    fail "${consumer} does not consume the canonical catalog"
  fi
done

for template in parts/header.html parts/footer.html; do
  hardcoded=$(grep -cE '/(custom-metal-gates-miami|metal-railings-miami|metal-fences-miami|custom-metal-furniture-miami|metal-stairs-miami)/' "${THEME}/${template}" || true)
  if [[ "${hardcoded}" -eq 0 ]]; then
    pass "${template} has no duplicated service slugs"
  else
    fail "${template} contains ${hardcoded} duplicated service slugs"
  fi
done

contact_html=$(curl -kfsS "${BASE_URL}/contact/")
canonical_options=$(grep -oE 'value="(custom-gates|railings|fences|furniture|stairs)"' <<<"${contact_html}" | sort -u | wc -l)
if [[ "${canonical_options}" -eq 5 ]]; then
  pass "contact form renders the five canonical services"
else
  fail "contact form renders ${canonical_options} of 5 canonical services"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
