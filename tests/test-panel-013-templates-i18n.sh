#!/usr/bin/env bash
# =============================================================================
# test-panel-013-templates-i18n.sh
# TDD tests for TICKET-PANEL-016: data-i18n in auth templates
# =============================================================================
set -euo pipefail

PASS=0
FAIL=0
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"

pass() { echo "  ✅ PASS: $1"; PASS=$(( PASS + 1 )); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$(( FAIL + 1 )); }

echo ""
echo "════════════════════════════════════════════════════════════"
echo "  TICKET-PANEL-016 — data-i18n en templates auth"
echo "════════════════════════════════════════════════════════════"
echo ""

# ─────────────────────────────────────────────────────────────────────────────
# 1. login.php — data-i18n attributes
# ─────────────────────────────────────────────────────────────────────────────
echo "── login.php ──────────────────────────────────────────────"

LOGIN="$PLUGIN_DIR/templates/login.php"

grep -q 'data-i18n="login.title"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.title\" present" \
	|| fail "login.php: data-i18n=\"login.title\" missing"

grep -q 'data-i18n="login.subtitle"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.subtitle\" present" \
	|| fail "login.php: data-i18n=\"login.subtitle\" missing"

grep -q 'data-i18n="login.email"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.email\" present" \
	|| fail "login.php: data-i18n=\"login.email\" missing"

grep -q 'data-i18n="login.password"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.password\" present" \
	|| fail "login.php: data-i18n=\"login.password\" missing"

grep -q 'data-i18n="login.remember"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.remember\" present" \
	|| fail "login.php: data-i18n=\"login.remember\" missing"

grep -q 'data-i18n="login.submit"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.submit\" present" \
	|| fail "login.php: data-i18n=\"login.submit\" missing"

grep -q 'data-i18n="login.forgot"' "$LOGIN" \
	&& pass "login.php: data-i18n=\"login.forgot\" present" \
	|| fail "login.php: data-i18n=\"login.forgot\" missing"

# Script tag loading i18n.js
grep -q 'assets/js/i18n.js' "$LOGIN" \
	&& pass "login.php: loads i18n.js" \
	|| fail "login.php: does not load i18n.js"

# TMA_i18n.init() called
grep -q 'TMA_i18n.init' "$LOGIN" \
	&& pass "login.php: calls TMA_i18n.init()" \
	|| fail "login.php: TMA_i18n.init() not called"

echo ""

# ─────────────────────────────────────────────────────────────────────────────
# 2. forgot-password.php — data-i18n attributes
# ─────────────────────────────────────────────────────────────────────────────
echo "── forgot-password.php ─────────────────────────────────────"

FORGOT="$PLUGIN_DIR/templates/forgot-password.php"

grep -q 'data-i18n="auth.forgot_title"' "$FORGOT" \
	&& pass "forgot-password.php: data-i18n=\"auth.forgot_title\" present" \
	|| fail "forgot-password.php: data-i18n=\"auth.forgot_title\" missing"

grep -q 'data-i18n="login.email"' "$FORGOT" \
	&& pass "forgot-password.php: data-i18n=\"login.email\" present" \
	|| fail "forgot-password.php: data-i18n=\"login.email\" missing"

grep -q 'data-i18n="auth.send_reset"' "$FORGOT" \
	&& pass "forgot-password.php: data-i18n=\"auth.send_reset\" present" \
	|| fail "forgot-password.php: data-i18n=\"auth.send_reset\" missing"

grep -q 'data-i18n="auth.back_login"' "$FORGOT" \
	&& pass "forgot-password.php: data-i18n=\"auth.back_login\" present" \
	|| fail "forgot-password.php: data-i18n=\"auth.back_login\" missing"

grep -q 'assets/js/i18n.js' "$FORGOT" \
	&& pass "forgot-password.php: loads i18n.js" \
	|| fail "forgot-password.php: does not load i18n.js"

grep -q 'TMA_i18n.init' "$FORGOT" \
	&& pass "forgot-password.php: calls TMA_i18n.init()" \
	|| fail "forgot-password.php: TMA_i18n.init() not called"

echo ""

# ─────────────────────────────────────────────────────────────────────────────
# 3. reset-password.php — data-i18n attributes
# ─────────────────────────────────────────────────────────────────────────────
echo "── reset-password.php ──────────────────────────────────────"

RESET="$PLUGIN_DIR/templates/reset-password.php"

grep -q 'data-i18n="auth.reset_title"' "$RESET" \
	&& pass "reset-password.php: data-i18n=\"auth.reset_title\" present" \
	|| fail "reset-password.php: data-i18n=\"auth.reset_title\" missing"

grep -q 'data-i18n="auth.new_password"' "$RESET" \
	&& pass "reset-password.php: data-i18n=\"auth.new_password\" present" \
	|| fail "reset-password.php: data-i18n=\"auth.new_password\" missing"

grep -q 'data-i18n="auth.confirm_password"' "$RESET" \
	&& pass "reset-password.php: data-i18n=\"auth.confirm_password\" present" \
	|| fail "reset-password.php: data-i18n=\"auth.confirm_password\" missing"

grep -q 'data-i18n="auth.reset_submit"' "$RESET" \
	&& pass "reset-password.php: data-i18n=\"auth.reset_submit\" present" \
	|| fail "reset-password.php: data-i18n=\"auth.reset_submit\" missing"

grep -q 'assets/js/i18n.js' "$RESET" \
	&& pass "reset-password.php: loads i18n.js" \
	|| fail "reset-password.php: does not load i18n.js"

grep -q 'TMA_i18n.init' "$RESET" \
	&& pass "reset-password.php: calls TMA_i18n.init()" \
	|| fail "reset-password.php: TMA_i18n.init() not called"

echo ""

# ─────────────────────────────────────────────────────────────────────────────
# 4. i18n.js — new keys exist in ES dictionary
# ─────────────────────────────────────────────────────────────────────────────
echo "── i18n.js — ES dictionary ─────────────────────────────────"

I18N="$PLUGIN_DIR/assets/js/i18n.js"

for key in 'login.title' 'login.subtitle' 'login.email' 'login.password' \
           'login.remember' 'login.submit' 'login.forgot' \
           'auth.forgot_title' 'auth.send_reset' 'auth.back_login' \
           'auth.reset_title' 'auth.new_password' 'auth.confirm_password' \
           'auth.reset_submit'; do
	grep -q "'${key}'" "$I18N" \
		&& pass "i18n.js ES: '${key}' present" \
		|| fail "i18n.js ES: '${key}' missing"
done

echo ""

# ─────────────────────────────────────────────────────────────────────────────
# 5. i18n.js — new keys exist in EN dictionary (after 'en:' block)
# ─────────────────────────────────────────────────────────────────────────────
echo "── i18n.js — EN dictionary ─────────────────────────────────"

# EN entries must appear after the 'en:' marker
EN_BLOCK=$(awk '/^\t\ten: \{/,0' "$I18N")

for key in 'login.title' 'login.email' 'login.password' \
           'login.remember' 'login.submit' 'login.forgot' \
           'auth.forgot_title' 'auth.send_reset' 'auth.back_login' \
           'auth.reset_title' 'auth.reset_submit'; do
	echo "$EN_BLOCK" | grep -q "'${key}'" \
		&& pass "i18n.js EN: '${key}' present" \
		|| fail "i18n.js EN: '${key}' missing"
done

echo ""

# ─────────────────────────────────────────────────────────────────────────────
# 6. PHP syntax validation
# ─────────────────────────────────────────────────────────────────────────────
echo "── PHP syntax ──────────────────────────────────────────────"

CONTAINER_PLUGIN_DIR="/var/www/html/wp-content/plugins/tma-panel"
for name in login.php forgot-password.php reset-password.php; do
	docker exec tma_dev_wordpress php -l "${CONTAINER_PLUGIN_DIR}/templates/${name}" > /dev/null 2>&1 \
		&& pass "PHP syntax valid: $name" \
		|| fail "PHP syntax error: $name"
done

echo ""

# ─────────────────────────────────────────────────────────────────────────────
# Summary
# ─────────────────────────────────────────────────────────────────────────────
TOTAL=$(( PASS + FAIL ))
echo "════════════════════════════════════════════════════════════"
echo "  Resultado: $PASS pasaron / $FAIL fallaron / $TOTAL total"
echo "════════════════════════════════════════════════════════════"
echo ""

if [ "$FAIL" -eq 0 ]; then
	echo "  ✅ ALL TESTS PASSED — TICKET-PANEL-016 GREEN"
	exit 0
else
	echo "  ❌ TESTS FAILED — implementar para pasar a GREEN"
	exit 1
fi
