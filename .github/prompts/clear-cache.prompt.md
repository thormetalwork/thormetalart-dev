---
description: "Clear Redis object cache and verify WordPress is caching correctly"
agent: "devops"
---
Clear and verify the Redis object cache for Thor Metal Art:

1. Check current cache state (key count and memory usage)
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning DBSIZE
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning INFO memory | grep used_memory_human
   ```

2. Flush the cache (DB 0 only — never FLUSHALL):
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning FLUSHDB
   ```

3. Trigger WordPress to repopulate cache by hitting the homepage:
   ```bash
   curl -sI http://dev.thormetalart.com | head -5
   ```

4. Verify cache is repopulating (key count should increase):
   ```bash
   docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning DBSIZE
   ```

5. Report: keys before, keys after flush, keys after repopulation, memory freed
