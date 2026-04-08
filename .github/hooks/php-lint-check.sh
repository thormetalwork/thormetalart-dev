#!/usr/bin/env bash
# PostToolUse hook: Run php -l on PHP files edited via agent tools
# Called by .github/hooks/safety-checks.json PostToolUse event
# Reads JSON from stdin, checks if a PHP file was edited, runs syntax check
set -e

INPUT=$(cat /dev/stdin 2>/dev/null || echo "")
TOOL=$(echo "$INPUT" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('toolName',''))" 2>/dev/null || echo "")

# Only trigger on edit tools
if ! echo "$TOOL" | grep -qiE "edit|replace|create_file"; then
  exit 0
fi

# Extract file path from input
FILE=$(echo "$INPUT" | python3 -c "
import sys, json
d = json.load(sys.stdin)
i = d.get('input', {})
print(i.get('filePath', i.get('file', '')))
" 2>/dev/null || echo "")

# Only check PHP files
if [[ ! "$FILE" =~ \.php$ ]]; then
  exit 0
fi

# Run lint inside WordPress container if file is in data/wordpress/
if [[ "$FILE" =~ data/wordpress/ ]]; then
  CONTAINER_PATH="${FILE#*data/wordpress/}"
  RESULT=$(docker exec tma_dev_wordpress php -l "/var/www/html/$CONTAINER_PATH" 2>&1 || true)
  if echo "$RESULT" | grep -qi "parse error\|fatal error"; then
    echo "{\"systemMessage\":\"⚠️ PHP Syntax Error in $CONTAINER_PATH:\\n$RESULT\"}"
  fi
elif [[ -f "$FILE" ]]; then
  RESULT=$(php -l "$FILE" 2>&1 || true)
  if echo "$RESULT" | grep -qi "parse error\|fatal error"; then
    echo "{\"systemMessage\":\"⚠️ PHP Syntax Error in $FILE:\\n$RESULT\"}"
  fi
fi
