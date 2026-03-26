#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-PANEL-001 — Tests: Plugin scaffold + routing + Traefik
# TDD RED: Todos estos tests deben FALLAR antes de implementar
# ═══════════════════════════════════════════════════════════════════

PLUGIN_DIR="/srv/stacks/thormetalart/data/wordpress/wp-content/plugins/tma-panel"
COMPOSE="/srv/stacks/thormetalart/docker-compose.yml"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; ((PASS++)); }
fail() { echo "  ❌ FAIL: $1"; ((FAIL++)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-PANEL-001 — Plugin Scaffold Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ── Scenario 1: Plugin creado con estructura base ────────────────

echo "▸ Scenario 1: Plugin structure"

# Test: Plugin main file exists
[[ -f "$PLUGIN_DIR/tma-panel.php" ]] \
    && pass "tma-panel.php exists" \
    || fail "tma-panel.php missing"

# Test: Plugin has correct WP headers
if [[ -f "$PLUGIN_DIR/tma-panel.php" ]]; then
    grep -q "Plugin Name:" "$PLUGIN_DIR/tma-panel.php" \
        && pass "Plugin Name header present" \
        || fail "Plugin Name header missing"
    grep -q "Version:" "$PLUGIN_DIR/tma-panel.php" \
        && pass "Version header present" \
        || fail "Version header missing"
fi

# Test: Constants defined
if [[ -f "$PLUGIN_DIR/tma-panel.php" ]]; then
    grep -q "TMA_PANEL_VERSION" "$PLUGIN_DIR/tma-panel.php" \
        && pass "TMA_PANEL_VERSION defined" \
        || fail "TMA_PANEL_VERSION missing"
    grep -q "TMA_PANEL_PATH" "$PLUGIN_DIR/tma-panel.php" \
        && pass "TMA_PANEL_PATH defined" \
        || fail "TMA_PANEL_PATH missing"
    grep -q "TMA_PANEL_URL" "$PLUGIN_DIR/tma-panel.php" \
        && pass "TMA_PANEL_URL defined" \
        || fail "TMA_PANEL_URL missing"
    grep -q "TMA_PANEL_HOST" "$PLUGIN_DIR/tma-panel.php" \
        && pass "TMA_PANEL_HOST defined" \
        || fail "TMA_PANEL_HOST missing"
fi

# Test: Includes directory exists
[[ -d "$PLUGIN_DIR/includes" ]] \
    && pass "includes/ directory exists" \
    || fail "includes/ directory missing"

# Test: Templates directory exists
[[ -d "$PLUGIN_DIR/templates" ]] \
    && pass "templates/ directory exists" \
    || fail "templates/ directory missing"

echo ""

# ── Scenario 2: Router class ────────────────────────────────────

echo "▸ Scenario 2: Router class"

[[ -f "$PLUGIN_DIR/includes/class-tma-panel-router.php" ]] \
    && pass "Router class file exists" \
    || fail "Router class file missing"

if [[ -f "$PLUGIN_DIR/includes/class-tma-panel-router.php" ]]; then
    grep -q "class TMA_Panel_Router" "$PLUGIN_DIR/includes/class-tma-panel-router.php" \
        && pass "TMA_Panel_Router class defined" \
        || fail "TMA_Panel_Router class not defined"
    grep -q "send_security_headers" "$PLUGIN_DIR/includes/class-tma-panel-router.php" \
        && pass "Security headers method exists" \
        || fail "Security headers method missing"
    grep -q "is_panel_request" "$PLUGIN_DIR/includes/class-tma-panel-router.php" \
        && pass "is_panel_request method exists" \
        || fail "is_panel_request method missing"
fi

echo ""

# ── Scenario 3: Panel template ──────────────────────────────────

echo "▸ Scenario 3: Panel template (SPA shell)"

[[ -f "$PLUGIN_DIR/templates/panel.php" ]] \
    && pass "panel.php template exists" \
    || fail "panel.php template missing"

if [[ -f "$PLUGIN_DIR/templates/panel.php" ]]; then
    grep -q "TMA_PANEL" "$PLUGIN_DIR/templates/panel.php" \
        && pass "window.TMA_PANEL config present" \
        || fail "window.TMA_PANEL config missing"
    grep -q "tma-panel-app" "$PLUGIN_DIR/templates/panel.php" \
        && pass "SPA app container present" \
        || fail "SPA app container missing"
    grep -q "sidebar" "$PLUGIN_DIR/templates/panel.php" \
        && pass "Sidebar navigation present" \
        || fail "Sidebar navigation missing"
fi

echo ""

# ── Scenario 4: Traefik labels in docker-compose ────────────────

echo "▸ Scenario 4: Traefik labels for panel.thormetalart.com"

grep -q "panel.thormetalart.com" "$COMPOSE" \
    && pass "panel.thormetalart.com in docker-compose.yml" \
    || fail "panel.thormetalart.com NOT in docker-compose.yml"

echo ""

# ── Scenario 5: No interference with main site ──────────────────

echo "▸ Scenario 5: Non-interference verification"

if [[ -f "$PLUGIN_DIR/tma-panel.php" ]]; then
    grep -q "HTTP_HOST" "$PLUGIN_DIR/tma-panel.php" \
        && pass "Host check ensures panel-only activation" \
        || fail "No host check — may interfere with main site"
fi

echo ""

# ── Scenario 6: Plugin activates without errors (in WP) ────────

echo "▸ Scenario 6: Plugin activation (PHP syntax check)"

if [[ -f "$PLUGIN_DIR/tma-panel.php" ]]; then
    docker exec thormetalart_wordpress php -l "/var/www/html/wp-content/plugins/tma-panel/tma-panel.php" 2>&1 | grep -q "No syntax errors" \
        && pass "tma-panel.php — no syntax errors" \
        || fail "tma-panel.php — syntax error detected"
fi

if [[ -f "$PLUGIN_DIR/includes/class-tma-panel-router.php" ]]; then
    docker exec thormetalart_wordpress php -l "/var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-router.php" 2>&1 | grep -q "No syntax errors" \
        && pass "Router — no syntax errors" \
        || fail "Router — syntax error detected"
fi

if [[ -f "$PLUGIN_DIR/templates/panel.php" ]]; then
    docker exec thormetalart_wordpress php -l "/var/www/html/wp-content/plugins/tma-panel/templates/panel.php" 2>&1 | grep -q "No syntax errors" \
        && pass "Panel template — no syntax errors" \
        || fail "Panel template — syntax error detected"
fi

echo ""

# ── Summary ─────────────────────────────────────────────────────

echo "══════════════════════════════════════════════════"
echo " Results: $PASS passed, $FAIL failed"
echo "══════════════════════════════════════════════════"

[[ $FAIL -eq 0 ]] && echo "🟢 ALL TESTS PASS" || echo "🔴 $FAIL TESTS FAILING"
exit $FAIL
