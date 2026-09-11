#!/bin/bash
# Format-on-save hook — auto-formats JS/CSS files after agent edits
# Called as PostToolUse hook; receives JSON on stdin

set -euo pipefail

INPUT=$(cat /dev/stdin 2>/dev/null || echo "")

# Only trigger on file-edit tools
TOOL=$(echo "$INPUT" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('toolName',''))" 2>/dev/null || echo "")

if ! echo "$TOOL" | grep -qiE "create_file|replace_string|edit|multi_replace"; then
  exit 0
fi

# Extract file path from tool input
FILE_PATH=$(echo "$INPUT" | python3 -c "
import sys, json
d = json.load(sys.stdin)
i = d.get('input', {})
# Try common parameter names for file path
for key in ['filePath', 'file_path', 'path']:
    if key in i:
        print(i[key])
        sys.exit(0)
# For multi_replace, get first replacement's file
replacements = i.get('replacements', [])
if replacements:
    print(replacements[0].get('filePath', ''))
    sys.exit(0)
print('')
" 2>/dev/null || echo "")

if [[ -z "$FILE_PATH" ]]; then
  exit 0
fi

# Only format files in our project
if [[ "$FILE_PATH" != /srv/stacks/thormetalart-dev/* ]]; then
  exit 0
fi

# Check file extension and format accordingly
case "$FILE_PATH" in
  *.js|*.mjs|*.css|*.json)
    # Run Prettier if available
    if command -v npx &>/dev/null && [[ -f /srv/stacks/thormetalart-dev/node_modules/.bin/prettier ]]; then
      cd /srv/stacks/thormetalart-dev
      npx prettier --write "$FILE_PATH" 2>/dev/null && \
        echo "{\"systemMessage\":\"✨ Auto-formatted $(basename "$FILE_PATH") with Prettier.\"}" || true
    fi
    ;;
  *.php)
    # PHP syntax check (formatting handled by php-lint-check.sh already)
    # Only add PHPCS fix if phpcbf is available
    if command -v vendor/bin/phpcbf &>/dev/null || [[ -f /srv/stacks/thormetalart-dev/vendor/bin/phpcbf ]]; then
      cd /srv/stacks/thormetalart-dev
      vendor/bin/phpcbf --standard=WordPress "$FILE_PATH" 2>/dev/null && \
        echo "{\"systemMessage\":\"✨ Auto-fixed $(basename "$FILE_PATH") with PHPCBF (WordPress standards).\"}" || true
    fi
    ;;
esac

exit 0
