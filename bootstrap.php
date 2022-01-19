<?php

use DI\ContainerBuilder;

require 'vendor/autoload.php';

// setup DI container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    \F3\Repo\Database::class => DI\create(\F3\Repo\SQLiteDatabase::class),
    \F3\Util\DataRetriever::class => DI\create(\F3\Util\RequestInputDataRetriever::class), 
    \F3\Util\HttpRequest::class => DI\create(\F3\Util\CurlRequest::class)
]);
$container = $containerBuilder->build();

return $container;
?>