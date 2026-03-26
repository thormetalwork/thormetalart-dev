---
description: "Test all stack connections: MySQL, Redis, WordPress, phpMyAdmin, Dashboard"
agent: "devops"
---
Run a comprehensive health check of the entire Thor Metal Art stack:

1. Execute `make test` to run the connection test script
2. Verify each service individually:
   - MySQL: container health + query test
   - Redis: ping + memory usage
   - WordPress: HTTP response on wp-login.php
   - phpMyAdmin: HTTP response
   - Dashboard (Nginx): HTTP response
3. Check `make status` for container states
4. Report any services that are unhealthy or unreachable
5. Suggest remediation for any failures found
