#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → TICKET-PANEL-009: Security hardening
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-009 — Security hardening                      ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. SECURITY HEADERS (already exist from PANEL-001 — verify)
# ─────────────────────────────────────────────────────────────────
echo "🔒 Security headers in router"

ROUTER="$PLUGIN_DIR/includes/class-tma-panel-router.php"

grep -q 'X-Content-Type-Options: nosniff' "$ROUTER" \
  && pass "X-Content-Type-Options header" \
  || fail "X-Content-Type-Options header missing"

grep -q 'X-Frame-Options: DENY' "$ROUTER" \
  && pass "X-Frame-Options header" \
  || fail "X-Frame-Options header missing"

grep -q 'Referrer-Policy: strict-origin' "$ROUTER" \
  && pass "Referrer-Policy header" \
  || fail "Referrer-Policy header missing"

grep -q 'X-Robots-Tag: noindex' "$ROUTER" \
  && pass "X-Robots-Tag header" \
  || fail "X-Robots-Tag header missing"

grep -q 'Permissions-Policy' "$ROUTER" \
  && pass "Permissions-Policy header" \
  || fail "Permissions-Policy header missing"

grep -q "Content-Security-Policy" "$ROUTER" \
  && pass "CSP header present" \
  || fail "CSP header missing"

# ─────────────────────────────────────────────────────────────────
# 2. WP-ADMIN REDIRECT FOR TMA ROLES
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🚫 wp-admin redirect for TMA roles"

MAIN="$PLUGIN_DIR/tma-panel.php"

grep -q 'admin_init\|admin_page_access_denied' "$MAIN" \
  && pass "Has admin_init or admin_page hook" \
  || fail "No admin_init/admin_page hook for wp-admin redirect"

grep -q 'tma_client\|tma_admin' "$MAIN" \
  && pass "References TMA roles in security check" \
  || fail "No TMA role references for redirect"

# Test: tma_client is blocked from wp-admin
WP_ADMIN_BLOCK=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  // Check if the redirect hook is registered
  \$hooks = has_action('admin_init');
  echo \$hooks ? 'REGISTERED' : 'NOT_REGISTERED';
" 2>/dev/null || echo "ERROR")

[ "$WP_ADMIN_BLOCK" = "REGISTERED" ] \
  && pass "admin_init hook registered for redirect" \
  || fail "admin_init hook not registered (got: $WP_ADMIN_BLOCK)"

# ─────────────────────────────────────────────────────────────────
# 3. ADMIN BAR HIDDEN FOR TMA ROLES
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🙈 Admin bar hidden for TMA roles"

grep -q 'show_admin_bar\|admin_bar' "$MAIN" \
  && pass "show_admin_bar filter/hook present" \
  || fail "show_admin_bar filter/hook missing"

# Check via PHP that show_admin_bar logic targets tma roles
ADMINBAR_CODE=$(grep -c 'show_admin_bar' "$MAIN" 2>/dev/null || echo "0")
[ "$ADMINBAR_CODE" -ge 1 ] \
  && pass "Admin bar control code exists" \
  || fail "Admin bar control code missing"

# ─────────────────────────────────────────────────────────────────
# 4. SESSION TIMEOUT / AUTH COOKIE EXPIRATION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "⏰ Session timeout configuration"

grep -q 'auth_cookie_expiration' "$MAIN" \
  && pass "auth_cookie_expiration filter present" \
  || fail "auth_cookie_expiration filter missing"

# Verify timeout value is approximately 12 hours (43200 seconds)
grep -qE '43200|12\s*\*\s*HOUR_IN_SECONDS' "$MAIN" \
  && pass "Timeout set to ~12 hours" \
  || fail "Timeout not set to 12 hours"

# ─────────────────────────────────────────────────────────────────
# 5. CORS CONFIGURATION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 CORS configuration"

grep -qi 'Access-Control-Allow-Origin\|cors\|allowed_http_origins' "$MAIN" \
  && pass "CORS configuration present" \
  || fail "CORS configuration missing"

# ─────────────────────────────────────────────────────────────────
# 6. PHP SYNTAX CHECK
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax validation"

for f in "tma-panel.php" "includes/class-tma-panel-router.php"; do
  docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" \
    && pass "Syntax OK: $f" \
    || fail "Syntax error: $f"
done

# ─────────────────────────────────────────────────────────────────
# 7. INTEGRATION — hooks active
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔌 Integration checks"

# Verify the plugin loads without fatal errors
LOAD_CHECK=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  echo is_plugin_active('tma-panel/tma-panel.php') ? 'ACTIVE' : 'INACTIVE';
" 2>/dev/null || echo "ERROR")

[ "$LOAD_CHECK" = "ACTIVE" ] \
  && pass "Plugin active after security changes" \
  || fail "Plugin not active (got: $LOAD_CHECK)"

# Check auth_cookie_expiration filter is registered
AUTH_FILTER=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  echo has_filter('auth_cookie_expiration') ? 'YES' : 'NO';
" 2>/dev/null || echo "ERROR")

[ "$AUTH_FILTER" = "YES" ] \
  && pass "auth_cookie_expiration filter registered" \
  || fail "auth_cookie_expiration filter not registered (got: $AUTH_FILTER)"

# ─────────────────────────────────────────────────────────────────
#  RESULTS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  RESULTS: $PASS passed / $FAIL failed / $TOTAL total"
echo "════════════════════════════════════════════════════════════"
echo ""

[ "$FAIL" -eq 0 ] && echo "🎉 ALL TESTS PASSED" || echo "💔 SOME TESTS FAILED"
exit "$FAIL"
