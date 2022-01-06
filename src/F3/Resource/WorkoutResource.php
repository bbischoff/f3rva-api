<?php

namespace F3\Resource;

use F3\Model\Response;
use F3\Service\WorkoutService;
use F3\Util\DataRetriever;
use F3\Util\DateUtil;

/*
 * The main REST resource controller for all supported ${REQUEST_METHOD}s
 */
class WorkoutResource extends AbstractResource {

    private $dataRetriever;
    private $workoutService;

    /**
     * Main constructor
     */
    public function __construct(WorkoutService $workoutService, DataRetriever $dataRetriever)
    {
        $this->workoutService = $workoutService;
        $this->dataRetriever = $dataRetriever;
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

        switch ($requestMethod) {
            case RequestMethod::GET:
                // retrieve various potential filters
                $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
                $numDays = isset($_GET['numberOfDays']) ? $_GET['numberOfDays'] : null;
                $ao = isset($_GET['ao']) ? $_GET['ao'] : null;
                $q = isset($_GET['q']) ? $_GET['q'] : null;
                $pax = isset($_GET['pax']) ? $_GET['pax'] : null;

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
            case RequestMethod::POST:
                $jsonStr = $this->dataRetriever->retrieve();
                $json = json_decode($jsonStr);
            
                $response = $this->createWorkout($json);
                break;
            case RequestMethod::PUT:
                if (isset($workoutId)) {
                    $response = $this->updateWorkout($workoutId);
                } else {
                    $jsonStr = $this->dataRetriever->retrieve();
                    $json = json_decode($jsonStr);

                    $response = $this->updateWorkouts($json);
                }
                break;
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

    private function createWorkout($json) {
        $response = null;

        if (is_null($json)) {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }
        else {
            $result = $this->workoutService->addWorkout($json);

            if ($result->getCode() == Response::SUCCESS) {
                $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
            }
            else {
                $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
            }
        }

        return $response;
    }

    private function updateWorkout($workoutId) {
        // PRECONDITION:  $workoutId is not null
        $response = null;

        $result = $this->workoutService->refreshWorkout($workoutId);

        if ($result->getCode() == Response::SUCCESS) {
            $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
        }
        else if ($result->getCode() == Response::NOT_FOUND) {
            $response = $this->createResponse(HttpStatusCode::HTTP_NOT_FOUND, null);
        }
        else {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }

        return $response;
    }

    private function updateWorkouts($json) {
        $response = null;

        if (is_null($json) || empty($json->numDays)) {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }
        else if (!is_numeric($json->numDays)) {
            $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
        }
        else {
            $result = $this->workoutService->refreshWorkouts($json->numDays);

            if ($result->getCode() == Response::SUCCESS || $result->getCode() == Response::PARTIAL) {
                $response = $this->createResponse(HttpStatusCode::HTTP_OK, json_encode($result));
            }
            else {
                $response = $this->createResponse(HttpStatusCode::HTTP_BAD_REQUEST, null);
            }
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
