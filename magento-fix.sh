#!/bin/bash
set -e
# Fix Magento 2 cache and permissions

MAGENTO_ROOT="$(cd "$(dirname "$0")" && pwd)"
PHP="php -d memory_limit=-1"

# Server (Linux) vs. local macOS: ownership/service restarts only apply on Linux
if [ "$(uname)" = "Linux" ]; then
    OWNER="ubuntu:www-data"
    SUDO="sudo"
else
    OWNER=""
    SUDO=""
fi

echo "Removing all cache and generated files..."
$SUDO rm -rf "$MAGENTO_ROOT/var/cache/"* "$MAGENTO_ROOT/var/page_cache/"* "$MAGENTO_ROOT/var/view_preprocessed/"* "$MAGENTO_ROOT/generated/"*

echo "Creating required directories..."
mkdir -p "$MAGENTO_ROOT/var/cache" "$MAGENTO_ROOT/var/page_cache" "$MAGENTO_ROOT/var/view_preprocessed" "$MAGENTO_ROOT/var/tmp" "$MAGENTO_ROOT/var/log" "$MAGENTO_ROOT/var/session" "$MAGENTO_ROOT/generated/code" "$MAGENTO_ROOT/generated/metadata" "$MAGENTO_ROOT/pub/static/frontend" "$MAGENTO_ROOT/pub/static/adminhtml" "$MAGENTO_ROOT/pub/media"

echo "Fixing ownership and permissions for all var/ and generated/ directories..."
[ -n "$OWNER" ] && $SUDO chown -R "$OWNER" "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"
$SUDO chmod -R 775 "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"

echo "Ensuring cache directories are writable..."
[ -n "$OWNER" ] && $SUDO chown -R "$OWNER" "$MAGENTO_ROOT/var/cache" "$MAGENTO_ROOT/var/page_cache"
$SUDO chmod -R 777 "$MAGENTO_ROOT/var/cache" "$MAGENTO_ROOT/var/page_cache"

echo "Running Magento setup and cache operations..."
cd "$MAGENTO_ROOT" || exit 1
$PHP bin/magento setup:upgrade
$PHP bin/magento setup:di:compile
$PHP bin/magento cache:flush
$PHP bin/magento setup:static-content:deploy -f

echo "Final permission fix..."
[ -n "$OWNER" ] && $SUDO chown -R "$OWNER" "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"
$SUDO chmod -R 775 "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"

echo "Restarting PHP-FPM and Nginx..."
if [ "$(uname)" = "Linux" ]; then
    sudo systemctl restart php8.4-fpm nginx
else
    echo "Skipped (local macOS: restart containers with docker compose if needed)"
fi

echo "Done! Testing sites..."
sleep 3

# Test both main site and installment form
MAIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://magento2-ce.local/ || true)
FORM_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://magento2-ce.local/moneyspace/installment/form || true)

echo "Main Site HTTP Status: $MAIN_STATUS"
echo "Installment Form HTTP Status: $FORM_STATUS"

if [ "$MAIN_STATUS" = "200" ] && [ "$FORM_STATUS" = "200" ]; then
    echo "✅ All systems working correctly!"
else
    echo "❌ Some issues detected. Check error logs:"
    echo "sudo tail -20 /var/log/nginx/error.log"
fi