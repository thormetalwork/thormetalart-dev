#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
PLUGIN="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-service-pages.php"
TEMPLATE="${ROOT}/data/wordpress/wp-content/themes/thormetalart/templates/page-service.html"
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

echo "TICKET-WP-047 - Service page architecture"

if [[ -f "${TEMPLATE}" ]]; then
  pass "dedicated service template exists"
else
  fail "dedicated service template is missing"
fi

service_body=$(awk '/^function tma_service_page_content/{active=1} active{print} active && /^}/ {exit}' "${PLUGIN}")
if grep -q 'tma_page_shell_markup' <<<"${service_body}"; then
  fail "service post content still owns the page shell"
else
  pass "service post content contains body content only"
fi

slugs=(
  custom-metal-gates-miami
  metal-railings-miami
  metal-fences-miami
  custom-metal-furniture-miami
  metal-stairs-miami
)

assigned_templates=$(docker compose --project-directory "${ROOT}" exec -T mysql sh -c \
  'MYSQL_PWD="${MYSQL_PASSWORD}" mysql -u "${MYSQL_USER}" "${MYSQL_DATABASE}" -Nse "SELECT COUNT(*) FROM tma_posts p INNER JOIN tma_postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '\''_wp_page_template'\'' WHERE p.post_name IN ('\''custom-metal-gates-miami'\'', '\''metal-railings-miami'\'', '\''metal-fences-miami'\'', '\''custom-metal-furniture-miami'\'', '\''metal-stairs-miami'\'') AND pm.meta_value = '\''page-service'\'';"')
if [[ "${assigned_templates}" -eq 5 ]]; then
  pass "all five services use the dedicated template"
else
  fail "${assigned_templates} of 5 services use the dedicated template"
fi

for slug in "${slugs[@]}"; do
  html=$(curl -kfsS "${BASE_URL}/${slug}/")
  shells=$(grep -o 'class="[^"]*tma-page-shell[^"]*"' <<<"${html}" | wc -l)
  breadcrumbs=$(grep -o 'class="[^"]*tma-breadcrumbs[^"]*"' <<<"${html}" | wc -l)
  headings=$(grep -o '<h1[ >]' <<<"${html}" | wc -l)
  if [[ "${shells}" -eq 1 && "${breadcrumbs}" -eq 1 && "${headings}" -eq 1 ]]; then
    pass "${slug} renders one shell, breadcrumb and H1"
  else
    fail "${slug} renders shells=${shells}, breadcrumbs=${breadcrumbs}, h1=${headings}"
  fi
done

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
