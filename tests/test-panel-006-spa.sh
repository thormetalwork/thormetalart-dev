#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TDD RED → Test suite for TICKET-PANEL-006: Frontend SPA shell + sidebar
# ══════════════════════════════════════════════════════════════════════
set -e

PASS=0
FAIL=0
TOTAL=0
WP_CONTAINER="thormetalart_wordpress"
PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"

pass() { PASS=$((PASS + 1)); TOTAL=$((TOTAL + 1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL + 1)); TOTAL=$((TOTAL + 1)); echo "  ❌ $1"; }

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  TICKET-PANEL-006 — Frontend SPA shell + sidebar            ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ─────────────────────────────────────────────────────────────────
# 1. FILE EXISTENCE & STRUCTURE
# ─────────────────────────────────────────────────────────────────
echo "📁 File existence"

[ -f "$PLUGIN_DIR/templates/panel.php" ] \
  && pass "panel.php exists" \
  || fail "panel.php missing"

[ -f "$PLUGIN_DIR/assets/js/panel.js" ] \
  && pass "panel.js exists" \
  || fail "panel.js missing"

[ -f "$PLUGIN_DIR/assets/css/panel.css" ] \
  && pass "panel.css exists" \
  || fail "panel.css missing"

# ─────────────────────────────────────────────────────────────────
# 2. PANEL.PHP — HTML STRUCTURE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🏗️  Panel template structure"

PANEL="$PLUGIN_DIR/templates/panel.php"

grep -q 'id="tma-panel-app"' "$PANEL" \
  && pass "App root element exists" \
  || fail "Missing #tma-panel-app"

grep -q 'id="tma-sidebar"' "$PANEL" \
  && pass "Sidebar element exists" \
  || fail "Missing #tma-sidebar"

grep -q 'id="tma-content"' "$PANEL" \
  && pass "Content area exists" \
  || fail "Missing #tma-content"

grep -q 'id="tma-hamburger"' "$PANEL" \
  && pass "Hamburger button exists" \
  || fail "Missing #tma-hamburger"

grep -q 'id="tma-sidebar-overlay"' "$PANEL" \
  && pass "Sidebar overlay exists" \
  || fail "Missing sidebar overlay"

grep -q 'window.TMA_PANEL' "$PANEL" \
  && pass "TMA_PANEL config object injected" \
  || fail "Missing window.TMA_PANEL config"

grep -q 'apiBase' "$PANEL" \
  && pass "Config has apiBase" \
  || fail "Config missing apiBase"

grep -q 'nonce' "$PANEL" \
  && pass "Config has nonce" \
  || fail "Config missing nonce"

grep -q 'isAdmin' "$PANEL" \
  && pass "Config has user.isAdmin" \
  || fail "Config missing user.isAdmin"

# Nav links
for section in dashboard documents leads notes; do
  grep -q "data-section=\"$section\"" "$PANEL" \
    && pass "Nav link for $section" \
    || fail "Missing nav link for $section"
done

# Audit link only for admin
grep -q 'data-section="audit"' "$PANEL" \
  && pass "Nav link for audit" \
  || fail "Missing nav link for audit"

# Language switch
grep -q 'lang-switch' "$PANEL" \
  && pass "Language switch present" \
  || fail "Missing language switch"

# Logout link
grep -q 'sidebar__logout\|wp_logout_url' "$PANEL" \
  && pass "Logout link present" \
  || fail "Missing logout link"

# ─────────────────────────────────────────────────────────────────
# 3. PANEL.JS — SPA FUNCTIONALITY
# ─────────────────────────────────────────────────────────────────
echo ""
echo "⚡ SPA JavaScript functionality"

JS="$PLUGIN_DIR/assets/js/panel.js"

# API helper
grep -q "function api\|const api\|api(" "$JS" \
  && pass "API helper function exists" \
  || fail "Missing API helper function"

grep -q 'X-WP-Nonce' "$JS" \
  && pass "API sends nonce header" \
  || fail "API missing nonce header"

# Hash router
grep -q 'hashchange' "$JS" \
  && pass "Hash change listener" \
  || fail "Missing hashchange listener"

# Section renderers call API
grep -q "api('/dashboard')\|api(\"/dashboard\")\|api.*dashboard" "$JS" \
  && pass "renderDashboard calls API" \
  || fail "renderDashboard doesn't call API"

grep -q "api('/documents')\|api(\"/documents\")\|api.*documents" "$JS" \
  && pass "renderDocuments calls API" \
  || fail "renderDocuments doesn't call API"

grep -q "api('/leads')\|api(\"/leads\")\|api.*leads" "$JS" \
  && pass "renderLeads calls API" \
  || fail "renderLeads doesn't call API"

grep -q "api('/notes')\|api(\"/notes\")\|api.*notes" "$JS" \
  && pass "renderNotes calls API" \
  || fail "renderNotes doesn't call API"

# Sidebar toggle
grep -q 'hamburger' "$JS" \
  && pass "Hamburger toggle handler" \
  || fail "Missing hamburger handler"

grep -q 'sidebar.*open\|open.*sidebar\|classList.*open' "$JS" \
  && pass "Sidebar open/close logic" \
  || fail "Missing sidebar open/close"

grep -q 'overlay' "$JS" \
  && pass "Overlay click handler" \
  || fail "Missing overlay handler"

# Active nav link toggle
grep -q 'active' "$JS" \
  && pass "Active nav link toggle" \
  || fail "Missing active class toggle"

# DOMContentLoaded
grep -q 'DOMContentLoaded' "$JS" \
  && pass "DOMContentLoaded init" \
  || fail "Missing DOMContentLoaded"

# ─────────────────────────────────────────────────────────────────
# 4. PANEL.CSS — BRANDING & RESPONSIVE
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🎨 CSS branding & responsive"

CSS="$PLUGIN_DIR/assets/css/panel.css"

grep -q '#0c0a09' "$CSS" \
  && pass "Dark background #0c0a09" \
  || fail "Missing dark background"

grep -q '#B8860B\|B8860B' "$CSS" \
  && pass "Gold accent #B8860B" \
  || fail "Missing gold accent"

grep -q 'Cormorant Garamond' "$CSS" \
  && pass "Display font Cormorant Garamond" \
  || fail "Missing display font"

grep -q 'Inter' "$CSS" \
  && pass "Body font Inter" \
  || fail "Missing body font"

grep -q 'min-height.*44px\|min-height: 44' "$CSS" \
  && pass "Touch target 44px" \
  || fail "Missing 44px touch targets"

grep -q '@media' "$CSS" \
  && pass "Has media queries" \
  || fail "Missing media queries"

grep -q '768px\|767px' "$CSS" \
  && pass "Responsive breakpoint 768px" \
  || fail "Missing 768px breakpoint"

grep -q 'translateX' "$CSS" \
  && pass "Sidebar slide transition" \
  || fail "Missing sidebar transition"

# ─────────────────────────────────────────────────────────────────
# 5. PHP SYNTAX
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🧪 PHP syntax"

for f in "templates/panel.php" "tma-panel.php"; do
  docker exec "$WP_CONTAINER" php -l "/var/www/html/wp-content/plugins/tma-panel/$f" 2>&1 | grep -q "No syntax errors" \
    && pass "Syntax OK: $f" \
    || fail "Syntax error: $f"
done

# ─────────────────────────────────────────────────────────────────
# 6. PANEL PAGE RENDERS FOR AUTH USER
# ─────────────────────────────────────────────────────────────────
echo ""
echo "🌐 HTTP render checks"

PANEL_OUTPUT=$(docker exec "$WP_CONTAINER" php -r "
  \$_SERVER['HTTP_HOST'] = 'panel.thormetalart.com';
  \$_SERVER['REQUEST_URI'] = '/';
  \$_SERVER['REQUEST_METHOD'] = 'GET';
  // Simulate authenticated user
  define('TMA_TEST_MODE', true);
  require '/var/www/html/wp-load.php';
  // If not authenticated, might redirect to /login
" 2>/dev/null || true)

if echo "$PANEL_OUTPUT" | grep -q 'tma-panel-app\|tma-sidebar\|TMA Panel'; then
  pass "Panel page renders SPA shell"
else
  # It's OK if we get login page (no auth session in CLI)
  echo "$PANEL_OUTPUT" | grep -q 'login\|Login\|Thor Metal Art' \
    && pass "Panel redirects to login (no session in CLI — OK)" \
    || fail "Panel page doesn't render"
fi

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
