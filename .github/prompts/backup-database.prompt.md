---
description: "Backup the MySQL database with rotation. Run before any major changes."
agent: "devops"
---
Execute a full database backup for the Thor Metal Art stack:

1. Verify the MySQL container is running and healthy
2. Run `make backup` to create a timestamped `.sql.gz` dump
3. Verify the backup was created successfully in `/backups/`
4. Report: backup filename, size, and total backups retained
5. If retention exceeds 10 files, confirm cleanup happened
