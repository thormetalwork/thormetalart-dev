#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
WP_CONTAINER="tma_dev_wordpress"
CONTACT="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-contact-form.php"
PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

echo "TICKET-LEAD-005 - Canonical lead services"

catalog=$(docker exec "${WP_CONTAINER}" php -r 'require "/var/www/html/wp-load.php"; echo wp_json_encode(array_keys(tma_get_contact_service_options()));')
expected='["custom-gates","railings","fences","furniture","stairs","metal-art","other"]'
if [[ "${catalog}" == "${expected}" ]]; then
  pass "form exposes five services, art and other with canonical values"
else
  fail "unexpected service catalog: ${catalog}"
fi

validation=$(docker exec "${WP_CONTAINER}" php -r 'require "/var/www/html/wp-load.php"; $valid=tma_validate_lead_service("stairs"); $invalid=tma_validate_lead_service("<script>alert(1)</script>"); echo wp_json_encode([$valid, is_wp_error($invalid)]);')
if [[ "${validation}" == '["stairs",true]' ]]; then
  pass "allowlist accepts a canonical value and rejects an arbitrary value"
else
  fail "service validation result is incorrect: ${validation}"
fi

if grep -q '\$service = tma_validate_lead_service' "${CONTACT}"; then
  pass "submission handler validates service before persistence"
else
  fail "submission handler bypasses the canonical allowlist"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
