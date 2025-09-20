#!/bin/bash
set -e

PHP_BIN="$(which php)"
if [ -z "$PHP_BIN" ]; then
  PHP_BIN="/opt/homebrew/bin/php"
fi

# Run installer with increased memory limit and cleanup existing DB data
$PHP_BIN -d memory_limit=2G ./bin/magento setup:install \
--cleanup-database \
--base-url=http://magento2-ce.local/ \
--db-host=127.0.0.1:3306 \
--db-name=magento2-ce \
--db-user=root \
--db-password=root \
--admin-firstname=admin \
--admin-lastname=admin \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=magento@126! \
--language=en_US \
--currency=USD \
--timezone=Asia/Bangkok \
--use-rewrites=1 \
--search-engine=elasticsearch8 \
--elasticsearch-host=127.0.0.1 \
--elasticsearch-port=9200 \
--elasticsearch-index-prefix=magento2 \
--elasticsearch-timeout=15
