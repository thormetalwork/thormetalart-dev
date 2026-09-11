---
description: "Promote changes from DEV to PROD: copy files, verify, smoke-test production. Use after verifying changes on dev.thormetalart.com."
agent: "devops"
---
# Promote DEV → PROD

## Pre-conditions
Before promoting, verify in DEV:
1. Run `make test-all` in `/srv/stacks/thormetalart-dev/` — all tests must pass
2. Visually verify the feature at `dev.thormetalart.com`
3. No pending uncommitted changes in DEV

## Promotion Steps

### 1. Identify changed files
List the files modified for this change (provide them as `$FILES`):
```bash
# Example: git diff --name-only main HEAD
```

### 2. Copy to PROD
For each modified file, copy from DEV to PROD:
```bash
# Theme file
cp /srv/stacks/thormetalart-dev/data/wordpress/wp-content/themes/thormetalart/<file> \
   /srv/stacks/thormetalart-prod/data/wordpress/wp-content/themes/thormetalart/<file>

# Plugin file
cp /srv/stacks/thormetalart-dev/data/wordpress/wp-content/plugins/tma-panel/<file> \
   /srv/stacks/thormetalart-prod/data/wordpress/wp-content/plugins/tma-panel/<file>

# Config/scripts
cp /srv/stacks/thormetalart-dev/<file> /srv/stacks/thormetalart-prod/<file>
```

### 3. Database migrations (if any)
If the change includes new migrations (`data/wordpress/wp-content/plugins/tma-panel/migrations/`):
- Apply migration manually via phpMyAdmin at `pma.thormetalart.com`
- Or run via WP CLI: `docker exec tma-prod-wordpress wp eval-file /path/to/migration.php`

### 4. Restart if needed
If Docker config or Dockerfile changed:
```bash
cd /srv/stacks/thormetalart-prod && make restart
```
Otherwise, WordPress file changes take effect immediately (no restart needed).

### 5. Smoke test PROD
```bash
curl -sI https://thormetalart.com | head -5
curl -sI https://panel.thormetalart.com | head -5
```
Verify:
- HTTP 200 responses
- No PHP fatal errors in `make logs-wp` (PROD)
- Key pages load: `/`, `/contacto`, `/portafolio`

## Report
After completing promotion:
- List files promoted
- Confirm smoke test results
- Note any manual steps taken (DB migrations, restarts)
- Flag anything that needs DNS or OAuth reconfiguration in PROD only
