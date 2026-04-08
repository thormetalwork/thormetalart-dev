---
description: "Migrate the MySQL database between environments with safety checks and rollback plan."
agent: "devops"
---
Migrate the Thor Metal Art database between environments.

## Pre-Flight Checklist
1. Verify source database is accessible: `make test`
2. Create a fresh backup BEFORE migration: `make backup`
3. Confirm the backup file exists in `backups/` and note its filename
4. Verify target environment `.env` has correct credentials

## Export
5. Export the database from source:
   ```bash
   docker exec tma_dev_mysql mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" thormetalart_wp | gzip > backups/migration-$(date +%Y%m%d-%H%M%S).sql.gz
   ```

## Import
6. On the target environment, restore using `scripts/restore-database.sh <backup-file>`
7. After import, verify table count and `tma_` prefix consistency
8. Flush Redis cache on target: `scripts/clear-cache.sh`

## Validation
9. Run `make test` on target to verify all connections
10. Spot-check WordPress admin login and a few front-end pages

## Rollback
If anything fails, restore from the pre-migration backup created in step 2.

Report: backup filename, table count, import duration, and validation results.
