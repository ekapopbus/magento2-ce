#!/bin/bash
# Fix Magento 2 cache and permissions

MAGENTO_ROOT="/home/ubuntu/workspaces/projects/magento2-ce"

echo "Removing all cache and generated files..."
sudo rm -rf "$MAGENTO_ROOT/var/cache/"* "$MAGENTO_ROOT/var/page_cache/"* "$MAGENTO_ROOT/var/view_preprocessed/"* "$MAGENTO_ROOT/generated/"*

echo "Creating required directories..."
mkdir -p "$MAGENTO_ROOT/var/cache" "$MAGENTO_ROOT/var/page_cache" "$MAGENTO_ROOT/var/view_preprocessed" "$MAGENTO_ROOT/var/tmp" "$MAGENTO_ROOT/var/log" "$MAGENTO_ROOT/var/session" "$MAGENTO_ROOT/generated/code" "$MAGENTO_ROOT/generated/metadata" "$MAGENTO_ROOT/pub/static/frontend" "$MAGENTO_ROOT/pub/static/adminhtml" "$MAGENTO_ROOT/pub/media"

echo "Fixing ownership and permissions for all var/ and generated/ directories..."
sudo chown -R ubuntu:www-data "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"
sudo chmod -R 775 "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"

echo "Ensuring cache directories are writable..."
sudo chown -R ubuntu:www-data "$MAGENTO_ROOT/var/cache" "$MAGENTO_ROOT/var/page_cache"
sudo chmod -R 777 "$MAGENTO_ROOT/var/cache" "$MAGENTO_ROOT/var/page_cache"

echo "Running Magento setup and cache operations..."
cd "$MAGENTO_ROOT"
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
php bin/magento setup:static-content:deploy -f

echo "Final permission fix..."
sudo chown -R ubuntu:www-data "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"
sudo chmod -R 775 "$MAGENTO_ROOT/var" "$MAGENTO_ROOT/generated" "$MAGENTO_ROOT/pub/static"

echo "Restarting PHP-FPM and Nginx..."
sudo systemctl restart php8.4-fpm nginx

echo "Done! Testing sites..."
sleep 3

# Test both main site and installment form
MAIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://magento2-ce.local/)
FORM_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://magento2-ce.local/moneyspace/installment/form)

echo "Main Site HTTP Status: $MAIN_STATUS"
echo "Installment Form HTTP Status: $FORM_STATUS"

if [ "$MAIN_STATUS" = "200" ] && [ "$FORM_STATUS" = "200" ]; then
    echo "✅ All systems working correctly!"
else
    echo "❌ Some issues detected. Check error logs:"
    echo "sudo tail -20 /var/log/nginx/error.log"
fi