# GitHub Copilot Instructions - Magento 2 CE Development Environment

You are working with a complete Magento 2.4.8-p2 Community Edition development environment optimized for Docker, MFTF testing, and enterprise development patterns.

## Core Architecture & Workflows

### Project Structure
- **`bin/magento`**: Primary CLI entry point using Symfony Console (`Magento\Framework\Console\Cli`)
- **`app/code/`**: Custom modules (PSR-0 autoloading)  
- **`dev/tests/acceptance/`**: MFTF functional tests with Docker Selenium integration
- **`pub/`**: Web root with static assets and entry points (`index.php`, `static.php`)
- **`var/`**: Generated code, cache, logs, sessions (cleared frequently in development)

### Essential Development Commands
```bash
# Environment startup (Docker Compose stack)
docker-compose up -d && sudo systemctl start nginx php8.4-fpm

# Core Magento CLI patterns
bin/magento setup:di:compile              # DI container compilation (required after code changes)
bin/magento setup:static-content:deploy   # Static asset deployment
bin/magento cache:flush                   # Cache invalidation
bin/magento setup:upgrade                 # Database schema/data upgrades

# MFTF Testing workflow
vendor/bin/mftf doctor --verbose          # Environment health check (API, Selenium, CLI)
vendor/bin/mftf build:project             # Generate test infrastructure
vendor/bin/mftf run:test TestName         # Execute functional tests
```

### Docker Services Integration
- **MySQL**: `mysql-magento` container (port 3306) with optimized configuration
- **OpenSearch**: `opensearch-magento` (port 9201) for search engine
- **Elasticsearch**: Alternative search option (port 9200)
- **Selenium**: External container at `172.17.0.1:4444` for MFTF testing
- **Web Services**: Native Nginx + PHP-FPM (not containerized)

## Development Patterns

### Module Development
Follow Magento's component architecture:
```php
// registration.php - Module registration
use Magento\Framework\Component\ComponentRegistrar;
ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Vendor_Module', __DIR__);

// etc/module.xml - Dependencies and versioning
<module name="Vendor_Module" setup_version="1.0.0">
    <sequence>
        <module name="Magento_Framework"/>
    </sequence>
</module>
```

### CLI Command Development
Extend `Magento\Framework\Console\Command\Command`:
```php
// Console/Command/MyCommand.php
protected function configure() {
    $this->setName('my:command')->setDescription('Description');
}

protected function execute(InputInterface $input, OutputInterface $output) {
    // Use ObjectManager via DI, never ObjectManager::getInstance()
}
```

### MFTF Testing with Selenium Integration
- **Environment Configuration**: Configure via `dev/tests/acceptance/.env`
  ```bash
  # Required Selenium settings
  SELENIUM_HOST=172.17.0.1
  SELENIUM_PORT=4444
  SELENIUM_PROTOCOL=http
  MAGENTO_BASE_URL=http://magento2-ce.local/
  MAGENTO_BACKEND_NAME=admin
  ```
- **Selenium WebDriver**: External container/service at `http://172.17.0.1:4444/wd/hub`
- **CLI Integration**: Uses `dev/tests/acceptance/utils/command.php` for remote CLI execution
- **Authentication**: TFA handled via `.credentials` file with OTP secrets
- **Test Structure**: XML-based test definitions in `tests/functional/` directories

### MFTF Testing Commands & Workflow
```bash
# Environment validation
vendor/bin/mftf doctor --verbose          # Health check: Selenium, CLI, pages
vendor/bin/mftf doctor                    # Quick health check

# Test infrastructure setup
vendor/bin/mftf build:project             # Generate test infrastructure
vendor/bin/mftf generate:tests            # Generate PHP test files from XML

# Running tests with Selenium
vendor/bin/mftf run:test TestName         # Run specific test
vendor/bin/mftf run:group GroupName       # Run test group
vendor/bin/mftf run:failed                # Re-run failed tests
vendor/bin/mftf run:test --debug          # Debug mode with screenshots

# Test management
vendor/bin/mftf generate:suite SuiteName  # Create test suite
vendor/bin/mftf reset --hard               # Clean all generated files
```

### MFTF Selenium Debugging
- **Screenshots**: Saved to `dev/tests/acceptance/tests/_output/`
- **Logs**: Check `dev/tests/acceptance/mftf.log` for detailed execution logs
- **CLI Wrapper Logs**: 
  - `var/log/mftf-magento-cli-inline.log` (inline execution)
  - `var/log/mftf-command-output.log` (HTTP wrapper)
- **Selenium Grid**: Access at `http://172.17.0.1:4444/grid/console` for session info

### Database & Cache Management
```bash
# Development mode helpers
bin/magento deploy:mode:set developer       # Enable developer mode
bin/magento dev:query-log:enable            # SQL query logging
bin/magento setup:db-schema:upgrade         # Schema changes only
bin/magento setup:db-data:upgrade          # Data migrations only
```

### Static Content & Asset Pipeline
- **Generated Content**: Lives in `pub/static/` and `var/view_preprocessed/`
- **Developer Mode**: Generates assets on-demand (slower but no deployment needed)
- **Production Deployment**: Requires `setup:static-content:deploy` for performance

## Integration Points & Dependencies

### Service Contracts & APIs
- **WebAPI**: REST/SOAP endpoints defined in `etc/webapi.xml`
- **Service Layer**: Use `Api/` interfaces, avoid direct model dependencies
- **Authentication**: Admin token-based for API access, Integration tokens for services

### Event System & Observers
```xml
<!-- etc/events.xml -->
<event name="catalog_product_save_after">
    <observer name="my_observer" instance="Vendor\Module\Observer\ProductSave"/>
</event>
```

### Plugin System (Interceptors)
Preferred over preferences for extensibility:
```xml
<!-- etc/di.xml -->
<type name="Magento\Catalog\Model\Product">
    <plugin name="my_plugin" type="Vendor\Module\Plugin\Product"/>
</type>
```

## Critical Environment Setup

### Development Dependencies
- **PHP 8.4**: With required extensions (intl, gd, soap, bcmath, etc.)
- **Composer 2.x**: Package management and autoloading
- **Node.js**: For frontend build tools (optional but recommended)

### File Permissions & Ownership
```bash
# Standard Magento permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod +x bin/magento

# Web server ownership for generated content
chown -R www-data:www-data var/ generated/ pub/static/
```

### MFTF Testing Requirements
- Selenium WebDriver container running and accessible
- `.mftf_secret` file for dev authentication bypass
- TFA configured with shared secrets in `.credentials`
- HTTP wrapper (`pub/mftf-command.php`) for remote CLI execution

## MoneySpace Payment Gateway Integration

This environment includes a comprehensive MoneySpace payment gateway integration with multiple payment methods and advanced features.

### MoneySpace Module Architecture
```
app/code/
├── Moneyspace/Payment/                    # Core payment module
│   ├── Model/
│   │   ├── MoneyspacePayment.php         # Base payment method
│   │   ├── QrPromPayment.php             # QR PromptPay integration
│   │   └── InstallmentPayment.php        # Installment payment handling
│   ├── Service/
│   │   ├── MoneyspaceApiService.php      # API communication layer
│   │   ├── OrderManagementService.php    # Order processing
│   │   ├── PaymentResponseParser.php     # Response handling
│   │   └── ResponseHandlerService.php    # Webhook response processing
│   ├── Controller/
│   │   ├── Webhook/Index.php             # CSRF-aware webhook endpoint
│   │   ├── Payment/                      # Payment flow controllers
│   │   ├── QrProm/                       # QR PromptPay controllers
│   │   └── Installment/                  # Installment controllers
│   └── etc/payment.xml                   # Payment method definitions
├── Moneyspaceinstallment/Msinstallmentpayment/  # Installment-specific module
└── Moneyspaceqrprom/Msqrprompayment/             # QR PromptPay-specific module
```

### Payment Gateway Development Patterns
- **Service Layer Architecture**: API communication abstracted into dedicated service classes
- **Multi-Method Support**: Single codebase supporting card payments, QR PromptPay, and installments
- **Webhook Integration**: CSRF-aware webhook controller implementing `CsrfAwareActionInterface`
- **Response Parsing**: Dedicated services for handling API responses and payment status updates
- **Modular Design**: Separate modules for each payment method while sharing core functionality

### Configuration Structure
```xml
<!-- payment.xml - Multiple payment method registration -->
<methods>
    <method name="moneyspacepayment">
        <allow_multiple_address>1</allow_multiple_address>
    </method>
    <method name="moneyspacepayment_qrprom">
        <allow_multiple_address>1</allow_multiple_address>
    </method>
    <method name="moneyspacepayment_installment">
        <allow_multiple_address>1</allow_multiple_address>
    </method>
</methods>
```

### API Integration Patterns
```php
// Service-based API communication
class MoneyspaceApiService {
    public function checkPaymentStatus($transactionId, $paymentMethod = 'card') {
        $configPath = $paymentMethod === 'qrprom' 
            ? 'payment/moneyspacepayment_qrprom' 
            : 'payment/moneyspacepayment';
        
        // Dynamic configuration based on payment method
        $credentials = $this->getApiCredentials($configPath);
        return $this->makeApiCall($credentials, $transactionId);
    }
}
```

### Webhook Security Implementation
```php
// CSRF-aware webhook controller
class Index extends Action implements CsrfAwareActionInterface {
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException {
        return null; // Disable CSRF for webhook endpoints
    }
    
    public function validateForCsrf(RequestInterface $request): ?bool {
        return true; // Custom validation logic
    }
}
```

This environment emphasizes Docker-based services with native PHP execution, comprehensive MFTF testing capabilities, and adherence to Magento's enterprise architecture patterns.

## MFTF Testing with Selenium WebDriver

### Environment Setup & Configuration
MFTF tests require Selenium WebDriver for browser automation. Configure your testing environment:

```bash
# dev/tests/acceptance/.env - Core MFTF configuration
SELENIUM_HOST=172.17.0.1
SELENIUM_PORT=4444
SELENIUM_PROTOCOL=http
MAGENTO_BASE_URL=http://magento2-ce.local/
MAGENTO_BACKEND_NAME=admin
BROWSER=chrome
TESTS_MODULE_PATH=tests/functional/Magento
MODULE_ALLOWLIST=Magento_Framework,Magento_ConfigurableProductWishlist,Magento_ConfigurableProductCatalogSearch
```

### Selenium WebDriver Integration
- **Driver Location**: Selenium Grid at `http://172.17.0.1:4444/wd/hub`
- **Browser Support**: Chrome (primary), Firefox (secondary)
- **Session Management**: Automatic session creation/cleanup per test
- **Grid Console**: Monitor active sessions at `http://172.17.0.1:4444/grid/console`

### MFTF Test Execution Workflow
```bash
# 1. Environment health check
vendor/bin/mftf doctor --verbose
# Validates: Selenium connection, admin/storefront pages, CLI wrapper

# 2. Build test infrastructure
vendor/bin/mftf build:project
# Generates: PHP test classes, data providers, page objects

# 3. Run tests
vendor/bin/mftf run:test AdminLoginTest
vendor/bin/mftf run:group smoke
vendor/bin/mftf run:suite MyTestSuite

# 4. Debug failed tests
vendor/bin/mftf run:failed --debug
# Enables: Screenshots, step-by-step logging, browser console logs
```

### Test Development Patterns
```xml
<!-- tests/functional/tests/AdminLoginTest.xml -->
<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <test name="AdminLoginTest">
        <annotations>
            <features value="Admin"/>
            <stories value="Login"/>
            <group value="smoke"/>
        </annotations>
        <before>
            <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        </before>
        <actionGroup ref="AssertAdminDashboardPageIsVisibleActionGroup" stepKey="assertDashboard"/>
    </test>
</tests>
```

### Debugging & Troubleshooting
- **Screenshots**: Auto-saved to `dev/tests/acceptance/tests/_output/debug/`
- **Logs**: 
  - `dev/tests/acceptance/mftf.log` (main execution log)
  - `var/log/mftf-magento-cli-inline.log` (CLI wrapper execution)
  - `var/log/mftf-command-output.log` (HTTP CLI wrapper debug)
- **Common Issues**:
  - Selenium connection: Verify `SELENIUM_HOST:PORT` accessibility
  - CLI failures: Check `pub/mftf-command.php` and `.htaccess` setup
  - Element not found: Use explicit waits, update selectors for UI changes

### MFTF CLI wrapper — focused troubleshooting

If `vendor/bin/mftf doctor` reports a failing "Running Magento CLI" check, this module tries three paths in order: inline include (temporary PHP file that includes `dev/tests/acceptance/utils/command.php`), HTTP wrapper (`pub/mftf-command.php`), and direct `bin/magento` exec as a last resort. Troubleshooting checklist:

- Force the HTTP wrapper (helps isolate the problem):

    Export these env vars before running the doctor so the module posts to the wrapper and uses the `command` parameter:

    ```bash
    export MFTF_FORCE_HTTP_COMMAND=1
    export MAGENTO_CLI_COMMAND_PARAMETER=command
    export MAGENTO_CLI_COMMAND_PATH=pub/mftf-command.php
    vendor/bin/mftf doctor --verbose
    ```

- Inspect wrapper debug log immediately after a doctor run:
    - `tail -n 200 var/log/mftf-command-output.log` — shows POST payload, request headers, process args, exitCode, STDOUT and STDERR.
    - `tail -n 200 var/log/mftf-magento-cli-inline.log` — shows inline include runs and any PHP warnings/parse errors.

- Common causes when the CLI step fails:
    1. Inline include produced parse errors or PHP warnings that contaminated STDOUT (e.g. "Constant BP already defined"). In that case the module treats the output as invalid and falls back; the doctor may report failure if all paths fail.
 2. The HTTP wrapper returned an HTML error or empty body (HTTP 404/500 or an error page), which the doctor treats as an error.
 3. Token retrieval via WebAPI failed (check `dev/tests/acceptance/mftf.log` for WebApiAuth errors and check `MAGENTO_BASE_URL` resolution from all contexts).

- Quick, safe mitigation for inline include noise (development only):

    Edit `dev/tests/acceptance/utils/command.php` to ensure bootstrap output/warnings don't contaminate CLI output. Minimal safe pattern to add at the top of the file:

    ```php
    // ...existing code...
    // Ensure bootstrap doesn't print warnings to STDOUT for MFTF inline includes
    @ini_set('display_errors', '0');
    ob_start();
    // include Magento bootstrap and perform token validation below
    // ...existing code...
    $bootstrapOutput = ob_get_clean();
    // If bootstrap printed anything unexpected, log it to var/log/mftf-magento-cli-inline.log
    if (!empty(trim($bootstrapOutput))) {
            @file_put_contents(BP . '/var/log/mftf-magento-cli-inline.log', date('c') . " [bootstrap_output]\n" . $bootstrapOutput . "\n---\n", FILE_APPEND);
    }
    // ...existing code continues ...
    ```

    This buffers and discards bootstrap noise while preserving a log entry for debugging.

- Developer secret bypass (safe for local dev only):
    - Create `BP/.mftf_secret` (project root) containing a single secret string. The `pub/mftf-command.php` wrapper will accept a POST with `secret` matching that file and bypass token validation. Use this only in local development and never commit secrets to git.

    Example (on the host):
    ```bash
    echo 'my_local_mftf_secret' > .mftf_secret
    chmod 600 .mftf_secret
    ```

    Then an HTTP POST to `pub/mftf-command.php` containing `secret=my_local_mftf_secret` and `command=info:currency:list` will be accepted by the wrapper.

- If DNS resolution differs across environments (PHP CLI, web server, Selenium), ensure `MAGENTO_BASE_URL` (e.g. `http://magento2-ce.local/`) resolves from:
    - the host (where you run `vendor/bin/mftf`),
    - the PHP processes that handle CLI wrapper (the web server/PHP-FPM host), and
    - the Selenium container (browsers).

    If necessary, add host mappings in the Selenium container or host `/etc/hosts` entries so all contexts resolve the same IP.

If you want, I can apply the safe inline-hardening patch to `dev/tests/acceptance/utils/command.php` and re-run `vendor/bin/mftf doctor` (without forcing HTTP wrapper) to confirm the CLI step is consistently [OK].