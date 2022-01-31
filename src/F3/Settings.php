<?php
namespace F3;

use DI\ContainerBuilder;

class Settings {

	const VERSION = '0.1.0-dev';
	
	public static function getDIContainer() {
		$environment = getenv('ENVIRONMENT');

		// setup DI container
		$containerBuilder = new ContainerBuilder();
		$containerBuilder->addDefinitions(dirname(__DIR__) . '/../config/config.php');
		$containerBuilder->addDefinitions(dirname(__DIR__) . '/../config/config-' . $environment . '.php');
		$containerBuilder->useAnnotations(true);
		$container = $containerBuilder->build();

		return $container;
	}
}

?>
