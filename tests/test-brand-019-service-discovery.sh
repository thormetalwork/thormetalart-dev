#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
PLUGIN="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-service-pages.php"
PATTERN="${ROOT}/data/wordpress/wp-content/themes/thormetalart/patterns/disciplines.php"
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

echo "TICKET-BRAND-019 - Service discovery and related work"

if grep -q '\[tma_service_catalog\]' "${PATTERN}" && ! grep -qE '/(custom-metal-gates-miami|metal-railings-miami|metal-fences-miami|custom-metal-furniture-miami|metal-stairs-miami)/' "${PATTERN}"; then
  pass "homepage pattern consumes the canonical service catalog"
else
  fail "homepage pattern still duplicates or omits service links"
fi

if grep -q "add_shortcode('tma_related_work'" "${PLUGIN}" && grep -q '\[tma_related_work\]' "${PLUGIN}"; then
  pass "service content includes dynamic related work"
else
  fail "dynamic related work is not registered and embedded"
fi

slugs=(
  custom-metal-gates-miami
  metal-railings-miami
  metal-fences-miami
  custom-metal-furniture-miami
  metal-stairs-miami
)

for slug in "${slugs[@]}"; do
  html=$(curl -kfsS "${BASE_URL}/${slug}/")
  sections=$(grep -o 'class="tma-related-work"' <<<"${html}" | wc -l || true)
  cards=$(grep -o 'class="tma-related-work__card"' <<<"${html}" | wc -l || true)
  if [[ "${sections}" -eq 1 && "${cards}" -le 3 ]]; then
    pass "${slug} renders one related section with at most three projects"
  else
    fail "${slug} renders sections=${sections}, cards=${cards}"
  fi
done

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
