<?php

namespace Src\F3\Resource;

use Src\F3\Service\WorkoutService;

/*
 * The main REST resource controller for all supported ${REQUEST_METHOD}s
 */
class WorkoutResource {

    private $requestMethod;
    private $workoutId;
    private $workoutService;

    /*
     * Main constructor
     */
    public function __construct($requestMethod, $workoutId)
    {
        $this->requestMethod = $requestMethod;
        $this->workoutId = $workoutId;
        $this->workoutService = new WorkoutService();
    }

    /*
     * Handles all supported requests
     */
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->workoutId) {
                    $response = $this->getWorkout($this->workoutId);
                } else {
                    $response = $this->getAllWorkouts();
                };
                break;
            // case 'POST':
            //     $response = $this->createUserFromRequest();
            //     break;
            // case 'PUT':
            //     $response = $this->updateUserFromRequest($this->userId);
            //     break;
            // case 'DELETE':
            //     $response = $this->deleteUser($this->userId);
            //     break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        // set header and response based on previous functions
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllWorkouts()
    {
        // $result = $this->personGateway->findAll();

        $result = array(
            1 => array ('firstName' => 'Sarah', 'age' => 42),
            2 => array ('firstName' => 'Brian', 'age' => 42),
            3 => array ('firstName' => 'Allison', 'age' => 14),
            4 => array ('firstName' => 'Justin', 'age' => 12),
            5 => array ('firstName' => 'Sam', 'age' => 7)
        );

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getWorkout($id)
    {
        // $result = $this->personGateway->find($id);
        // if (! $result) {
        //     return $this->notFoundResponse();
        // }

        // $result = array ('workoutId' => $id, 'title' => 'some spider run workout');
        
        $result = $this->workoutService->getWorkout($id);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

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

    private function deleteUser($id)
    {
        $result = $this->personGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->personGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatePerson($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}

?>
