<?php

/**
 * This file serves as the front controller for all API requests, essentially a router.
 * If the requested path is not a known path, appropriate and consistent error handling happens here.
 */
require "../bootstrap.php";
use Src\F3\Resource\WorkoutResource;

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
        // set the workoutId if it exists in the path
        $workoutId = NULL;
        if (isset($uri[2])) {
            $workoutId = (int) $uri[2];
        }

        // delegate to the resource
        $resource = new WorkoutResource($requestMethod, $workoutId);
        $resource->processRequest();
        break;
    default:
        notFound();
        break;
}

function notFound() {
    header('HTTP/1.1 404 Not Found');
}

// the user id is, of course, optional and must be a number:
// $userId = null;
// if (isset($uri[2])) {
//     $userId = (int) $uri[2];
// }

?>