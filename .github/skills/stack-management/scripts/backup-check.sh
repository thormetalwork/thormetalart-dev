#!/bin/bash
set -e

# Quick stack health verification for Thor Metal Art
# Used by the stack-management skill

echo "=== Thor Metal Art Stack Health Check ==="
echo ""

# Check if docker compose is available
if ! command -v docker &> /dev/null; then
    echo "❌ Docker not found"
    exit 1
fi

cd /srv/stacks/thormetalart

# Check containers
echo "📦 Container Status:"
docker compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}" 2>/dev/null || echo "⚠️  Could not get container status"
echo ""

# Check backups
echo "💾 Backups:"
if [[ -d backups ]]; then
    BACKUP_COUNT=$(ls -1 backups/*.sql.gz 2>/dev/null | wc -l)
    LATEST=$(ls -1t backups/*.sql.gz 2>/dev/null | head -1)
    echo "  Total: ${BACKUP_COUNT} backups"
    if [[ -n "${LATEST}" ]]; then
        echo "  Latest: ${LATEST} ($(du -h "${LATEST}" | cut -f1))"
    fi
else
    echo "  ⚠️  No backups directory found"
fi
echo ""

# Check disk usage
echo "💿 Disk Usage:"
du -sh data/mysql/ 2>/dev/null | awk '{print "  MySQL data: " $1}' || echo "  MySQL data: N/A"
du -sh data/wordpress/ 2>/dev/null | awk '{print "  WordPress:  " $1}' || echo "  WordPress: N/A"
echo ""

echo "✅ Health check complete"
