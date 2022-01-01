<?php
namespace F3\Resource;

use PHPUnit\Framework\TestCase;
use F3\Model\Workout;
use F3\Service\WorkoutService;

/**
 * @covers \F3\Resource\WorkoutResource
 * @backupGlobals enabled
 */
class WorkoutResourceTest extends TestCase {

    public function testProcessRequestGetWorkout() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
		$workout->setWorkoutId('123');
        $workout->setTitle('Best Backblast Ever');

        $mock->method('getWorkout')
             ->willReturn($workout);

        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::GET, '123');

        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workout), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutNotNumeric() {
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::GET, 'abc');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null');
    }

    public function testProcessRequestGetWorkoutNotFound() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        $mock->method('getWorkouts')
             ->willReturn(null);
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::GET, '123');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_NOT_FOUND), $result[WorkoutResource::HEADER_KEY], 'numeric not found');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null');
    }

    public function testProcessRequestGetWorkouts() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
		$workout->setWorkoutId('123');
        $workout->setTitle('Best Backblast Ever');
        $workoutArray = array();
        $workoutArray['123'] = $workout;

        $mock->method('getWorkouts')
             ->willReturn($workoutArray);
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::GET, null);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workoutArray), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutsBadRequest() {
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $_GET['startDate'] = 'abc-not-a-date';

        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::GET, null);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on get bad request');
    }

    public function testProcessRequestDeleteWorkout() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
		$workout->setWorkoutId('123');
        $workout->setTitle('To be deleted');

        $mock->method('getWorkout')
             ->willReturn($workout);
        $mock->method('deleteWorkout')
             ->willReturn(true);
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::DELETE, '123');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body on delete');
    }

    public function testProcessRequestDeleteWorkoutBadRequest() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        $mock->method('deleteWorkout')
             ->willReturn(true);
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::DELETE, 'ABC');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on delete');
    }

    public function testProcessRequestDeleteWorkoutNotFound() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        $mock->method('getWorkout')
             ->willReturn(null);
        $mock->method('deleteWorkout')
             ->willReturn(true);
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::DELETE, '123');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_NOT_FOUND), $result[WorkoutResource::HEADER_KEY], 'delete not found');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on delete');
    }

    public function testProcessRequestDeleteWorkoutServerError() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
		$workout->setWorkoutId('123');
        $workout->setTitle('To be deleted');

        $mock->method('getWorkout')
             ->willReturn($workout);
        $mock->method('deleteWorkout')
             ->willReturn(false);
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest(RequestMethod::DELETE, '123');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body on delete');
    }

    public function testProcessRequestDefault() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
                
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutResource = new WorkoutResource($workoutService);
        $result = $workoutResource->processRequest('BADREQUEST', '123');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body');
    }
}
?>
