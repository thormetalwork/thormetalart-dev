#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
ATELIER="${ROOT}/data/wordpress/wp-content/themes/thormetalart/patterns/atelier.php"
PROOF="${ROOT}/data/wordpress/wp-content/themes/thormetalart/patterns/client-logos.php"
SEED="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-seed-content.php"
PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

echo "TICKET-BRAND-020 - Verified public proof"

if ! grep -q '4.9' "${ATELIER}"; then
  pass "atelier contains no unverified rating"
else
  fail "atelier still publishes an unverified rating"
fi

if ! grep -Eq 'ARCADIA HOMES|BRICKELL DESIGN|CORAL GABLES DEV|DORAL INTERIORS|WYNWOOD STUDIO|SOUTH BEACH ARC' "${PROOF}"; then
  pass "proof band contains no placeholder client names"
else
  fail "proof band still presents placeholder names as clients"
fi

if grep -q 'tma_retire_unverified_seed_content' "${SEED}" && \
   grep -q "hash_equals(\$legacy_testimonials\[\$testimonial->post_title\], \$testimonial->post_content)" "${SEED}" && \
   ! grep -q '\$testimonials = array' "${SEED}"; then
  pass "seed migration retires only exact unverified testimonials instead of publishing them"
else
  fail "seed plugin can still publish unverified testimonials"
fi

curl -kfsS 'https://dev.thormetalart.com/' >/dev/null
published=$(docker compose --project-directory "${ROOT}" exec -T mysql sh -c \
  'MYSQL_PWD="${MYSQL_PASSWORD}" mysql -u "${MYSQL_USER}" "${MYSQL_DATABASE}" -Nse "SELECT COUNT(*) FROM tma_posts WHERE post_status='\''publish'\'' AND (post_title LIKE '\''Review - %'\'' OR post_content LIKE '\''Project summary placeholder.%'\'');"')
if [[ "${published}" -eq 0 ]]; then
  pass "DEV publishes no identifiable seed testimonials or projects"
else
  fail "DEV still publishes ${published} identifiable seed testimonials or projects"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
