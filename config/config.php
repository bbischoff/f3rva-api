<?php

// Global configurations.  Environment specific configurations should go in config-$env.php.
return [
    // class dependencies
    \F3\Util\DataRetriever::class => DI\create(\F3\Util\RequestInputDataRetriever::class), 
    \F3\Util\HttpRequest::class => DI\create(\F3\Util\CurlRequest::class)
];

?>