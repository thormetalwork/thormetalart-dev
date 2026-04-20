---
description: "Restore the MySQL database from a backup file in /backups/"
agent: "devops"
---
Restore the Thor Metal Art database from a backup:

1. List available backups in `/backups/` sorted by date (newest first)
2. Ask the user which backup to restore (default: most recent)
3. **Before restoring**, create a fresh backup of the current database (`make backup`)
4. Restore the selected backup:
   ```bash
   gunzip -c /backups/<selected_file> | docker exec -i tma_dev_mysql mysql -u root -p"$MYSQL_ROOT_PASSWORD" thormetalart_wp
   ```
5. Flush Redis cache after restore:
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning FLUSHDB
   ```
6. Verify restore: run a quick table count and spot-check `tma_options`
7. Report: restored file, size, table count, and rollback backup filename
