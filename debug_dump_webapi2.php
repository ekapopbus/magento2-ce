<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();
/** @var \Magento\Webapi\Model\ServiceMetadata $serviceMetadata */
$serviceMetadata = $objectManager->get(\Magento\Webapi\Model\ServiceMetadata::class);
try {
    $servicesConfig = $serviceMetadata->getServicesConfig();
    $routesConfig = $serviceMetadata->getRoutesConfig();
    file_put_contents(__DIR__.'/var/webapi_services2.json', json_encode(['services'=>array_keys($servicesConfig),'routes'=>array_keys($routesConfig)], JSON_PRETTY_PRINT));
    echo "Wrote var/webapi_services2.json\n";
} catch (Exception $e) {
    file_put_contents(__DIR__.'/var/webapi_services2_error.txt', $e->getMessage()."\n".print_r($e->getTrace(),1));
    echo "Error: " . $e->getMessage() . "\n";
}
