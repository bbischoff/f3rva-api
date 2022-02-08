<?php

namespace F3\Resource;

use F3\Model\Version;
use F3\Settings;

/*
 * The main REST resource controller for all supported ${REQUEST_METHOD}s
 */
class VersionResource extends AbstractResource {

    private $dataRetriever;
    private $workoutService;

    /**
     * Main constructor
     */
    public function __construct()
    {
    }

    /**
     * Handles all supported requests
     */
    public function processRequest($requestMethod)
    {
        $response = null;

        switch ($requestMethod) {
            case RequestMethod::GET:
                $version = new Version();
                $version->setVersion(Settings::VERSION);
                
                $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($version));
                break;
            default:
                $response = $this->createResponse(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED, null);
                break;
        }

        return $response;
    }
}

?>
