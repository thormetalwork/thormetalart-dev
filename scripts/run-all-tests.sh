#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════
# Thor Metal Art — Master Test Runner
# Runs all test suites or a specific scope and produces a summary
# Usage: bash scripts/run-all-tests.sh [all|panel|dash|lead|portal|docker]
# ═══════════════════════════════════════════════════════════════════

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "${SCRIPT_DIR}")"
TESTS_DIR="${PROJECT_DIR}/tests"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

TOTAL_SUITES=0
PASSED_SUITES=0
FAILED_SUITES=0
SKIPPED_SUITES=0
FAILED_FILES=()

# Parse scope argument
SCOPE="${1:-all}"
case "${SCOPE}" in
    all)     PATTERN="test-*.sh" ;;
    panel)   PATTERN="test-panel-*.sh" ;;
    dash)    PATTERN="test-dash-*.sh" ;;
    lead)    PATTERN="test-lead-*.sh" ;;
    portal)  PATTERN="test-portal-*.sh" ;;
    docker)  PATTERN="test-dock-*.sh" ;;
    *)
        echo -e "${RED}Unknown scope: ${SCOPE}${NC}"
        echo "Usage: $0 [all|panel|dash|lead|portal|docker]"
        exit 2
        ;;
esac

echo ""
echo -e "${BLUE}══════════════════════════════════════════════════${NC}"
echo -e "${BLUE} Thor Metal Art — Test Runner (scope: ${SCOPE})${NC}"
echo -e "${BLUE}══════════════════════════════════════════════════${NC}"
echo ""

# Pre-flight: Stack connectivity check
echo -e "${YELLOW}▸ Pre-flight: Stack connectivity${NC}"
if bash "${PROJECT_DIR}/scripts/test-connections.sh" > /dev/null 2>&1; then
    echo -e "  ${GREEN}✅ Stack is healthy${NC}"
    STACK_UP=true
else
    echo -e "  ${YELLOW}⚠️  Stack may not be fully running — some tests may fail${NC}"
    STACK_UP=false
fi
echo ""

# Find and run test files
for TEST_FILE in "${TESTS_DIR}"/${PATTERN}; do
    [[ -f "${TEST_FILE}" ]] || continue
    TOTAL_SUITES=$((TOTAL_SUITES + 1))
    TEST_NAME="$(basename "${TEST_FILE}" .sh)"

    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}▸ ${TEST_NAME}${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

    # Run the test, capture exit code
    set +e
    bash "${TEST_FILE}" 2>&1
    EXIT_CODE=$?
    set -e

    if [[ ${EXIT_CODE} -eq 0 ]]; then
        PASSED_SUITES=$((PASSED_SUITES + 1))
        echo -e "  ${GREEN}→ SUITE PASSED${NC}"
    else
        FAILED_SUITES=$((FAILED_SUITES + 1))
        FAILED_FILES+=("${TEST_NAME}")
        echo -e "  ${RED}→ SUITE FAILED (exit ${EXIT_CODE})${NC}"
    fi
    echo ""
done

# Summary
echo -e "${BLUE}══════════════════════════════════════════════════${NC}"
echo -e "${BLUE} SUMMARY — ${SCOPE}${NC}"
echo -e "${BLUE}══════════════════════════════════════════════════${NC}"
echo -e "  Total suites:  ${TOTAL_SUITES}"
echo -e "  ${GREEN}Passed:        ${PASSED_SUITES}${NC}"
echo -e "  ${RED}Failed:        ${FAILED_SUITES}${NC}"

if [[ ${FAILED_SUITES} -gt 0 ]]; then
    echo ""
    echo -e "${RED}Failed suites:${NC}"
    for f in "${FAILED_FILES[@]}"; do
        echo -e "  ${RED}✗ ${f}${NC}"
    done
    echo ""
    echo -e "${RED}🔴 ${FAILED_SUITES} suite(s) failed${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}🟢 All ${PASSED_SUITES} suite(s) passed!${NC}"
exit 0
