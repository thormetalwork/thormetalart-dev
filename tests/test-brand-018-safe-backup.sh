#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
SCRIPT="${ROOT}/scripts/backup-database.sh"
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

echo "TICKET-BRAND-018 - Safe database backup"

if grep -qE '^[[:space:]]*(source|\.)[[:space:]].*\.env' "${SCRIPT}"; then
  fail "backup script executes .env as shell code"
else
  pass "backup script does not execute .env"
fi

if grep -q 'docker compose.*exec -T mysql' "${SCRIPT}"; then
  pass "backup uses the Compose mysql service"
else
  fail "backup does not use docker compose exec -T mysql"
fi

if grep -q 'printenv MYSQL_DATABASE' "${SCRIPT}"; then
  pass "database name is read from the container environment"
else
  fail "database name is not read from the container environment"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]