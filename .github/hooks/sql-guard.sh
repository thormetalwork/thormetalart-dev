#!/bin/bash
# ══════════════════════════════════════════════════════════════
# SQL Guard Hook — PreToolUse
# ══════════════════════════════════════════════════════════════
# Blocks destructive SQL operations (DROP, TRUNCATE, DELETE without
# WHERE) from both terminal tools and MCP MySQL tools.
# Returns "ask" permission to require user confirmation.
# ══════════════════════════════════════════════════════════════
set -euo pipefail

INPUT=$(cat /dev/stdin 2>/dev/null || echo "")

# Extract tool name
TOOL=$(echo "$INPUT" | python3 -c "
import sys, json
try:
    d = json.load(sys.stdin)
    print(d.get('toolName', ''))
except:
    print('')
" 2>/dev/null || echo "")

# Only check terminal tools and MCP MySQL tools
if ! echo "$TOOL" | grep -qiE "execute|terminal|mcp_mysql"; then
    exit 0
fi

# Extract the command or query content
QUERY=$(echo "$INPUT" | python3 -c "
import sys, json
try:
    d = json.load(sys.stdin)
    inp = d.get('input', {})
    # Terminal tools use 'command', MCP MySQL uses 'query'
    print(inp.get('command', inp.get('query', '')))
except:
    print('')
" 2>/dev/null || echo "")

# Empty query — nothing to check
if [[ -z "$QUERY" ]]; then
    exit 0
fi

# Detect destructive SQL patterns (case-insensitive)
if echo "$QUERY" | grep -qiE "DROP\s+(TABLE|DATABASE|INDEX|VIEW)|TRUNCATE\s+TABLE|DELETE\s+FROM\s+\S+\s*$|DELETE\s+FROM\s+\S+\s*;|ALTER\s+TABLE\s+\S+\s+DROP"; then
    PATTERN=$(echo "$QUERY" | grep -oiE "DROP\s+(TABLE|DATABASE|INDEX|VIEW)|TRUNCATE\s+TABLE|DELETE\s+FROM|ALTER\s+TABLE\s+\S+\s+DROP" | head -1)
    cat <<EOF
{
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "ask",
    "permissionDecisionReason": "🛑 SQL Guard: Destructive operation detected (${PATTERN}). Requires explicit user confirmation. Consider running 'make backup' first."
  }
}
EOF
fi
