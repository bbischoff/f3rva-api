<?php

use F3\Resource\WorkoutResource;
use F3\Resource\WorkoutSourceResource;

/**
 * This file serves as the front controller for all API requests, essentially a router.
 * If the requested path is not a known path, appropriate and consistent error handling happens here.
 */
$container = require "../bootstrap.php";

// set default headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];

// router for supported endpoints
switch($uri[1]) {
    case 'workout': 
        // delegate to the resource
        $resource = $container->get(WorkoutResource::class);
        $response = $resource->processRequest($requestMethod);

        // set header and response body
        header($response[WorkoutResource::HEADER_KEY]);
        if ($response[WorkoutResource::BODY_KEY]) {
            echo $response[WorkoutResource::BODY_KEY];
        }
        
        break;
    case 'workoutSource': 
        // delegate to the resource
        $resource = $container->get(WorkoutSourceResource::class);
        $response = $resource->processRequest($requestMethod);

        // set header and response body
        header($response[WorkoutResource::HEADER_KEY]);
        if ($response[WorkoutResource::BODY_KEY]) {
            echo $response[WorkoutResource::BODY_KEY];
        }

        break;
    default:
        header('HTTP/1.1 404 Not Found');
        break;
}

?>