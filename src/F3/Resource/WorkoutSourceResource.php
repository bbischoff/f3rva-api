<?php

namespace F3\Resource;

use F3\Service\WorkoutService;

/*
 * The main REST resource controller for all supported ${REQUEST_METHOD}s
 */
class WorkoutSourceResource extends AbstractResource {

    private $workoutService;

    /**
     * Main constructor
     */
    public function __construct(WorkoutService $workoutService)
    {
        $this->workoutService = $workoutService;
    }

    /**
     * Handles all supported requests
     */
    public function processRequest($requestMethod)
    {
        $response = null;

        switch ($requestMethod) {
            case RequestMethod::GET:
                $url = $_GET['url'];
                    if (is_null($url)) {
                        $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
                    }
                    else {
                        $result = $this->workoutService->parsePost($url);
                        $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
                    }    
                break;
            default:
                $response = $this->createResponse(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED, null);
                break;
        }

        return $response;
    }
}

?>
