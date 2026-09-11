#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
STYLE="${ROOT}/data/wordpress/wp-content/themes/thormetalart/style.css"
HEADER="${ROOT}/data/wordpress/wp-content/themes/thormetalart/parts/header.html"
NAVIGATION="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-navigation.php"
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

echo "TICKET-FIX-003 - Services dropdown"

header_html=$(curl -kfsS 'https://dev.thormetalart.com/')
service_links=$(grep -oE 'href="https://dev\.thormetalart\.com/(custom-metal-gates-miami|metal-railings-miami|metal-fences-miami|custom-metal-furniture-miami|metal-stairs-miami)/"' <<<"${header_html}" | sed -E 's/.*\/(custom|metal).*/&/' | sort -u | wc -l)
if [[ "${service_links}" -eq 5 && $(grep -c '\[tma_primary_navigation\]' "${HEADER}") -eq 1 ]]; then
  pass "header renders the five canonical service links"
else
  fail "header renders ${service_links} canonical service links instead of 5"
fi

desktop_header_rule=$(awk '
  /@media \(min-width: 961px\)/ { desktop = 1 }
  desktop && /\.tma-site-header[[:space:]]*\{/ { header = 1 }
  desktop && header && /overflow-x:[[:space:]]*clip/ { x = 1 }
  desktop && header && /overflow-y:[[:space:]]*visible/ { y = 1 }
  desktop && /^}/ { desktop = header = 0 }
  END { print x && y ? "yes" : "no" }
' "${STYLE}")

if [[ "${desktop_header_rule}" == "yes" ]]; then
  pass "desktop header allows vertical submenu overflow"
else
  fail "desktop header still clips vertical submenu overflow"
fi

if grep -q '<details class="tma-services-menu"><summary>' "${NAVIGATION}"; then
  pass "navigation uses the native keyboard-operable submenu control"
else
  fail "navigation does not use details/summary for the service submenu"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
