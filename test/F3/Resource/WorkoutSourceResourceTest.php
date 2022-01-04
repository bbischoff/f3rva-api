<?php
namespace F3\Resource;

use PHPUnit\Framework\TestCase;
use F3\Model\Workout;
use F3\Service\WorkoutService;

/**
 * @covers \F3\Resource\WorkoutSourceResource
 * @backupGlobals enabled
 */
class WorkoutSourceResourceTest extends TestCase {

    public function testProcessRequestGetWorkout() {
        $mock = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        
        $mockedResult = array (
            'author' => 'splinter'
        );
        $mock->method('parsePost')
             ->willReturn($mockedResult);

        $_GET['url'] = 'https://testurl';
        
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $mock;
        $workoutSourceResource = new WorkoutSourceResource($workoutService);
        $result = $workoutSourceResource->processRequest(RequestMethod::GET);

        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($mockedResult), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutBadRequest() {
        /** @var \F3\Service\WorkoutService $workoutService */
        $workoutService = $this->getMockBuilder(WorkoutService::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $workoutSourceResource = new WorkoutSourceResource($workoutService);
        $result = $workoutSourceResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'expected bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null');
    }
}
?>
