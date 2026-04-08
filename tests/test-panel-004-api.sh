#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → Test suite for TICKET-PANEL-004: REST API endpoints base
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
PANEL_HOST="panel.thormetalart.com"
API_BASE="https://${PANEL_HOST}/wp-json/tma-panel/v1"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-004 — REST API endpoints base                 ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence checks"

[ -f "$PLUGIN_DIR/includes/class-tma-panel-api.php" ] \
  && pass "class-tma-panel-api.php exists" \
  || fail "class-tma-panel-api.php missing"

# ─────────────────────────────────────────────────────────────────
# 2. CODE PATTERNS — API CLASS
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔍 Code patterns in class-tma-panel-api.php"

API_FILE="$PLUGIN_DIR/includes/class-tma-panel-api.php"
if [ -f "$API_FILE" ]; then
  grep -q 'class TMA_Panel_API' "$API_FILE" \
    && pass "TMA_Panel_API class defined" \
    || fail "TMA_Panel_API class not found"

  grep -q 'tma-panel/v1' "$API_FILE" \
    && pass "Namespace tma-panel/v1 defined" \
    || fail "Namespace tma-panel/v1 not found"

  grep -q 'register_rest_route' "$API_FILE" \
    && pass "Uses register_rest_route" \
    || fail "register_rest_route not used"

  grep -q 'rest_api_init' "$API_FILE" \
    && pass "Hooks into rest_api_init" \
    || fail "rest_api_init hook not found"

  grep -q 'permission_callback' "$API_FILE" \
    && pass "Has permission_callback" \
    || fail "Missing permission_callback"

  grep -q 'sanitize_text_field\|sanitize_callback' "$API_FILE" \
    && pass "Uses sanitization" \
    || fail "No sanitization found"

  grep -q 'prepare\|wpdb' "$API_FILE" \
    && pass "Uses \$wpdb->prepare() or \$wpdb" \
    || fail "No prepared statements found"

  # Endpoint patterns
  grep -q '/dashboard' "$API_FILE" \
    && pass "Has /dashboard endpoint" \
    || fail "Missing /dashboard endpoint"

  grep -q '/documents' "$API_FILE" \
    && pass "Has /documents endpoint" \
    || fail "Missing /documents endpoint"

  grep -q '/leads' "$API_FILE" \
    && pass "Has /leads endpoint" \
    || fail "Missing /leads endpoint"

  grep -q '/notes' "$API_FILE" \
    && pass "Has /notes endpoint" \
    || fail "Missing /notes endpoint"

  grep -q '/audit' "$API_FILE" \
    && pass "Has /audit endpoint" \
    || fail "Missing /audit endpoint"

  grep -q '/export' "$API_FILE" \
    && pass "Has /export endpoint" \
    || fail "Missing /export endpoint"

  # Capability checks
  grep -q 'tma_view_panel' "$API_FILE" \
    && pass "Checks tma_view_panel capability" \
    || fail "tma_view_panel capability check missing"

  grep -q 'tma_view_audit' "$API_FILE" \
    && pass "Checks tma_view_audit capability" \
    || fail "tma_view_audit capability check missing"
else
  for t in "TMA_Panel_API class" "Namespace" "register_rest_route" "rest_api_init" \
    "permission_callback" "sanitization" "prepared statements" \
    "/dashboard" "/documents" "/leads" "/notes" "/audit" "/export" \
    "tma_view_panel" "tma_view_audit"; do
    fail "$t (file missing)"
  done
fi

# ─────────────────────────────────────────────────────────────────
# 3. BOOTSTRAP INTEGRATION
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔌 Bootstrap integration"

MAIN_FILE="$PLUGIN_DIR/tma-panel.php"
grep -q 'class-tma-panel-api.php' "$MAIN_FILE" \
  && pass "Main plugin requires API class" \
  || fail "Main plugin doesn't require API class"

grep -q 'TMA_Panel_API' "$MAIN_FILE" \
  && pass "Main plugin references TMA_Panel_API" \
  || fail "Main plugin doesn't reference TMA_Panel_API"

# ─────────────────────────────────────────────────────────────────
# 4. PHP SYNTAX CHECK
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax validation"

for f in "includes/class-tma-panel-api.php" "tma-panel.php"; do
  if [ -f "$PLUGIN_DIR/$f" ]; then
    docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" \
      && pass "Syntax OK: $f" \
      || fail "Syntax error: $f"
  else
    fail "Syntax check skipped (missing): $f"
  fi
done

# ─────────────────────────────────────────────────────────────────
# 5. REST API NAMESPACE REGISTERED
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 REST API registration"

NS_EXISTS=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  do_action('rest_api_init');
  \$server = rest_get_server();
  \$namespaces = \$server->get_namespaces();
  echo in_array('tma-panel/v1', \$namespaces) ? '1' : '0';
" 2>/dev/null || echo "0")
[ "$NS_EXISTS" = "1" ] \
  && pass "Namespace tma-panel/v1 registered in WP" \
  || fail "Namespace tma-panel/v1 not registered"

# Check individual routes
ROUTES=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  do_action('rest_api_init');
  \$server = rest_get_server();
  \$routes = array_keys(\$server->get_routes('tma-panel/v1'));
  echo implode(',', \$routes);
" 2>/dev/null || echo "")

for endpoint in "/tma-panel/v1/dashboard" "/tma-panel/v1/documents" "/tma-panel/v1/leads" "/tma-panel/v1/notes" "/tma-panel/v1/audit" "/tma-panel/v1/export"; do
  echo "$ROUTES" | grep -q "$endpoint" \
    && pass "Route $endpoint registered" \
    || fail "Route $endpoint not registered"
done

# ─────────────────────────────────────────────────────────────────
# 6. AUTHENTICATION — UNAUTHENTICATED RETURNS 401
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🔒 Authentication checks"

UNAUTH_DASH=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  wp_set_current_user(0);
  \$request = new WP_REST_Request('GET', '/tma-panel/v1/dashboard');
  \$response = rest_do_request(\$request);
  echo \$response->get_status();
" 2>/dev/null || echo "0")
[ "$UNAUTH_DASH" = "401" ] \
  && pass "GET /dashboard unauthenticated → 401" \
  || fail "GET /dashboard unauthenticated → $UNAUTH_DASH (expected 401)"

UNAUTH_LEADS=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  wp_set_current_user(0);
  \$request = new WP_REST_Request('GET', '/tma-panel/v1/leads');
  \$response = rest_do_request(\$request);
  echo \$response->get_status();
" 2>/dev/null || echo "0")
[ "$UNAUTH_LEADS" = "401" ] \
  && pass "GET /leads unauthenticated → 401" \
  || fail "GET /leads unauthenticated → $UNAUTH_LEADS (expected 401)"

# ─────────────────────────────────────────────────────────────────
# 7. AUTHENTICATED — ADMIN GETS 200
# ─────────────────────────────────────────────────────────────────
echo ""
echo "👤 Authenticated admin access"

# Get a valid nonce for admin user
ADMIN_COOKIE=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  \$user = get_users(array('role' => 'administrator', 'number' => 1));
  if (empty(\$user)) { echo 'NO_ADMIN'; exit; }
  wp_set_current_user(\$user[0]->ID);
  \$nonce = wp_create_nonce('wp_rest');
  echo \$nonce;
" 2>/dev/null || echo "FAIL")

if [ "$ADMIN_COOKIE" != "FAIL" ] && [ "$ADMIN_COOKIE" != "NO_ADMIN" ]; then
  # Simulate authenticated REST request inside the container
  AUTH_DASHBOARD=$(docker exec "$WP_CONTAINER" php -r "
    require '/var/www/html/wp-load.php';
    \$user = get_users(array('role' => 'administrator', 'number' => 1));
    wp_set_current_user(\$user[0]->ID);
    \$request = new WP_REST_Request('GET', '/tma-panel/v1/dashboard');
    \$response = rest_do_request(\$request);
    echo \$response->get_status();
  " 2>/dev/null || echo "0")
  [ "$AUTH_DASHBOARD" = "200" ] \
    && pass "Admin GET /dashboard → 200" \
    || fail "Admin GET /dashboard → $AUTH_DASHBOARD (expected 200)"

  AUTH_DOCS=$(docker exec "$WP_CONTAINER" php -r "
    require '/var/www/html/wp-load.php';
    \$user = get_users(array('role' => 'administrator', 'number' => 1));
    wp_set_current_user(\$user[0]->ID);
    \$request = new WP_REST_Request('GET', '/tma-panel/v1/documents');
    \$response = rest_do_request(\$request);
    echo \$response->get_status();
  " 2>/dev/null || echo "0")
  [ "$AUTH_DOCS" = "200" ] \
    && pass "Admin GET /documents → 200" \
    || fail "Admin GET /documents → $AUTH_DOCS (expected 200)"

  AUTH_AUDIT=$(docker exec "$WP_CONTAINER" php -r "
    require '/var/www/html/wp-load.php';
    \$user = get_users(array('role' => 'administrator', 'number' => 1));
    wp_set_current_user(\$user[0]->ID);
    \$request = new WP_REST_Request('GET', '/tma-panel/v1/audit');
    \$response = rest_do_request(\$request);
    echo \$response->get_status();
  " 2>/dev/null || echo "0")
  [ "$AUTH_AUDIT" = "200" ] \
    && pass "Admin GET /audit → 200" \
    || fail "Admin GET /audit → $AUTH_AUDIT (expected 200)"
else
  fail "Admin cookie/nonce (could not get admin user)"
  fail "Admin GET /dashboard (skipped)"
  fail "Admin GET /documents (skipped)"
  fail "Admin GET /audit (skipped)"
fi

# ─────────────────────────────────────────────────────────────────
# 8. DASHBOARD RESPONSE SHAPE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📊 Dashboard response shape"

DASH_RESPONSE=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  \$user = get_users(array('role' => 'administrator', 'number' => 1));
  wp_set_current_user(\$user[0]->ID);
  \$request = new WP_REST_Request('GET', '/tma-panel/v1/dashboard');
  \$response = rest_do_request(\$request);
  echo json_encode(\$response->get_data());
" 2>/dev/null || echo "{}")

echo "$DASH_RESPONSE" | grep -q 'kpis\|leads\|docs' \
  && pass "Dashboard response has expected data keys" \
  || fail "Dashboard response missing expected keys"

# ─────────────────────────────────────────────────────────────────
# 9. NOTES POST ENDPOINT
# ─────────────────────────────────────────────────────────────────
echo ""
echo "📝 Notes POST endpoint"

NOTES_POST=$(docker exec "$WP_CONTAINER" php -r "
  require '/var/www/html/wp-load.php';
  \$user = get_users(array('role' => 'administrator', 'number' => 1));
  wp_set_current_user(\$user[0]->ID);
  \$request = new WP_REST_Request('POST', '/tma-panel/v1/notes');
  \$request->set_param('title', 'Test note');
  \$request->set_param('content', 'Test content from TDD');
  \$response = rest_do_request(\$request);
  echo \$response->get_status();
" 2>/dev/null || echo "0")
[ "$NOTES_POST" = "201" ] \
  && pass "POST /notes → 201 Created" \
  || fail "POST /notes → $NOTES_POST (expected 201)"

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
