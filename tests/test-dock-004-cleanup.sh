#!/usr/bin/env bash
set -e
PASS=0
FAIL=0
TOTAL=0
ROOT="/srv/stacks/thormetalart"
COMPOSE_FILE="$ROOT/docker-compose.yml"

pass(){ PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail(){ FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo "\nTICKET-DOCK-004 — Cleanup servicios obsoletos\n"

if grep -qE "^\s*dashboard:\s*$|^\s*dashboard-api:\s*$|^\s*portal:\s*$" "$COMPOSE_FILE"; then
  fail "docker-compose aún contiene dashboard/dashboard-api/portal"
else
  pass "docker-compose sin servicios obsoletos"
fi

[ -d "$ROOT/_archive/dashboard" ] && pass "dashboard archivado en _archive/dashboard" || fail "Falta _archive/dashboard"
[ -d "$ROOT/_archive/portal" ] && pass "portal archivado en _archive/portal" || fail "Falta _archive/portal"

if grep -q "dashboard.thormetalart.com\|portal.thormetalart.com" "$COMPOSE_FILE"; then
  fail "Traefik labels obsoletos siguen presentes"
else
  pass "Traefik labels obsoletos removidos"
fi

if docker compose -f "$COMPOSE_FILE" config >/dev/null 2>&1; then
  pass "docker compose config válido"
else
  fail "docker compose config inválido"
fi

echo "\nRESULTADOS: $PASS pass / $FAIL fail / $TOTAL total\n"
[ "$FAIL" -eq 0 ] && exit 0 || exit "$FAIL"
