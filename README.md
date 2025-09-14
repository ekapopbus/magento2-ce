# Magento 2 Community Edition

A complete Magento 2 CE installation with Nginx, MFTF testing framework, and development optimizations.

## 🚀 Quick Start

### Using Docker Compose (Recommended)
```bash
# Clone/download this project
cd magento2-ce

# Start all services with Docker Compose
docker-compose up -d

# Start web services
sudo systemctl start nginx php8.4-fpm

# Access the application
Frontend: http://magento2-ce.local/
Admin Panel: http://magento2-ce.local/admin
PHPMyAdmin: http://localhost:8080
```

### Using Individual Docker Commands
```bash
# Start MySQL container only
docker start mysql-magento

# Start web services
sudo systemctl start nginx php8.4-fpm
```

**Admin Credentials:**
- Username: `admin`
- Password: `magento@126!`

## 📋 Project Overview

### What's Included
- ✅ Magento 2 Community Edition (Latest)
- ✅ Docker Compose Setup (MySQL, OpenSearch, Redis, PHPMyAdmin)
- ✅ Nginx Web Server Configuration
- ✅ PHP 8.4 with FPM
- ✅ MySQL 8.0 with Magento-optimized configuration
- ✅ OpenSearch + Elasticsearch for Search Engine
- ✅ Redis for Caching (Optional)
- ✅ PHPMyAdmin for Database Management
- ✅ Two-Factor Authentication Disabled (Development)
- ✅ MFTF (Magento Functional Testing Framework)
- ✅ Optimized for Development Environment

### System Requirements
- Ubuntu 22.04 LTS
- PHP 8.4+ with required extensions
- MySQL 8.0+ or MariaDB 10.4+
- Nginx 1.18+
- Composer 2.x
- Node.js (for build tools)

## 🔧 Installation

### Option 1: Quick Setup (Docker Compose)
If you already have this configured environment:

1. **Start All Services**
   ```bash
   # Start all Docker services
   docker-compose up -d
   
   # Check running containers
   docker-compose ps
   
   # Start web services
   sudo systemctl start nginx php8.4-fpm
   ```

2. **Access Services**
   ```bash
   # Magento URLs
   Frontend: http://magento2-ce.local/
   Admin: http://magento2-ce.local/admin
   
   # Database Management
   PHPMyAdmin: http://localhost:8080
   
   # Search Engine
   OpenSearch: http://localhost:9201
   Elasticsearch: http://localhost:9200 (if using Elasticsearch)
   ```

3. **Verify Installation**
   ```bash
   # Test frontend
   curl -I http://magento2-ce.local/
   
   # Test database connection
   docker exec -it mysql-magento mysql -u root -proot -e "SHOW DATABASES;"
   ```

### Option 2: Individual Docker Commands
If you prefer to start services individually:

1. **Start Services**
   ```bash
   # Start MySQL container
   docker start mysql-magento
   
   # Start web services
   sudo systemctl start nginx php8.4-fpm
   ```

2. **Verify Installation**
   ```bash
   # Test frontend
   curl -I http://magento2-ce.local/
   
   # Test admin
   curl -I http://magento2-ce.local/admin
   ```

### Option 3: Fresh Installation
For a complete new setup, follow our detailed guides:

- 📖 **[Complete Installation Guide](INSTALLATION_GUIDE.md)** - Step-by-step detailed instructions
- ⚡ **[Quick Reference](QUICK_REFERENCE.md)** - Fast installation commands

## 🐳 Docker Services

### Available Services
- **MySQL 8.0**: Database server (Port 3306)
- **OpenSearch 2.5**: Search engine (Port 9201)
- **Elasticsearch 7.17**: Alternative search engine (Port 9200)
- **Redis 7.0**: Caching server (Port 6379)
- **PHPMyAdmin**: Database management (Port 8080)

### Docker Compose Commands
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs mysql
docker-compose logs opensearch

# Access containers
docker exec -it mysql-magento bash
docker exec -it opensearch-magento bash

# Restart specific service
docker-compose restart mysql
```

### Service Configuration
- **MySQL**: Optimized for Magento with custom configuration
- **OpenSearch**: Single-node setup with security disabled
- **Redis**: Ready for session and cache storage
- **Data Persistence**: All data stored in named volumes

## 🏗️ Architecture

### File Structure
```
magento2-ce/
├── app/                          # Magento application code
├── bin/magento                   # CLI tool
├── dev/tests/acceptance/         # MFTF tests
├── docker/                       # Docker configuration files
│   └── mysql/conf.d/            # MySQL configuration
├── generated/                    # Generated code
├── nginx.conf.sample            # Nginx configuration
├── pub/                         # Web root
├── var/                         # Cache, logs, sessions
├── vendor/                      # Dependencies
├── docker-compose.yml           # Docker services configuration
├── INSTALLATION_GUIDE.md         # Detailed setup guide
├── QUICK_REFERENCE.md            # Quick commands
└── README.md                     # This file
```

### Services Configuration
- **Web Server**: Nginx with PHP-FPM upstream
- **Database**: MySQL 8.0 in Docker container with Magento optimization
- **Search**: OpenSearch (primary) + Elasticsearch (alternative)
- **Cache**: Redis for sessions and cache (File-based as fallback)
- **Management**: PHPMyAdmin for database administration
- **Container Network**: Isolated Docker network for services

## 🛠️ Development Commands

### Magento CLI
```bash
# Cache management
bin/magento cache:flush                    # Clear all caches
bin/magento cache:clean                    # Clean cache
bin/magento cache:status                   # Check cache status

# Code compilation
bin/magento setup:di:compile               # Compile dependency injection
bin/magento setup:static-content:deploy    # Deploy static content

# Module management
bin/magento module:status                  # List all modules
bin/magento module:enable ModuleName       # Enable module
bin/magento module:disable ModuleName      # Disable module

# Indexing
bin/magento indexer:reindex                # Reindex all
bin/magento indexer:status                 # Check index status

# Database
bin/magento setup:upgrade                  # Run database upgrades
bin/magento setup:db-schema:upgrade        # Upgrade database schema

# Configuration
bin/magento config:set path/to/config value  # Set configuration
bin/magento config:show                      # Show all configuration
```

### Development Mode
```bash
# Enable developer mode
bin/magento deploy:mode:set developer

# Disable production optimizations
bin/magento deploy:mode:show               # Check current mode
```

## 🧪 Testing with MFTF

### Quick Testing Commands
```bash
# Check MFTF environment
vendor/bin/mftf doctor

# Generate all tests
vendor/bin/mftf generate:tests --force

# Run specific test
vendor/bin/mftf run:test AdminLoginTest --force

# Run test group
vendor/bin/mftf run:group smoke --force

# List available commands
vendor/bin/mftf list
```

### MFTF Configuration
- **Environment**: `dev/tests/acceptance/.env`
- **Credentials**: `dev/tests/acceptance/.credentials`
- **Test Output**: `dev/tests/acceptance/tests/_output/`

### Writing Custom Tests
1. Create test XML files in appropriate module
2. Generate tests: `vendor/bin/mftf generate:tests`
3. Run tests: `vendor/bin/mftf run:test YourTestName`

## 🔐 Security & Access

### Admin Panel
- **URL**: http://magento2-ce.local/admin
- **Username**: `admin`
- **Password**: `magento@126!`
- **2FA**: Disabled for development

### Database Access
```bash
# Connect to MySQL via Docker
docker exec -it mysql-magento mysql -u root -proot magento2-ce

# Connect from host
mysql -h 127.0.0.1 -P 3306 -u root -proot magento2-ce

# Using PHPMyAdmin (Web Interface)
# URL: http://localhost:8080
# Server: mysql (Docker network)
# Username: root
# Password: root

# Alternative database user
# Username: magento
# Password: magento
```

### File Permissions
```bash
# Reset permissions if needed
sudo chown -R ubuntu:ubuntu /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 755 /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 777 var/ generated/ pub/static/ pub/media/
```

## 📊 Performance & Optimization

### Caching
```bash
# Enable all cache types
bin/magento cache:enable

# Specific cache types
bin/magento cache:enable config layout block_html
```

### Static Content
```bash
# Deploy static content for development
bin/magento setup:static-content:deploy en_US --force

# For specific themes
bin/magento setup:static-content:deploy --theme Magento/luma en_US
```

### Database Optimization
```bash
# Reindex all data
bin/magento indexer:reindex

# Check indexer status
bin/magento indexer:status
```

## 🐛 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```bash
# Check permissions
sudo chmod -R 755 /home/ubuntu/workspaces/projects/magento2-ce
sudo chmod -R 777 var/ generated/ pub/

# Clear generated code
rm -rf generated/code/* generated/metadata/*
bin/magento setup:di:compile
```

#### 2. Database Connection Issues
```bash
# Verify MySQL container is running
docker-compose ps mysql

# Check container logs
docker-compose logs mysql

# Test connection from host
mysql -h 127.0.0.1 -P 3306 -u root -proot -e "SHOW DATABASES;"

# Restart MySQL service
docker-compose restart mysql
```

#### 3. Nginx 403 Forbidden
```bash
# Fix parent directory permissions
sudo chmod 755 /home/ubuntu/
sudo chmod 755 /home/ubuntu/workspaces/
```

#### 4. Admin Login Issues
```bash
# Clear sessions and cache
rm -rf var/session/* var/cache/*
bin/magento cache:flush
```

### Log Files
- **Magento Logs**: `var/log/`
- **Nginx Logs**: `/var/log/nginx/`
- **PHP-FPM Logs**: `/var/log/php8.4-fpm.log`
- **MFTF Logs**: `dev/tests/acceptance/mftf.log`

## 🚀 Deployment

### Development to Staging
```bash
# 1. Enable maintenance mode
bin/magento maintenance:enable

# 2. Update code
git pull origin main
composer install --no-dev --optimize-autoloader

# 3. Run upgrades
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy

# 4. Disable maintenance mode
bin/magento maintenance:disable
```

### Production Considerations
⚠️ **Important**: This setup is optimized for development. For production:

1. **Enable 2FA**: Remove 2FA module disabling
2. **Use HTTPS**: Configure SSL certificates
3. **Optimize Caching**: Enable Redis/Varnish
4. **Security Headers**: Configure proper security headers
5. **Database Security**: Use strong passwords and limited privileges
6. **File Permissions**: More restrictive permissions

## 📚 Documentation

- 📖 **[Installation Guide](INSTALLATION_GUIDE.md)** - Complete setup instructions
- ⚡ **[Quick Reference](QUICK_REFERENCE.md)** - Fast installation commands
- 🏗️ **[Magento DevDocs](https://devdocs.magento.com/)** - Official documentation
- 🧪 **[MFTF Documentation](https://devdocs.magento.com/mftf/docs/introduction.html)** - Testing framework docs

## 🤝 Contributing

### Development Workflow
1. Create feature branch from `main`
2. Make changes and test locally
3. Run MFTF tests: `vendor/bin/mftf run:group smoke`
4. Submit pull request

### Code Standards
```bash
# PHP CodeSniffer (if available)
vendor/bin/phpcs --standard=Magento2 app/code/

# PHP Static Analysis
vendor/bin/phpstan analyse app/code/
```

## 📝 License

This project includes Magento Community Edition, which is licensed under the Open Software License (OSL 3.0).

## 🆘 Support

### Getting Help
- **Issues**: Check the troubleshooting section above
- **Documentation**: Refer to installation guides
- **Magento Community**: [Magento Forums](https://community.magento.com/)
- **Stack Overflow**: Tag questions with `magento2`

### Quick Debug Commands
```bash
# Check system status
bin/magento                                # Magento CLI help
php -v                                     # PHP version
nginx -t                                   # Nginx config test
docker-compose ps                          # Running containers
systemctl status nginx php8.4-fpm         # Service status

# Check URLs
curl -I http://magento2-ce.local/          # Frontend test
curl -I http://magento2-ce.local/admin     # Admin test
curl -I http://localhost:8080              # PHPMyAdmin test
curl http://localhost:9201                 # OpenSearch test

# Docker service logs
docker-compose logs mysql                  # MySQL logs
docker-compose logs opensearch             # OpenSearch logs
docker-compose logs redis                  # Redis logs
```

---

## 🎯 Project Status

**Status**: ✅ Ready for Development  
**Last Updated**: September 2025  
**Magento Version**: Community Edition (Latest)  
**Environment**: Development-Optimized  

**Quick Access:**
- Frontend: http://magento2-ce.local/
- Admin: http://magento2-ce.local/admin (`admin` / `magento@126!`)
- Database: http://localhost:8080 (PHPMyAdmin: `root` / `root`)
- OpenSearch: http://localhost:9201
- Redis: localhost:6379

Happy coding! 🚀