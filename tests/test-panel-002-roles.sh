#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# TICKET-PANEL-002 — Tests: Roles y capabilities
# ═══════════════════════════════════════════════════════════════════

PLUGIN_DIR="/srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel"
PASS=0
FAIL=0

pass() { echo "  ✅ PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  ❌ FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "══════════════════════════════════════════════════"
echo " TICKET-PANEL-002 — Roles & Capabilities Tests"
echo "══════════════════════════════════════════════════"
echo ""

# ── Scenario 1: Roles class file ────────────────────────────────

echo "▸ Scenario 1: Roles class file"

[[ -f "$PLUGIN_DIR/includes/class-tma-panel-roles.php" ]] \
    && pass "Roles class file exists" \
    || fail "Roles class file missing"

if [[ -f "$PLUGIN_DIR/includes/class-tma-panel-roles.php" ]]; then
    grep -q "class TMA_Panel_Roles" "$PLUGIN_DIR/includes/class-tma-panel-roles.php" \
        && pass "TMA_Panel_Roles class defined" \
        || fail "TMA_Panel_Roles class not defined"
    grep -q "tma_admin" "$PLUGIN_DIR/includes/class-tma-panel-roles.php" \
        && pass "tma_admin role referenced" \
        || fail "tma_admin role not found"
    grep -q "tma_client" "$PLUGIN_DIR/includes/class-tma-panel-roles.php" \
        && pass "tma_client role referenced" \
        || fail "tma_client role not found"
fi

echo ""

# ── Scenario 2: Capabilities defined ────────────────────────────

echo "▸ Scenario 2: Capabilities"

if [[ -f "$PLUGIN_DIR/includes/class-tma-panel-roles.php" ]]; then
    for cap in tma_view_panel tma_manage_docs tma_manage_leads tma_manage_notes tma_view_audit tma_export tma_manage_kpis tma_toggle_visibility; do
        grep -q "$cap" "$PLUGIN_DIR/includes/class-tma-panel-roles.php" \
            && pass "Capability $cap defined" \
            || fail "Capability $cap missing"
    done
fi

echo ""

# ── Scenario 3: Roles registered in WordPress ───────────────────

echo "▸ Scenario 3: Roles in WordPress DB"

TMA_ADMIN_EXISTS=$(docker exec thormetalart_wordpress php -r "
require '/var/www/html/wp-load.php';
\$role = get_role('tma_admin');
echo \$role ? 'yes' : 'no';
" 2>/dev/null)

TMA_CLIENT_EXISTS=$(docker exec thormetalart_wordpress php -r "
require '/var/www/html/wp-load.php';
\$role = get_role('tma_client');
echo \$role ? 'yes' : 'no';
" 2>/dev/null)

[[ "$TMA_ADMIN_EXISTS" == "yes" ]] \
    && pass "tma_admin role exists in WP" \
    || fail "tma_admin role NOT in WP"

[[ "$TMA_CLIENT_EXISTS" == "yes" ]] \
    && pass "tma_client role exists in WP" \
    || fail "tma_client role NOT in WP"

echo ""

# ── Scenario 4: tma_admin has all capabilities ──────────────────

echo "▸ Scenario 4: tma_admin capabilities"

ADMIN_CAPS=$(docker exec thormetalart_wordpress php -r "
require '/var/www/html/wp-load.php';
\$role = get_role('tma_admin');
if (\$role) {
    \$caps = array_keys(array_filter(\$role->capabilities));
    sort(\$caps);
    echo implode(',', \$caps);
}
" 2>/dev/null)

for cap in tma_view_panel tma_manage_docs tma_manage_leads tma_manage_notes tma_view_audit tma_export tma_manage_kpis tma_toggle_visibility; do
    echo "$ADMIN_CAPS" | grep -q "$cap" \
        && pass "tma_admin has $cap" \
        || fail "tma_admin missing $cap"
done

echo ""

# ── Scenario 5: tma_client restricted capabilities ──────────────

echo "▸ Scenario 5: tma_client restrictions"

CLIENT_CAPS=$(docker exec thormetalart_wordpress php -r "
require '/var/www/html/wp-load.php';
\$role = get_role('tma_client');
if (\$role) {
    \$caps = array_keys(array_filter(\$role->capabilities));
    sort(\$caps);
    echo implode(',', \$caps);
}
" 2>/dev/null)

# Should have these
for cap in tma_view_panel tma_manage_docs tma_manage_leads tma_manage_notes tma_export; do
    echo "$CLIENT_CAPS" | grep -q "$cap" \
        && pass "tma_client has $cap" \
        || fail "tma_client missing $cap"
done

# Should NOT have these
for cap in tma_view_audit tma_toggle_visibility tma_manage_kpis; do
    echo "$CLIENT_CAPS" | grep -q "$cap" \
        && fail "tma_client should NOT have $cap" \
        || pass "tma_client correctly lacks $cap"
done

echo ""

# ── Scenario 6: Plugin includes roles file ──────────────────────

echo "▸ Scenario 6: Plugin bootstrap loads roles"

if [[ -f "$PLUGIN_DIR/tma-panel.php" ]]; then
    grep -q "class-tma-panel-roles" "$PLUGIN_DIR/tma-panel.php" \
        && pass "tma-panel.php loads roles class" \
        || fail "tma-panel.php does NOT load roles class"
fi

echo ""

# ── Scenario 7: PHP syntax check ────────────────────────────────

echo "▸ Scenario 7: Syntax check"

if [[ -f "$PLUGIN_DIR/includes/class-tma-panel-roles.php" ]]; then
    docker exec thormetalart_wordpress php -l "/var/www/html/wp-content/plugins/tma-panel/includes/class-tma-panel-roles.php" 2>&1 | grep -q "No syntax errors" \
        && pass "Roles class — no syntax errors" \
        || fail "Roles class — syntax error"
fi

echo ""

# ── Summary ─────────────────────────────────────────────────────

echo "══════════════════════════════════════════════════"
echo " Results: $PASS passed, $FAIL failed"
echo "══════════════════════════════════════════════════"

[[ $FAIL -eq 0 ]] && echo "🟢 ALL TESTS PASS" || echo "🔴 $FAIL TESTS FAILING"
exit $FAIL
