#!/usr/bin/env bash
# test-wp-039-blog-seed-posts.sh
# TDD tests for TICKET-WP-039: 12 seed blog posts (Karel Frometa)
# Usage: bash tests/test-wp-039-blog-seed-posts.sh

set -euo pipefail

PASS=0
FAIL=0
BASE_URL="${WP_BASE_URL:-https://dev.thormetalart.com}"

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

pass() { echo -e "${GREEN}[PASS]${NC} $1"; PASS=$((PASS+1)); }
fail() { echo -e "${RED}[FAIL]${NC} $1"; FAIL=$((FAIL+1)); }
info() { echo -e "${YELLOW}[INFO]${NC} $1"; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="${SCRIPT_DIR}/../.env"
if [ -f "$ENV_FILE" ]; then
  MYSQL_USER=$(grep '^MYSQL_USER=' "$ENV_FILE" | cut -d'=' -f2-)
  MYSQL_PASSWORD=$(grep '^MYSQL_PASSWORD=' "$ENV_FILE" | cut -d'=' -f2-)
  MYSQL_DATABASE=$(grep '^MYSQL_DATABASE=' "$ENV_FILE" | cut -d'=' -f2-)
else
  echo "ERROR: .env not found at $ENV_FILE" >&2
  exit 1
fi

mysql_q() {
  docker exec tma_dev_mysql mysql -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" \
    --skip-column-names -e "$1" 2>/dev/null | tr -d '\r'
}

echo "================================================"
echo " TICKET-WP-039: Blog Seed Posts — TDD Tests"
echo "================================================"
echo ""

# -------------------------------------------------------
# TEST 1: At least the 12 seed posts exist; editorial posts may be added later.
# -------------------------------------------------------
info "TEST 1: At least 12 published posts exist in DB"
COUNT=$(mysql_q "SELECT COUNT(*) FROM tma_posts WHERE post_type='post' AND post_status='publish';")
if [ "$COUNT" -ge 12 ]; then
  pass "At least 12 published posts found (got $COUNT)"
else
  fail "Expected at least 12 published posts, got $COUNT"
fi

# -------------------------------------------------------
# TEST 2: All posts authored by Karel Frometa (ID=3)
# -------------------------------------------------------
info "TEST 2: All posts authored by Karel Frometa (user_id=3)"
NON_KAREL=$(mysql_q "SELECT COUNT(*) FROM tma_posts WHERE post_type='post' AND post_status='publish' AND post_author != 3;")
if [ "$NON_KAREL" -eq 0 ]; then
  pass "All posts authored by Karel Frometa (ID=3)"
else
  fail "Found $NON_KAREL posts NOT authored by Karel Frometa"
fi

# -------------------------------------------------------
# TEST 3: Posts have non-empty excerpts
# -------------------------------------------------------
info "TEST 3: All posts have non-empty post_excerpt"
EMPTY_EX=$(mysql_q "SELECT COUNT(*) FROM tma_posts WHERE post_type='post' AND post_status='publish' AND (post_excerpt IS NULL OR post_excerpt='');")
if [ "$EMPTY_EX" -eq 0 ]; then
  pass "All 12 posts have non-empty excerpts"
else
  fail "Found $EMPTY_EX posts with empty excerpts"
fi

# -------------------------------------------------------
# TEST 4: Posts have non-empty slugs (post_name)
# -------------------------------------------------------
info "TEST 4: All posts have non-empty post_name (slug)"
EMPTY_SLUG=$(mysql_q "SELECT COUNT(*) FROM tma_posts WHERE post_type='post' AND post_status='publish' AND (post_name IS NULL OR post_name='');")
if [ "$EMPTY_SLUG" -eq 0 ]; then
  pass "All 12 posts have slugs"
else
  fail "Found $EMPTY_SLUG posts without slugs"
fi

# -------------------------------------------------------
# TEST 5–9: Category distribution (expected posts per cat)
# fabrication=4, design-ideas=3, miami-projects=3, care-tips=1, metal-art=1
# -------------------------------------------------------
info "TEST 5: fabrication category has 4 posts"
FAB_COUNT=$(mysql_q "SELECT COUNT(*) FROM tma_posts p JOIN tma_term_relationships tr ON p.ID=tr.object_id WHERE p.post_type='post' AND p.post_status='publish' AND tr.term_taxonomy_id=10;")
if [ "$FAB_COUNT" -ge 4 ]; then
  pass "fabrication: at least 4 posts (got $FAB_COUNT)"
else
  fail "fabrication: expected at least 4, got $FAB_COUNT"
fi

info "TEST 6: design-ideas category has 3 posts"
DI_COUNT=$(mysql_q "SELECT COUNT(*) FROM tma_posts p JOIN tma_term_relationships tr ON p.ID=tr.object_id WHERE p.post_type='post' AND p.post_status='publish' AND tr.term_taxonomy_id=11;")
if [ "$DI_COUNT" -ge 3 ]; then
  pass "design-ideas: at least 3 posts (got $DI_COUNT)"
else
  fail "design-ideas: expected at least 3, got $DI_COUNT"
fi

info "TEST 7: miami-projects category has 3 posts"
MP_COUNT=$(mysql_q "SELECT COUNT(*) FROM tma_posts p JOIN tma_term_relationships tr ON p.ID=tr.object_id WHERE p.post_type='post' AND p.post_status='publish' AND tr.term_taxonomy_id=12;")
if [ "$MP_COUNT" -ge 3 ]; then
  pass "miami-projects: at least 3 posts (got $MP_COUNT)"
else
  fail "miami-projects: expected at least 3, got $MP_COUNT"
fi

info "TEST 8: care-tips category has 1 post"
CT_COUNT=$(mysql_q "SELECT COUNT(*) FROM tma_posts p JOIN tma_term_relationships tr ON p.ID=tr.object_id WHERE p.post_type='post' AND p.post_status='publish' AND tr.term_taxonomy_id=13;")
if [ "$CT_COUNT" -eq 1 ]; then
  pass "care-tips: 1 post"
else
  fail "care-tips: expected 1, got $CT_COUNT"
fi

info "TEST 9: metal-art category has 1 post"
MA_COUNT=$(mysql_q "SELECT COUNT(*) FROM tma_posts p JOIN tma_term_relationships tr ON p.ID=tr.object_id WHERE p.post_type='post' AND p.post_status='publish' AND tr.term_taxonomy_id=14;")
if [ "$MA_COUNT" -eq 1 ]; then
  pass "metal-art: 1 post"
else
  fail "metal-art: expected 1, got $MA_COUNT"
fi

# -------------------------------------------------------
# TEST 10–21: Specific post slugs exist
# -------------------------------------------------------
check_post_slug() {
  local slug="$1"
  local label="$2"
  info "TEST: Post slug '$slug' exists"
  FOUND=$(mysql_q "SELECT COUNT(*) FROM tma_posts WHERE post_type='post' AND post_status='publish' AND post_name='$slug';")
  if [ "$FOUND" -eq 1 ]; then
    pass "$label — slug '$slug' found"
  else
    fail "$label — slug '$slug' NOT found (got $FOUND)"
  fi
}

check_post_slug "custom-metal-gate-cost-miami"           "Post 1: gate cost"
check_post_slug "metal-gate-styles-miami-climate"        "Post 2: gate styles"
check_post_slug "steel-vs-aluminum-gates-miami"          "Post 3: steel vs aluminum"
check_post_slug "miami-metalwork-project-design-install" "Post 4: project design"
check_post_slug "maintain-metal-railings-south-florida"  "Post 5: railings care"
check_post_slug "custom-metal-furniture-ideas"           "Post 6: furniture ideas"
check_post_slug "metal-sculpture-commissioning"          "Post 7: sculpture"
check_post_slug "miami-architects-custom-metalwork"      "Post 8: architects"
check_post_slug "water-jet-cutting-vs-plasma-cutting"    "Post 9: cutting methods"
check_post_slug "right-metal-fence-miami-property"       "Post 10: metal fence"
check_post_slug "tig-welding-structural-decorative"      "Post 11: TIG welding"
check_post_slug "custom-metal-gate-process"              "Post 12: gate process"

# -------------------------------------------------------
# TEST 22: Posts have substantial content (>200 chars)
# -------------------------------------------------------
info "TEST 22: All posts have substantial content (>200 chars)"
SHORT_CONTENT=$(mysql_q "SELECT COUNT(*) FROM tma_posts WHERE post_type='post' AND post_status='publish' AND CHAR_LENGTH(post_content) < 200;")
if [ "$SHORT_CONTENT" -eq 0 ]; then
  pass "All 12 posts have substantial content"
else
  fail "Found $SHORT_CONTENT posts with less than 200 chars content"
fi

# -------------------------------------------------------
# TEST 23–24: HTTP — /blog/ and /fabrication/ return 200
# -------------------------------------------------------
info "TEST 23: HTTP — /blog/ returns 200"
HTTP_BLOG=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "${BASE_URL}/blog/")
if [ "$HTTP_BLOG" -eq 200 ]; then
  pass "/blog/ → HTTP 200"
else
  fail "/blog/ → HTTP $HTTP_BLOG (expected 200)"
fi

info "TEST 24: HTTP — /fabrication/ archive returns 200"
HTTP_FAB=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "${BASE_URL}/fabrication/")
if [ "$HTTP_FAB" -eq 200 ]; then
  pass "/fabrication/ → HTTP 200"
else
  fail "/fabrication/ → HTTP $HTTP_FAB (expected 200)"
fi

# -------------------------------------------------------
# TEST 25: HTTP — post permalink returns 200
# -------------------------------------------------------
info "TEST 25: HTTP — individual post permalink returns 200"
HTTP_POST=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "${BASE_URL}/fabrication/custom-metal-gate-cost-miami/")
if [ "$HTTP_POST" -eq 200 ]; then
  pass "/fabrication/custom-metal-gate-cost-miami/ → HTTP 200"
else
  fail "/fabrication/custom-metal-gate-cost-miami/ → HTTP $HTTP_POST (expected 200)"
fi

# -------------------------------------------------------
# TEST 26: Blog listing shows posts (not empty state)
# -------------------------------------------------------
info "TEST 26: /blog/ response does not contain 'No posts found'"
BLOG_BODY=$(curl -s --max-time 10 "${BASE_URL}/blog/")
if echo "$BLOG_BODY" | grep -qi "no posts found"; then
  fail "/blog/ shows 'No posts found' — posts not rendering"
else
  pass "/blog/ response does not contain 'No posts found'"
fi

# -------------------------------------------------------
# Summary
# -------------------------------------------------------
echo ""
echo "================================================"
echo " Results: ${PASS} passed, ${FAIL} failed"
echo "================================================"

if [ "$FAIL" -gt 0 ]; then
  exit 1
fi
exit 0
