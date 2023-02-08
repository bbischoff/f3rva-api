<?php
namespace F3\Resource;

use F3\Model\Member;
use F3\Model\Response;
use PHPUnit\Framework\TestCase;
use F3\Model\Workout;
use F3\Service\WorkoutService;
use F3\Util\DataRetriever;

/**
 * @covers \F3\Resource\WorkoutResource
 * @backupGlobals enabled
 */
class WorkoutResourceTest extends TestCase {

    var $workoutServiceMock = null;
    var $dataRetrieverMock = null;

    /** @var \F3\Service\WorkoutService $workoutService */
    var $workoutService = null;
    /** @var \F3\Util\DataRetriever $dataRetriever */
    var $dataRetriever = null;

    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = '/workout';

        $this->workoutServiceMock = $this->getMockBuilder(WorkoutService::class)
                                         ->disableOriginalConstructor()
                                         ->getMock();
        $this->dataRetrieverMock = $this->getMockBuilder(DataRetriever::class)
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $this->workoutService = $this->workoutServiceMock;
        $this->dataRetriever = $this->dataRetrieverMock;
    }

    public function testProcessRequestGetWorkout() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('Best Backblast Ever');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');

        $this->workoutServiceMock->method('getWorkout')
                                 ->willReturn($workout);
        
        $_SERVER['REQUEST_URI'] = '/workout/123';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);

        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workout), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutNotNumeric() {
        $_SERVER['REQUEST_URI'] = '/workout/abc';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null');
    }

    public function testProcessRequestGetWorkoutNotFound() {
        $this->workoutServiceMock->method('getWorkouts')
                                 ->willReturn(null);
        
        $_SERVER['REQUEST_URI'] = '/workout/123';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_NOT_FOUND), $result[WorkoutResource::HEADER_KEY], 'numeric not found');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null');
    }

    public function testProcessRequestGetWorkouts() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('Best Backblast Ever');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');
        $workoutArray = array();
        $workoutArray['123'] = $workout;

        $this->workoutServiceMock->method('getWorkouts')
                                 ->willReturn($workoutArray);
        
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workoutArray), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutsBadRequest() {
        $_GET['startDate'] = 'abc-not-a-date';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on get bad request');
    }

    public function testProcessRequestGetWorkoutsByAo() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('get by ao');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');
        $workoutArray = array();
        $workoutArray['123'] = $workout;

        $this->workoutServiceMock->method('getWorkoutsByAo')
                                 ->willReturn($workoutArray);
        
        $_GET['ao'] = '5';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workoutArray), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutsByAoBadRequest() {
        $_GET['ao'] = 'abc';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on get bad request');
    }

    public function testProcessRequestGetWorkoutsByQ() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('get by ao');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');
        $workoutArray = array();
        $workoutArray['123'] = $workout;

        $this->workoutServiceMock->method('getWorkoutsByQ')
                                 ->willReturn($workoutArray);
        
        $_GET['q'] = '5';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workoutArray), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutsByQBadRequest() {
        $_GET['q'] = 'abc';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on get bad request');
    }

    public function testProcessRequestGetWorkoutsByPax() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('get by ao');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');
        $workoutArray = array();
        $workoutArray['123'] = $workout;

        $this->workoutServiceMock->method('getWorkoutsByPax')
                                 ->willReturn($workoutArray);
        
        $_GET['pax'] = '5';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($workoutArray), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestGetWorkoutsByPaxBadRequest() {
        $_GET['pax'] = 'abc';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::GET);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on get bad request');
    }

    public function testProcessRequestPostWorkout() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::SUCCESS);
        $response->setId('123');

        $this->workoutServiceMock->method('addWorkout')
                                 ->willReturn($response);
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn(json_encode(array('name' => 'value')));
        
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::POST);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($response), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestPostWorkoutErrorAdding() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::FAILURE);

        $this->workoutServiceMock->method('addWorkout')
                                 ->willReturn($response);
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn(json_encode(array('name' => 'value')));
        
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::POST);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'invalid add bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on bad request');
    }

    public function testProcessRequestPostWorkoutNoBody() {
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn('');
        
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::POST);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'no body bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on bad request');
    }

    public function testProcessRequestPutWorkout() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::SUCCESS);
        $response->setId('123');

        $this->workoutServiceMock->method('refreshWorkout')
                                 ->willReturn($response);
        
        $_SERVER['REQUEST_URI'] = '/workout/123';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($response), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestPutWorkoutNotFound() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::NOT_FOUND);

        $this->workoutServiceMock->method('refreshWorkout')
                                 ->willReturn($response);
        
        $_SERVER['REQUEST_URI'] = '/workout/94949494';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_NOT_FOUND), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on not found');
    }

    public function testProcessRequestPutWorkoutFailure() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::FAILURE);

        $this->workoutServiceMock->method('refreshWorkout')
                                 ->willReturn($response);
        
        $_SERVER['REQUEST_URI'] = '/workout/94949494';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on bad request');
    }

    public function testProcessRequestsPutWorkout() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::SUCCESS);

        $this->workoutServiceMock->method('refreshWorkouts')
                                 ->willReturn($response);
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn('{"numDays": "5"}');

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'expected OK');
        $this->assertNotNull($result[WorkoutResource::BODY_KEY], 'body not null on update');
    }

    public function testProcessRequestsPutWorkoutPartial() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::PARTIAL);

        $this->workoutServiceMock->method('refreshWorkouts')
                                 ->willReturn($response);
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn('{"numDays": "5"}');

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'expected OK');
        $this->assertNotNull($result[WorkoutResource::BODY_KEY], 'body not null on update');
    }

    public function testProcessRequestsPutWorkoutFailure() {
        // create mocked response
        $response = new Response();
        $response->setCode(Response::FAILURE);

        $this->workoutServiceMock->method('refreshWorkouts')
                                 ->willReturn($response);
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn('{"numDays": "5"}');

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'failure bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on update');
    }

    public function testProcessRequestsPutWorkoutNullNumDaysBadRequest() {
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn('');
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'no num days bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on update');
    }

    public function testProcessRequestsPutWorkoutNonNumericNumDaysBadRequest() {
        $this->dataRetrieverMock->method('retrieve')
                                ->willReturn('{"numDays": "asdf"}');
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'non numeric numDays bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on update');
    }

    public function testProcessRequestDeleteWorkout() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('To be deleted');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');

        $this->workoutServiceMock->method('getWorkout')
                                 ->willReturn($workout);
        $this->workoutServiceMock->method('deleteWorkout')
                                 ->willReturn(true);
        
        $_SERVER['REQUEST_URI'] = '/workout/123';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::DELETE);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body on delete');
    }

    public function testProcessRequestDeleteWorkoutBadRequest() {
        $this->workoutServiceMock->method('deleteWorkout')
             ->willReturn(true);
        
        $_SERVER['REQUEST_URI'] = '/workout/abc';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::DELETE);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_BAD_REQUEST), $result[WorkoutResource::HEADER_KEY], 'not numeric bad request');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on delete');
    }

    public function testProcessRequestDeleteWorkoutNotFound() {
        $this->workoutServiceMock->method('getWorkout')
                                 ->willReturn(null);
        $this->workoutServiceMock->method('deleteWorkout')
                                 ->willReturn(true);
        
        $_SERVER['REQUEST_URI'] = '/workout/123';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::DELETE);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_NOT_FOUND), $result[WorkoutResource::HEADER_KEY], 'delete not found');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'body null on delete');
    }

    public function testProcessRequestDeleteWorkoutServerError() {
        // create mocked response
        /** @var \F3\Model\Workout $workout */
        $workout = new Workout();
        $workout->setAo(array("1" => "Spider Run"));
        $workout->setBackblastUrl('http://someurl');
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $workout->setPax(array("1" => $member));
        $workout->setPaxCount("1");
        $workout->setQ(array("1" => "Splinter"));
        $workout->setTitle('To be deleted');
        $workout->setWorkoutDate("2023-02-06");
		$workout->setWorkoutId('123');

        $this->workoutServiceMock->method('getWorkout')
                                 ->willReturn($workout);
        $this->workoutServiceMock->method('deleteWorkout')
                                 ->willReturn(false);
        
        $_SERVER['REQUEST_URI'] = '/workout/123';

        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest(RequestMethod::DELETE);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body on delete');
    }

    public function testProcessRequestDefault() {
        $workoutResource = new WorkoutResource($this->workoutService, $this->dataRetriever);
        $result = $workoutResource->processRequest('BADREQUEST');
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body');
    }
}
?>
