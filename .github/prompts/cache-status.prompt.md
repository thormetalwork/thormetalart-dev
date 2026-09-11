---
description: "Check Redis cache health: hit rate, memory usage, connected clients, and key count"
agent: "performance-analyst"
---
Analyze the Redis cache status for Thor Metal Art:

1. **Health**: Verify Redis is running and responding
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning ping
   ```

2. **Memory**: Report `used_memory_human`, `maxmemory_human`, and usage percentage
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO memory
   ```

3. **Hit Rate**: Calculate from `keyspace_hits` / (`keyspace_hits` + `keyspace_misses`) × 100
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO stats
   ```

4. **Keys**: Report total keys in DB 0
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning DBSIZE
   ```

5. **Clients**: Report `connected_clients`
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO clients
   ```

## Output

Present as a compact status card:
- 🟢 Healthy: hit rate > 80%, memory < 80%
- 🟡 Warning: hit rate 50-80%, memory 80-95%
- 🔴 Critical: hit rate < 50%, memory > 95%

If any metric is 🟡 or 🔴, suggest specific actions (flush stale keys, increase maxmemory, check WP-Redis plugin).
