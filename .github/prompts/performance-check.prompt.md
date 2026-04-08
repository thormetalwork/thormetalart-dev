---
description: "Check WordPress performance: response times, Redis cache hit rate, MySQL slow queries, and container resource usage"
agent: "devops"
---

## Performance Check — Thor Metal Art

Run a comprehensive performance analysis of the stack:

1. **Container health** — `make status` to verify all services are running
2. **WordPress response time** — Measure TTFB with `curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s\nTotal: %{time_total}s\n" http://localhost/wp-login.php` from inside the WordPress container
3. **Redis stats** — `docker compose exec redis redis-cli INFO stats` — report: connected_clients, used_memory_human, keyspace_hits, keyspace_misses, hit rate %
4. **MySQL process list** — `docker compose exec mysql mysqladmin -u root -p"${MYSQL_ROOT_PASSWORD}" processlist` — check for long-running queries
5. **Container resources** — `docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}" | grep thormetalart`

## Output

Present results in a table with status indicators:
- 🟢 Healthy: TTFB < 1s, Redis hit rate > 80%, no long queries
- 🟡 Warning: TTFB 1-3s, Redis hit rate 50-80%, queries > 5s
- 🔴 Critical: TTFB > 3s, Redis hit rate < 50%, queries > 30s

If any metric is 🟡 or 🔴, suggest specific remediation steps.
