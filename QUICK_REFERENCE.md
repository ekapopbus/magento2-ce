# Magento 2 CE Quick Installation Reference

## Docker Compose Setup (Recommended)

### Start All Services
```bash
# Start all Docker services (MySQL, OpenSearch, Redis, PHPMyAdmin)
docker-compose up -d

# Check running services
docker-compose ps

# View logs
docker-compose logs mysql
docker-compose logs opensearch
```

### Alternative: Individual Docker Commands
```bash
# Start MySQL Docker container only
docker run -d --name mysql-magento -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=magento2-ce -p 3306:3306 mysql:8.0.33
```

## Prerequisites Installation
```bash
# Install PHP 8.4 and extensions
sudo apt install php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml php8.4-gd php8.4-mbstring php8.4-curl php8.4-soap php8.4-intl php8.4-zip php8.4-bcmath

# Install Docker and Docker Compose (if not already installed)
sudo apt update
sudo apt install -y docker.io docker-compose
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker $USER
```
## Magento Installation
```bash
# Download and install
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition magento2-ce
cd magento2-ce

# Set permissions
sudo chmod -R 755 /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 777 var/ generated/ pub/static/ pub/media/
sudo chown -R ubuntu:ubuntu /home/ubuntu/workspaces/projects/magento2-ce
chmod +x bin/magento

# Install Magento (Note: Use 127.0.0.1 for Docker MySQL)
# For OpenSearch (recommended):
bin/magento setup:install \
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
--search-engine=opensearch \
--opensearch-host=localhost \
--opensearch-port=9201 \
--opensearch-index-prefix=magento2 \
--opensearch-timeout=15

# Alternative: For Elasticsearch
bin/magento setup:install \
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
--search-engine=elasticsearch7 \
--elasticsearch-host=localhost \
--elasticsearch-port=9200 \
--elasticsearch-index-prefix=magento2 \
--elasticsearch-timeout=15
```

## Nginx Configuration
```bash
# Create virtual host /etc/nginx/sites-available/magento2-ce.local
upstream fastcgi_backend {
    server unix:/run/php/php8.4-fpm.sock;
}
server {
    listen 80;
    server_name magento2-ce.local;
    set $MAGE_ROOT /home/ubuntu/workspaces/projects/magento2-ce;
    include /home/ubuntu/workspaces/projects/magento2-ce/nginx.conf.sample;
}

# Enable site
sudo ln -sf /etc/nginx/sites-available/magento2-ce.local /etc/nginx/sites-enabled/
echo "127.0.0.1 magento2-ce.local" | sudo tee -a /etc/hosts
sudo nginx -t && sudo systemctl reload nginx

# Fix directory permissions for nginx
sudo chmod 755 /home/ubuntu/ /home/ubuntu/workspaces/ /home/ubuntu/workspaces/projects/
```

## Admin Configuration
```bash
# Change admin URL to /admin
bin/magento setup:config:set --backend-frontname=admin

# Disable 2FA (development only)
bin/magento module:disable Magento_TwoFactorAuth Magento_AdminAdobeImsTwoFactorAuth
rm -rf var/cache/* var/page_cache/* generated/code/*
bin/magento setup:di:compile
bin/magento cache:flush
```

## MFTF Setup
```bash
# Install Chrome and ChromeDriver
wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" | sudo tee /etc/apt/sources.list.d/google-chrome.list
sudo apt update && sudo apt install -y google-chrome-stable default-jdk

# Install ChromeDriver
CHROME_VERSION=$(google-chrome --version | cut -d ' ' -f3)
wget -O /tmp/chromedriver.zip "https://storage.googleapis.com/chrome-for-testing-public/${CHROME_VERSION}/linux64/chromedriver-linux64.zip"
cd /tmp && unzip -o chromedriver.zip
sudo mv chromedriver-linux64/chromedriver /usr/local/bin/ && sudo chmod +x /usr/local/bin/chromedriver

# Setup MFTF
cd /home/ubuntu/workspaces/projects/magento2-ce
vendor/bin/mftf setup:env
```

## MFTF Configuration Files

### dev/tests/acceptance/.env
```env
MAGENTO_BASE_URL=http://magento2-ce.local/
MAGENTO_BACKEND_BASE_URL=http://magento2-ce.local/admin/
MAGENTO_BACKEND_NAME=admin
MAGENTO_ADMIN_USERNAME=admin
MAGENTO_ADMIN_PASSWORD=magento@126!
SELENIUM_CLOSE_ALL_SESSIONS=true
BROWSER=chrome
BROWSER_ARGS="--headless --no-sandbox --disable-dev-shm-usage"
WINDOW_WIDTH=1920
WINDOW_HEIGHT=1080
MODULE_ALLOWLIST=Magento_Framework,ConfigurableProductWishlist,ConfigurableProductCatalogSearch
WAIT_TIMEOUT=60
MAGENTO_CLI_WAIT_TIMEOUT=60
BROWSER_LOG_BLOCKLIST=other
ELASTICSEARCH_VERSION=7
```

### dev/tests/acceptance/.credentials
```
magento/MAGENTO_ADMIN_PASSWORD=magento@126!
```

## Complete MFTF Setup
```bash
# Copy htaccess and build project
cp dev/tests/acceptance/.htaccess.sample dev/tests/acceptance/.htaccess
vendor/bin/mftf build:project
vendor/bin/mftf generate:tests --force

# Test installation
vendor/bin/mftf doctor
```

## Docker Services Management
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View running services
docker-compose ps

# Check logs
docker-compose logs mysql
docker-compose logs opensearch
docker-compose logs redis

# Restart specific service
docker-compose restart mysql

# Access containers
docker exec -it mysql-magento mysql -u root -proot
docker exec -it opensearch-magento bash

# Clean up (remove containers and volumes)
docker-compose down -v
```

## Service URLs
```bash
# Application URLs
Frontend: http://magento2-ce.local/
Admin: http://magento2-ce.local/admin

# Service URLs
PHPMyAdmin: http://localhost:8080 (root/root)
OpenSearch: http://localhost:9201
Elasticsearch: http://localhost:9200 (if using Elasticsearch)
Redis: localhost:6379
```
## Final Verification
```bash
# Test URLs
curl -I http://magento2-ce.local/        # Should return 200 OK
curl -I http://magento2-ce.local/admin   # Should return 200 OK
curl -I http://localhost:8080            # PHPMyAdmin
curl http://localhost:9201               # OpenSearch health

# Test database connection
docker exec -it mysql-magento mysql -u root -proot -e "SHOW DATABASES;"

# Access points
# Frontend: http://magento2-ce.local/
# Admin: http://magento2-ce.local/admin (admin / magento@126!)
# PHPMyAdmin: http://localhost:8080 (root / root)
```

## Common Issues & Quick Fixes
```bash
# Database connection error - use 127.0.0.1 not localhost for Docker
# 500 errors - fix permissions: sudo chmod -R 755 /home/ubuntu/workspaces/projects/magento2-ce
# 403 nginx errors - fix parent dir permissions: sudo chmod 755 /home/ubuntu/
# 2FA blocking admin - disable modules as shown above
# MFTF timeouts - increase WAIT_TIMEOUT in .env or remove --headless for debugging
# Docker services not starting - check: docker-compose logs <service_name>
# Search engine connection failed - verify OpenSearch/Elasticsearch is running: curl http://localhost:9201
```

## Essential Commands
```bash
# Magento commands
bin/magento cache:flush                           # Clear cache
bin/magento setup:di:compile                      # Recompile code  
bin/magento module:status                         # Check modules
bin/magento indexer:reindex                       # Reindex data

# MFTF commands
vendor/bin/mftf run:test <TestName> --force       # Run MFTF test
vendor/bin/mftf generate:tests --force            # Generate tests

# Docker commands
docker-compose up -d                              # Start all services
docker-compose down                               # Stop all services
docker-compose ps                                 # Show running services
docker-compose logs <service>                     # View service logs
```

---
**Total installation time: ~30-45 minutes**  
**Key success factors: Use Docker Compose, 127.0.0.1 for MySQL, fix directory permissions, disable 2FA for development**