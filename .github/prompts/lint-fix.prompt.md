---
description: "Run all linters and auto-fix formatting issues across the codebase"
agent: "devops"
---
Run comprehensive code quality checks and auto-fix what's possible:

1. **Auto-fix formatting** (safe — modifies files):
   ```bash
   make format
   ```

2. **Run all linters** (read-only check):
   ```bash
   make lint
   ```

3. **Summarize results** per category:
   - PHP syntax errors (blocking)
   - WPCS violations (warnings vs errors)
   - ESLint issues (fixable vs manual)
   - Prettier format drift (should be zero after step 1)
   - PHPStan static analysis findings

4. For any remaining issues, list them with file:line and severity

5. If all clean, confirm: "All linters passed — ready for commit"
