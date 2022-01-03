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
    public function processRequest($requestMethod, $workoutId)
    {
        $response = null;

        switch ($requestMethod) {
            case RequestMethod::GET:
                if (isset($workoutId)) {
                    $response = $this->getWorkout($workoutId);
                } else {
                    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
                    $numDays = isset($_GET['numberOfDays']) ? $_GET['numberOfDays'] : null;
                
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
