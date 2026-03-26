---
description: "Deploy changes to the Thor Metal Art stack: rebuild containers, verify health, run tests"
agent: "devops"
---
Deploy the latest changes to the Thor Metal Art stack safely:

1. **Pre-flight checks:**
   - Run `make test` to verify current stack is healthy
   - Run `make backup` to create a database backup before changes

2. **Deploy:**
   - Run `make build` to rebuild containers without cache
   - Wait for all services to report healthy

3. **Post-deploy verification:**
   - Run `make test` to verify all connections
   - Check `make logs` for any errors in the last 50 lines
   - Verify WordPress is accessible at dev.thormetalart.com
   - Verify Dashboard is accessible at dashboard.thormetalart.server-dev

4. **Report:**
   - Services status (up/down/healthy)
   - Any warnings or errors found
   - Backup file created
   - Deployment timestamp
