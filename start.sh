#!/bin/bash
# Start the local Magento stack (mysql, php-fpm, nginx + Elasticsearch)
set -e
cd "$(dirname "$0")"

NETWORK="magento2-ce_magento-network"
URL="http://magento2-ce.local"

# --no-deps: don't pull in redis/opensearch, which may already run from local-services
echo "Starting mysql, php-fpm, nginx..."
docker compose up -d --no-deps mysql php-fpm nginx

# Elasticsearch: reuse the one from local-services if running, otherwise start ours
if docker ps --format '{{.Names}}' | grep -qx elasticsearch; then
    docker network connect "$NETWORK" elasticsearch 2>/dev/null || true
else
    docker compose up -d --no-deps elasticsearch
fi

# Session dir lives in the container's /tmp and is lost on recreate
docker exec -u root php-fpm-magento sh -c 'mkdir -p /tmp/magento_sessions && chmod 1777 /tmp/magento_sessions'

echo "Waiting for MySQL..."
for _ in $(seq 1 30); do
    docker exec mysql-magento mysqladmin ping -uroot -proot --silent 2>/dev/null && break
    sleep 2
done

echo "Testing sites..."
for path in / /admin; do
    printf "%s%s -> " "$URL" "$path"
    curl -s -o /dev/null -m90 -w "%{http_code}\n" "$URL$path" || echo "failed"
done
