<?php

namespace F3\Resource;

use Exception;
use F3\Service\WorkoutService;
use F3\Util\DateUtil;

/*
 * The main REST resource controller for all supported ${REQUEST_METHOD}s
 */
class WorkoutResource {

    private $requestMethod;
    private $workoutId;
    private $workoutService;

    private const HEADER_KEY = 'status_code_header';
    private const BODY_KEY = 'body';

    /**
     * Main constructor
     */
    public function __construct($requestMethod, $workoutId)
    {
        $this->requestMethod = $requestMethod;
        $this->workoutId = $workoutId;
        $this->workoutService = new WorkoutService();
    }

    /**
     * Handles all supported requests
     */
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case RequestMethod::GET:
                if (isset($this->workoutId)) {
                    $response = $this->getWorkout($this->workoutId);
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
                $response = $this->deleteWorkout($this->workoutId);
                break;
            default:
                $response = $this->createResponse(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED, null);
                break;
        }

        // set header and response based on previous functions
        header($response[self::HEADER_KEY]);
        if ($response[self::BODY_KEY]) {
            echo $response[self::BODY_KEY];
        }
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

    private function createResponse($statusCode, $body) {
        $response[self::HEADER_KEY] = HttpStatusCode::httpHeaderFor($statusCode);
        $response[self::BODY_KEY] = $body;

        return $response;
    }

    /*
    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->personGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }
    */

}

?>
