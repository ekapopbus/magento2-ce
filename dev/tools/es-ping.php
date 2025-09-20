<?php
// Use Magento's app bootstrap which registers the autoloader
require __DIR__ . '/../../app/bootstrap.php';
use Magento\Framework\App\Bootstrap;
// Define BP (Magento base path)
if (!defined('BP')) {
    define('BP', realpath(__DIR__ . '/../../'));
}
chdir(BP);
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();

/** @var \Magento\AdvancedSearch\Model\Client\ClientResolver $clientResolver */
$clientResolver = $objectManager->get(\Magento\AdvancedSearch\Model\Client\ClientResolver::class);
try {
    // Print scope config values
    $scopeConfig = $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
    echo "catalog/search/engine => ";
    var_export($scopeConfig->getValue('catalog/search/engine'));
    echo PHP_EOL;
    echo "catalog/search/elasticsearch8_server_hostname => ";
    var_export($scopeConfig->getValue('catalog/search/elasticsearch8_server_hostname'));
    echo PHP_EOL;
    echo "catalog/search/elasticsearch8_server_port => ";
    var_export($scopeConfig->getValue('catalog/search/elasticsearch8_server_port'));
    echo PHP_EOL;

    // Prepare client options via resolver internals: create with empty data triggers prepareClientOptions
    $reflection = new ReflectionClass($clientResolver);
    // Access protected pools
    $prop = $reflection->getProperty('clientOptionsPool');
    $prop->setAccessible(true);
    $clientOptionsPool = $prop->getValue($clientResolver);
    $currentEngine = $clientResolver->getCurrentEngine();
    $optionsClass = $clientOptionsPool[$currentEngine];
    $optionsObj = $objectManager->create($optionsClass);
    $prepared = $optionsObj->prepareClientOptions([]);
    echo "Prepared client options for engine {$currentEngine}:\n";
    print_r($prepared);

    $client = $clientResolver->create();
    $result = $client->testConnection();
    echo "testConnection result: ";
    var_export($result);
    echo PHP_EOL;
} catch (\Throwable $e) {
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
