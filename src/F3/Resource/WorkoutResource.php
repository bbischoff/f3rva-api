<?php

namespace F3\Resource;

use F3\Service\WorkoutService;
use F3\Util\DateUtil;

/*
 * The main REST resource controller for all supported ${REQUEST_METHOD}s
 */
class WorkoutResource extends AbstractResource {

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
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        // set the workoutId if it exists in the path
        $workoutId = NULL;
        if (isset($uri[2])) {
            $workoutId = $uri[2];
        }

        $response = null;

        $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
        $numDays = isset($_GET['numberOfDays']) ? $_GET['numberOfDays'] : null;
        $ao = isset($_GET['ao']) ? $_GET['ao'] : null;
        $q = isset($_GET['q']) ? $_GET['q'] : null;
        $pax = isset($_GET['pax']) ? $_GET['pax'] : null;

        switch ($requestMethod) {
            case RequestMethod::GET:
                if (isset($workoutId)) {
                    $response = $this->getWorkout($workoutId);
                } else if (isset($ao)) {
                    $response = $this->getWorkoutsByAO($ao);
                } else if (isset($q)) {
                    $response = $this->getWorkoutsByQ($q);
                } else if (isset($pax)) {
                    $response = $this->getWorkoutsByPax($pax);
                } else {        
                    $response = $this->getAllWorkouts($startDate, $numDays);
                };
                break;
            // RequestMethod::POST:
            //     $response = $this->createUserFromRequest();
            //     break;
            // RequestMethod::PUT:
            //     $response = $this->updateUserFromRequest($this->userId);
            //     break;
            case RequestMethod::DELETE:
                $response = $this->deleteWorkout($workoutId);
                break;
            default:
                $response = $this->createResponse(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED, null);
                break;
        }

        return $response;
    }

    private function getWorkout($id)
    {
        $response = null;

        // id has to be numeric
        if (!is_numeric($id)) {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }
        else {
            $result = $this->workoutService->getWorkout($id);

            if (is_null($result)) {
                $response = $this->createResponse(HttpStatusCode::HTTP_NOT_FOUND, null);
            }
            else {
                $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
            }
        }

        return $response;
    }

    private function getAllWorkouts($startDate, $numDays)
    {
        $response = null;

        if ($this->validWorkoutsRequest($startDate, $numDays)) {
            $result = $this->workoutService->getWorkouts($startDate, $numDays);
            $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
        }
        else {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }

        return $response;
    }

    private function getWorkoutsByAo($aoId)
    {
        $response = null;

        // id has to be numeric
        if (is_numeric($aoId)) {
            $result = $this->workoutService->getWorkoutsByAo($aoId);
            $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
        }
        else {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }

        return $response;
    }

    private function getWorkoutsByQ($q)
    {
        $response = null;

        // id has to be numeric
        if (is_numeric($q)) {
            $result = $this->workoutService->getWorkoutsByQ($q);
            $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
        }
        else {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }

        return $response;
    }

    private function getWorkoutsByPax($paxId)
    {
        $response = null;

        // id has to be numeric
        if (is_numeric($paxId)) {
            $result = $this->workoutService->getWorkoutsByPax($paxId);
            $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
        }
        else {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }

        return $response;
    }

    private function deleteWorkout($id)
    {
        $response = null;

        // id has to be numeric
        if (!is_numeric($id)) {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }
        else {
            $result = $this->workoutService->getWorkout($id);

            if (is_null($result)) {
                $response = $this->createResponse(HttpStatusCode::HTTP_NOT_FOUND, null);
            }
            else {
                $success = $this->workoutService->deleteWorkout($id);
                if ($success) {
                    $response = $this->createResponse(HttpStatusCode::HTTP_OK, null);
                }
                else {
                    $response = $this->createResponse(HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR, null);
                }
            }
        }

        return $response;
    }

    private function validWorkoutsRequest($startDate, $numDays) {
        $valid = false;

        // validation rules
        if ($this->validStartDate($startDate) &&
            $this->validNumberOfDays($numDays)) {

            $valid = true;
        }

        return $valid;
    }

    private function validStartDate($startDate) {
        $valid = false;

        // if it's null or a valid date
        if (is_null($startDate) || DateUtil::validDate($startDate)) {
            $valid = true;
        }

        return $valid;
    }

    private function validNumberOfDays($numDays) {
        return true;
    }
}

?>
