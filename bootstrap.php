<?php

use DI\ContainerBuilder;

require 'vendor/autoload.php';

// setup DI container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

return $container;
?>