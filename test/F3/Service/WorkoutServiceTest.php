<?php
namespace F3\Service;

use F3\Dao\ScraperDao;
use F3\Repo\Database;
use F3\Repo\WorkoutRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Service\WorkoutService
 * @backupGlobals enabled
 */
class WorkoutServiceTest extends TestCase {

    /** @var \PHPUnit\Framework\MockObject\MockObject $workoutRepoMock */
    private $workoutRepoMock;
    /** @var \PHPUnit\Framework\MockObject\MockObject $memberServiceMock */
    private $memberServiceMock;
    /** @var \PHPUnit\Framework\MockObject\MockObject $scraperDaoMock */
    private $scraperDaoMock;
    /** @var \PHPUnit\Framework\MockObject\MockObject $databaseMock */
    private $databaseMock;
    /** @var \F3\Repo\WorkoutRepository $workoutRepo */
    private $workoutRepo;
    /** @var \F3\Service\MemberService $memberService */
    private $memberService;
    /** @var \F3\Dao\ScraperDao $scraperDao */
    private $scraperDao;
    /** @var \F3\Repo\Database $database */
    private $database;
    
    protected function setUp(): void
    {
        $this->workoutRepoMock = $this->getMockBuilder(WorkoutRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->memberServiceMock = $this->getMockBuilder(MemberService::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->scraperDaoMock = $this->getMockBuilder(ScraperDao::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->databaseMock = $this->getMockBuilder(Database::class)
                             ->disableOriginalConstructor()
                             ->getMock();
        
        $this->workoutRepo = $this->workoutRepoMock;
        $this->memberService = $this->memberServiceMock;
        $this->scraperDao = $this->scraperDaoMock;
        $this->database = $this->databaseMock;
    }

    public function testGetWorkout() {   
        // create mocked response
        $workout = array();
        $workout['WORKOUT_ID'] = '1';
		$workout['BACKBLAST_URL'] = 'https://f3rva.org/2021/12/30/test-post';
		$workout['PAX_COUNT'] = '5';
		$workout['TITLE'] = 'Test Post';
		$workout['WORKOUT_DATE'] = '2021-12-30';
        $workout['AO_ID'] = '2';
        $workout['AO'] = 'Spider Run';
        $workout['Q_ID'] = '3';
        $workout['Q'] = 'Splinter';
        $workoutArray = array();
        array_push($workoutArray, $workout);

        // mock the second AO and Q
        $workout2 = array();
        $workout2['WORKOUT_ID'] = '1';
        $workout2['AO_ID'] = '6';
        $workout2['AO'] = 'Hoedown';
        $workout2['Q_ID'] = '7';
        $workout2['Q'] = 'Lockjaw';
        array_push($workoutArray, $workout2);

        $this->workoutRepoMock->method('find')
                              ->willReturn($workoutArray);

        $pax = array();
        $pax['MEMBER_ID'] = '4';
        $pax['F3_NAME'] = 'Upchuck';
        $paxArray = array();
        array_push($paxArray, $pax);
        $this->workoutRepoMock->method('findPax')
                              ->willReturn($paxArray);

        $workoutService = new WorkoutService($this->memberService, $this->scraperDao, $this->workoutRepo, $this->database);
        $result = $workoutService->getWorkout(1);

        $this->assertEquals('1', $result->getWorkoutId(), 'workout id mismatch');
        $this->assertEquals('https://f3rva.org/2021/12/30/test-post', $result->getBackblastURL(), 'url mismatch');
        $this->assertEquals('5', $result->getPaxCount(), 'pax count mismatch');
        $this->assertEquals('Test Post', $result->getTitle(), 'title mismatch');
        $this->assertEquals('2021-12-30', $result->getWorkoutDate(), 'date mismatch');
        $this->assertEquals('Spider Run', $result->getAo()['2'], 'ao mismatch');
        $this->assertEquals('Hoedown', $result->getAo()['6'], 'ao2 mismatch');
        $this->assertEquals('Splinter', $result->getQ()['3'], 'q mismatch');
        $this->assertEquals('Lockjaw', $result->getQ()['7'], 'q2 mismatch');
        $this->assertEquals('4', $result->getPax()['4']->getMemberId(), 'pax member id mismatch');
        $this->assertEquals('Upchuck', $result->getPax()['4']->getF3Name(), 'pax name mismatch');
    }

    public function testGetWorkouts() {
        // create mocked response
        $workout = array();
        $workout['WORKOUT_ID'] = '1';
		$workout['BACKBLAST_URL'] = 'https://f3rva.org/2021/12/30/test-post';
		$workout['PAX_COUNT'] = '5';
		$workout['TITLE'] = 'Test Post';
		$workout['WORKOUT_DATE'] = '2021-12-30';
        $workout['AO_ID'] = '2';
        $workout['AO'] = 'Spider Run';
        $workout['Q_ID'] = '3';
        $workout['Q'] = 'Splinter';
        $workoutArray = array();
        array_push($workoutArray, $workout);

        // mock the second AO and Q
        $workout2 = array();
        $workout2['WORKOUT_ID'] = '1';
        $workout2['AO_ID'] = '6';
        $workout2['AO'] = 'Hoedown';
        $workout2['Q_ID'] = '7';
        $workout2['Q'] = 'Lockjaw';
        array_push($workoutArray, $workout2);

        $this->workoutRepoMock->method('findAllByDateRange')
                        ->willReturn($workoutArray);
        $this->workoutRepoMock->method('findMostRecentWorkoutDate')
                        ->willReturn('2021-12-30');

        $workoutService = new WorkoutService($this->memberService, $this->scraperDao, $this->workoutRepo, $this->database);
        $result = $workoutService->getWorkouts();

        $this->assertEquals('1', $result['1']->getWorkoutId(), 'workout id mismatch');
        $this->assertEquals('https://f3rva.org/2021/12/30/test-post', $result['1']->getBackblastURL(), 'url mismatch');
        $this->assertEquals('5', $result['1']->getPaxCount(), 'pax count mismatch');
        $this->assertEquals('Test Post', $result['1']->getTitle(), 'title mismatch');
        $this->assertEquals('2021-12-30', $result['1']->getWorkoutDate(), 'date mismatch');
        $this->assertEquals('Spider Run', $result['1']->getAo()['2'], 'ao mismatch');
        $this->assertEquals('Hoedown', $result['1']->getAo()['6'], 'ao2 mismatch');
        $this->assertEquals('Splinter', $result['1']->getQ()['3'], 'q mismatch');
        $this->assertEquals('Lockjaw', $result['1']->getQ()['7'], 'q2 mismatch');
    }

    public function testDeleteWorkout() {
        $pdoMock = $this->getMockBuilder(\PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();
    
        $this->databaseMock->method('getDatabase')
                           ->willReturn($pdoMock);

        $workoutService = new WorkoutService($this->memberService, $this->scraperDao, $this->workoutRepo, $this->database);
        $result = $workoutService->deleteWorkout(1);

        $this->assertTrue($result, 'delete expected to be true');
    }


    public function testDeleteWorkoutFalure() {
        $pdoMock = $this->getMockBuilder(\PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();    

        $this->databaseMock->method('getDatabase')
                     ->willReturn($pdoMock);
        $pdoMock->method('beginTransaction')
                ->willThrowException(new \Exception());

        $workoutService = new WorkoutService($this->memberService, $this->scraperDao, $this->workoutRepo, $this->database);
        $result = $workoutService->deleteWorkout(1);

        $this->assertFalse($result, 'delete expected to be false');
    }
}
