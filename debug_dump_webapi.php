<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();
/** @var \Magento\Webapi\Model\Config $config */
$config = $objectManager->get(\Magento\Webapi\Model\Config::class);
$services = $config->getServices();
$routes = $config->getRoutes();
file_put_contents(__DIR__.'/var/webapi_services.json', json_encode(['services'=>array_keys($services), 'routes'=>array_keys($routes)], JSON_PRETTY_PRINT));
echo "Wrote var/webapi_services.json\n";
