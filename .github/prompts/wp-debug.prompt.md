---
description: "Toggle WP_DEBUG mode and analyze the WordPress error log for debugging issues"
agent: "devops"
---
Debug WordPress by managing WP_DEBUG and analyzing error logs:

1. **Check current debug state:**
   ```bash
   docker exec tma_dev_wordpress wp config get WP_DEBUG --allow-root
   docker exec tma_dev_wordpress wp config get WP_DEBUG_LOG --allow-root
   ```

2. **If enabling debug** (user wants to debug an issue):
   ```bash
   docker exec tma_dev_wordpress wp config set WP_DEBUG true --raw --allow-root
   docker exec tma_dev_wordpress wp config set WP_DEBUG_LOG true --raw --allow-root
   docker exec tma_dev_wordpress wp config set WP_DEBUG_DISPLAY false --raw --allow-root
   ```

3. **If disabling debug** (debugging complete):
   ```bash
   docker exec tma_dev_wordpress wp config set WP_DEBUG false --raw --allow-root
   docker exec tma_dev_wordpress wp config set WP_DEBUG_LOG false --raw --allow-root
   ```

4. **Analyze error log** (always check):
   ```bash
   docker exec tma_dev_wordpress bash -c "test -f /var/www/html/wp-content/debug.log && tail -100 /var/www/html/wp-content/debug.log || echo 'No debug.log found'"
   ```

5. **Summarize findings:**
   - Group errors by type (Fatal, Warning, Notice, Deprecated)
   - Identify the source file and line for each unique error
   - Highlight tma-panel plugin errors vs core/third-party
   - Suggest fixes for the most critical issues

6. **Report:** Current WP_DEBUG state, error count by severity, and top issues with file locations
