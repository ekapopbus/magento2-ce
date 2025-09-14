# Magento 2 Community Edition Installation Guide

Complete step-by-step guide for installing Magento 2 CE with Nginx, disabling 2FA, and setting up MFTF testing framework.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Database Setup](#database-setup)
3. [Magento Installation](#magento-installation)
4. [Nginx Configuration](#nginx-configuration)
5. [Admin Configuration](#admin-configuration)
6. [MFTF Setup](#mftf-setup)
7. [Troubleshooting](#troubleshooting)

## Prerequisites

### System Requirements
- Ubuntu 22.04 LTS
- PHP 8.4 with FPM
- MySQL 8.0+ (can be Docker container)
- Nginx 1.18+
- Composer 2.x
- OpenSearch/Elasticsearch

### Required PHP Extensions
Ensure these PHP extensions are installed:
```bash
sudo apt install php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml php8.4-gd php8.4-mbstring php8.4-curl php8.4-soap php8.4-intl php8.4-zip php8.4-bcmath
```

## Database Setup

### Using Docker MySQL
```bash
# Start MySQL container
docker run -d --name mysql-magento \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=magento2-ce \
  -p 3306:3306 \
  mysql:8.0.33

# Verify connection
mysql -h 127.0.0.1 -P 3306 -u root -proot -e "SHOW DATABASES;"
```

**Important**: Use `127.0.0.1` instead of `localhost` when connecting to Docker MySQL to force TCP connection.

## Magento Installation

### 1. Download Magento 2 CE
```bash
cd /home/ubuntu/workspaces/projects/
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition magento2-ce
cd magento2-ce
```

### 2. Set Permissions
```bash
# Set directory permissions
sudo chmod -R 755 /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 777 var/ generated/ pub/static/ pub/media/
sudo chown -R ubuntu:ubuntu /home/ubuntu/workspaces/projects/magento2-ce
chmod +x bin/magento
```

### 3. Install Magento
Create installation script `magento-install.sh`:
```bash
#!/bin/bash
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
--opensearch-port=9200 \
--opensearch-index-prefix=magento2 \
--opensearch-timeout=15
```

Run installation:
```bash
chmod +x magento-install.sh
./magento-install.sh
```

## Nginx Configuration

### 1. Create Virtual Host
Create `/etc/nginx/sites-available/magento2-ce.local`:
```nginx
upstream fastcgi_backend {
    server unix:/run/php/php8.4-fpm.sock;
}

server {
    listen 80;
    server_name magento2-ce.local;
    set $MAGE_ROOT /home/ubuntu/workspaces/projects/magento2-ce;
    
    include /home/ubuntu/workspaces/projects/magento2-ce/nginx.conf.sample;
}
```

### 2. Enable Site
```bash
sudo ln -sf /etc/nginx/sites-available/magento2-ce.local /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 3. Update Hosts File
```bash
echo "127.0.0.1 magento2-ce.local" | sudo tee -a /etc/hosts
```

### 4. Fix Directory Permissions for Nginx
```bash
# Ensure nginx can traverse to the Magento directory
sudo chmod 755 /home/ubuntu/
sudo chmod 755 /home/ubuntu/workspaces/
sudo chmod 755 /home/ubuntu/workspaces/projects/
```

## Admin Configuration

### 1. Change Admin URL (Optional)
Change from random URL to simple `/admin`:
```bash
bin/magento setup:config:set --backend-frontname=admin
bin/magento cache:flush
```

### 2. Disable Two-Factor Authentication (Development Only)
```bash
# Disable 2FA modules
bin/magento module:disable Magento_TwoFactorAuth Magento_AdminAdobeImsTwoFactorAuth

# Clear cache and compile
rm -rf var/cache/* var/page_cache/* generated/code/*
bin/magento setup:di:compile
bin/magento cache:flush
```

### 3. Verify Installation
Test frontend and admin access:
```bash
curl -I http://magento2-ce.local/
curl -I http://magento2-ce.local/admin
```

Admin credentials:
- **URL**: http://magento2-ce.local/admin
- **Username**: admin
- **Password**: magento@126!

## MFTF Setup

### 1. Install Dependencies

#### Install Google Chrome
```bash
wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" | sudo tee /etc/apt/sources.list.d/google-chrome.list
sudo apt update
sudo apt install -y google-chrome-stable
```

#### Install ChromeDriver
```bash
# Get Chrome version and download matching ChromeDriver
CHROME_VERSION=$(google-chrome --version | cut -d ' ' -f3)
wget -O /tmp/chromedriver.zip "https://storage.googleapis.com/chrome-for-testing-public/${CHROME_VERSION}/linux64/chromedriver-linux64.zip"
cd /tmp && unzip -o chromedriver.zip
sudo mv chromedriver-linux64/chromedriver /usr/local/bin/
sudo chmod +x /usr/local/bin/chromedriver
```

#### Install Java
```bash
sudo apt install -y default-jdk
```

### 2. Configure MFTF Environment
```bash
# Create acceptance test directory
mkdir -p dev/tests/acceptance

# Generate environment file
vendor/bin/mftf setup:env
```

### 3. Update Environment Configuration
Edit `dev/tests/acceptance/.env`:
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

### 4. Create Credentials File
Create `dev/tests/acceptance/.credentials`:
```
magento/MAGENTO_ADMIN_PASSWORD=magento@126!
```

### 5. Set Up MFTF Files
```bash
# Copy htaccess file
cp dev/tests/acceptance/.htaccess.sample dev/tests/acceptance/.htaccess

# Build MFTF project
vendor/bin/mftf build:project

# Generate tests
vendor/bin/mftf generate:tests --force
```

### 6. Verify MFTF Setup
```bash
# Check environment health
vendor/bin/mftf doctor

# List available commands
vendor/bin/mftf list

# Run a test (example)
vendor/bin/mftf run:test AdminLoginTest --force
```

## MFTF Usage

### Basic Commands
```bash
# Generate all tests
vendor/bin/mftf generate:tests --force

# Run specific test
vendor/bin/mftf run:test <TestName> --force

# Run tests by group
vendor/bin/mftf run:group <GroupName>

# Check environment
vendor/bin/mftf doctor

# Static analysis
vendor/bin/mftf static-checks
```

### Test Output Location
- Test results: `dev/tests/acceptance/tests/_output/`
- Screenshots: `dev/tests/acceptance/tests/_output/debug/`
- Logs: `dev/tests/acceptance/mftf.log`

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
**Problem**: `SQLSTATE[HY000] [2002] No such file or directory`
**Solution**: Use `127.0.0.1` instead of `localhost` for Docker MySQL

#### 2. 500 Internal Server Error
**Problem**: File permission issues
**Solution**: 
```bash
sudo chown -R ubuntu:ubuntu /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 755 /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 777 var/ generated/ pub/static/ pub/media/
```

#### 3. Nginx 403 Forbidden
**Problem**: Directory traversal permissions
**Solution**:
```bash
sudo chmod 755 /home/ubuntu/
sudo chmod 755 /home/ubuntu/workspaces/
sudo chmod 755 /home/ubuntu/workspaces/projects/
```

#### 4. MFTF Browser Timeout
**Problem**: Headless browser navigation timeouts
**Solution**: Increase timeout values in `.env` file or use non-headless mode for debugging

#### 5. Admin 2FA Blocking Access
**Problem**: Two-factor authentication enabled
**Solution**: Disable 2FA modules as shown in Admin Configuration section

### Useful Commands
```bash
# Clear all caches
bin/magento cache:flush

# Recompile code
bin/magento setup:di:compile

# Check module status
bin/magento module:status

# Reindex data
bin/magento indexer:reindex

# Check Magento status
bin/magento

# Test URLs
curl -I http://magento2-ce.local/
curl -I http://magento2-ce.local/admin
```

## File Structure Reference
```
magento2-ce/
├── app/                          # Magento application code
├── bin/magento                   # Magento CLI tool
├── dev/tests/acceptance/         # MFTF tests and configuration
│   ├── .env                      # MFTF environment variables
│   ├── .credentials              # MFTF credentials
│   ├── .htaccess                 # MFTF web access rules
│   ├── codeception.yml           # Codeception configuration
│   └── tests/functional/         # Generated test files
├── generated/                    # Generated code
├── nginx.conf.sample            # Nginx configuration sample
├── pub/                         # Web root directory
├── var/                         # Variable data (cache, logs)
└── vendor/                      # Composer dependencies
```

## Next Steps

After successful installation:

1. **Configure Magento**: Set up stores, themes, and extensions
2. **Create Custom Tests**: Write MFTF tests for your specific functionality
3. **Set up CI/CD**: Integrate MFTF tests into your deployment pipeline
4. **Performance Optimization**: Configure caching, CDN, and database optimization
5. **Security Hardening**: Enable security features for production environments

## Notes

- This guide is for **development environments only**
- **Never disable 2FA in production**
- Always use HTTPS in production
- Regular backups are essential
- Keep Magento and dependencies updated

---

**Installation completed successfully!** 🎉

Frontend: http://magento2-ce.local/  
Admin Panel: http://magento2-ce.local/admin  
Credentials: admin / magento@126!