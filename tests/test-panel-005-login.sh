#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → Test suite for TICKET-PANEL-005: Login template custom branded
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"
PANEL_HOST="panel.thormetalart.com"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-005 — Login template custom branded           ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence checks"

[ -f "$PLUGIN_DIR/templates/login.php" ] \
  && pass "templates/login.php exists" \
  || fail "templates/login.php missing"

[ -f "$PLUGIN_DIR/templates/forgot-password.php" ] \
  && pass "templates/forgot-password.php exists" \
  || fail "templates/forgot-password.php missing"

[ -f "$PLUGIN_DIR/templates/reset-password.php" ] \
  && pass "templates/reset-password.php exists" \
  || fail "templates/reset-password.php missing"

# ─────────────────────────────────────────────────────────────────
# 2. LOGIN TEMPLATE — BRANDING
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🎨 Login template branding"

LOGIN="$PLUGIN_DIR/templates/login.php"
if [ -f "$LOGIN" ]; then
  grep -q '#B8860B\|B8860B' "$LOGIN" \
    && pass "Login has gold accent color" \
    || fail "Login missing gold accent #B8860B"

  grep -q 'Thor Metal Art' "$LOGIN" \
    && pass "Login shows Thor Metal Art brand" \
    || fail "Login missing brand name"

  grep -q 'Cormorant Garamond' "$LOGIN" \
    && pass "Login uses display font" \
    || fail "Login missing Cormorant Garamond font"

  grep -q 'dark\|#0c0a09\|#1c1917' "$LOGIN" \
    && pass "Login has dark theme" \
    || fail "Login missing dark theme"

  # No WordPress references visible
  grep -qi 'wordpress\|wp-admin\|wp-login' "$LOGIN" \
    && fail "Login leaks WordPress reference" \
    || pass "Login has no WordPress references"

  grep -q 'rememberme\|remember' "$LOGIN" \
    && pass "Login has remember-me checkbox" \
    || fail "Login missing remember-me"

  grep -qi 'olvidaste\|forgot.*password\|recuperar' "$LOGIN" \
    && pass "Login has forgot password link" \
    || fail "Login missing forgot password link"
else
  for t in "gold" "brand" "font" "dark" "no-wp" "remember" "forgot-link"; do
    fail "$t (file missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 3. FORGOT PASSWORD TEMPLATE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔑 Forgot password template"

FORGOT="$PLUGIN_DIR/templates/forgot-password.php"
if [ -f "$FORGOT" ]; then
  grep -q 'Thor Metal Art' "$FORGOT" \
    && pass "Forgot password has branding" \
    || fail "Forgot password missing branding"

  grep -q '#B8860B\|B8860B' "$FORGOT" \
    && pass "Forgot password has gold accent" \
    || fail "Forgot password missing gold accent"

  grep -q 'email\|correo' "$FORGOT" \
    && pass "Forgot password has email field" \
    || fail "Forgot password missing email field"

  grep -q 'nonce\|wp_nonce_field' "$FORGOT" \
    && pass "Forgot password has nonce protection" \
    || fail "Forgot password missing nonce"
else
  for t in "branding" "gold" "email" "nonce"; do
    fail "$t (forgot-password.php missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 4. RESET PASSWORD TEMPLATE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔐 Reset password template"

RESET="$PLUGIN_DIR/templates/reset-password.php"
if [ -f "$RESET" ]; then
  grep -q 'Thor Metal Art' "$RESET" \
    && pass "Reset password has branding" \
    || fail "Reset password missing branding"

  grep -q 'password\|contraseña' "$RESET" \
    && pass "Reset password has password fields" \
    || fail "Reset password missing password fields"

  grep -q 'nonce\|wp_nonce_field' "$RESET" \
    && pass "Reset password has nonce protection" \
    || fail "Reset password missing nonce"
else
  for t in "branding" "password-fields" "nonce"; do
    fail "$t (reset-password.php missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 5. RATE LIMITING
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🛡️  Rate limiting"

# Check if rate limiting code exists in tma-panel.php or login handler
MAIN="$PLUGIN_DIR/tma-panel.php"
RATE_FILE=""
grep -rl 'rate_limit\|login_attempts\|tma_panel_login_attempts' "$PLUGIN_DIR" 2>/dev/null | head -1 | read -r RATE_FILE || true

if [ -n "$RATE_FILE" ]; then
  grep -q 'login_attempts\|tma_panel_login_attempts' "$RATE_FILE" \
    && pass "Rate limiting tracks login attempts" \
    || fail "Rate limiting not tracking attempts"

  grep -q 'transient\|set_transient\|get_transient' "$RATE_FILE" \
    && pass "Rate limiting uses transients" \
    || fail "Rate limiting not using transients"
else
  # Check across all plugin PHP files
  FOUND_RATE=$(grep -rl 'login_attempts\|rate_limit' "$PLUGIN_DIR" 2>/dev/null | wc -l)
  [ "$FOUND_RATE" -ge 1 ] \
    && pass "Rate limiting code found" \
    || fail "Rate limiting code not found"
  
  FOUND_TRANSIENT=$(grep -rl 'transient' "$PLUGIN_DIR" 2>/dev/null | wc -l)
  [ "$FOUND_TRANSIENT" -ge 1 ] \
    && pass "Transient storage for rate limiting" \
    || fail "No transient storage for rate limiting"
fi

# ─────────────────────────────────────────────────────────────────
# 6. ROUTING — FORGOT/RESET PATHS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔀 Route handling"

grep -q 'forgot-password\|forgot_password' "$MAIN" \
  && pass "Main plugin routes /forgot-password" \
  || fail "Main plugin doesn't route /forgot-password"

grep -q 'reset-password\|reset_password' "$MAIN" \
  && pass "Main plugin routes /reset-password" \
  || fail "Main plugin doesn't route /reset-password"

# ─────────────────────────────────────────────────────────────────
# 7. PHP SYNTAX
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax validation"

for f in "templates/login.php" "templates/forgot-password.php" "templates/reset-password.php" "tma-panel.php"; do
  if [ -f "$PLUGIN_DIR/$f" ]; then
    docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" \
      && pass "Syntax OK: $f" \
      || fail "Syntax error: $f"
  else
    fail "Syntax check skipped (missing): $f"
  fi
done

# ─────────────────────────────────────────────────────────────────
# 8. LOGIN PAGE RETURNS 200
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 HTTP response checks"

LOGIN_OUTPUT=$(docker exec "$WP_CONTAINER" php -r "
  \$_SERVER['HTTP_HOST'] = 'panel.thormetalart.com';
  \$_SERVER['REQUEST_URI'] = '/login';
  \$_SERVER['REQUEST_METHOD'] = 'GET';
  require '/var/www/html/wp-load.php';
" 2>/dev/null || true)
echo "$LOGIN_OUTPUT" | grep -q 'Thor Metal Art' \
  && pass "Login page renders content" \
  || fail "Login page doesn't render"

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
