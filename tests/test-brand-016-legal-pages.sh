#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
TEMPLATE="${ROOT}/data/wordpress/wp-content/themes/thormetalart/templates/page-legal.html"
STYLE="${ROOT}/data/wordpress/wp-content/themes/thormetalart/style.css"
PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

echo "TICKET-BRAND-016 - Legal pages"

if [[ -f "${TEMPLATE}" ]] && grep -q 'tma-legal-page' "${TEMPLATE}"; then
  pass "legal pages have a dedicated readable template"
else
  fail "legal page template is missing"
fi

if grep -q '.tma-legal-page' "${STYLE}" && grep -q 'max-width: 760px' "${STYLE}"; then
  pass "legal content has a stable reading width"
else
  fail "legal content has no constrained reading width"
fi

curl -kfsS 'https://dev.thormetalart.com/privacy-policy/' >/dev/null
curl -kfsS 'https://dev.thormetalart.com/terms-of-service/' >/dev/null
assigned=$(docker compose --project-directory "${ROOT}" exec -T mysql sh -c \
  'MYSQL_PWD="${MYSQL_PASSWORD}" mysql -u "${MYSQL_USER}" "${MYSQL_DATABASE}" -Nse "SELECT COUNT(*) FROM tma_posts p INNER JOIN tma_postmeta pm ON pm.post_id=p.ID AND pm.meta_key='\''_wp_page_template'\'' WHERE p.post_name IN ('\''privacy-policy'\'', '\''terms-of-service'\'') AND pm.meta_value='\''page-legal'\'';"')
if [[ "${assigned}" -eq 2 ]]; then
  pass "privacy and terms use the legal template"
else
  fail "${assigned} of 2 legal pages use the legal template"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
