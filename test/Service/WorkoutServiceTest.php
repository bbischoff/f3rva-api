<?php

use F3\Dao\ScraperDao;
use F3\Repo\Database;
use F3\Repo\WorkoutRepository;
use F3\Service\MemberService;
use F3\Service\WorkoutService;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Service\WorkoutService
 * @backupGlobals enabled
 */
class WorkoutServiceTest extends TestCase {

    public function testGetWorkout() {
        $workoutRepoMock = $this->getMockBuilder(WorkoutRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $memberServiceMock = $this->getMockBuilder(MemberService::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $scraperDaoMock = $this->getMockBuilder(ScraperDao::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $databaseMock = $this->getMockBuilder(Database::class)
                             ->disableOriginalConstructor()
                             ->getMock();
   
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

        $workoutRepoMock->method('find')
                        ->willReturn($workoutArray);

        $pax = array();
        $pax['MEMBER_ID'] = '4';
        $pax['F3_NAME'] = 'Upchuck';
        $paxArray = array();
        array_push($paxArray, $pax);
        $workoutRepoMock->method('findPax')
                        ->willReturn($paxArray);

        /** @var \F3\Repo\WorkoutRepository $workoutRepo */
        $workoutRepo = $workoutRepoMock;
        /** @var \F3\Service\MemberService $memberService */
        $memberService = $memberServiceMock;
        /** @var \F3\Dao\ScraperDao $scraperDao */
        $scraperDao = $scraperDaoMock;
        /** @var \F3\Repo\Database $database */
        $database = $databaseMock;

        $workoutService = new WorkoutService($memberService, $scraperDao, $workoutRepo, $database);
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
        $workoutRepoMock = $this->getMockBuilder(WorkoutRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $memberServiceMock = $this->getMockBuilder(MemberService::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $scraperDaoMock = $this->getMockBuilder(ScraperDao::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $databaseMock = $this->getMockBuilder(Database::class)
                             ->disableOriginalConstructor()
                             ->getMock();

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

        $workoutRepoMock->method('findAllByDateRange')
                        ->willReturn($workoutArray);
        $workoutRepoMock->method('findMostRecentWorkoutDate')
                        ->willReturn('2021-12-30');

        /** @var \F3\Repo\WorkoutRepository $workoutRepo */
        $workoutRepo = $workoutRepoMock;
        /** @var \F3\Service\MemberService $memberService */
        $memberService = $memberServiceMock;
        /** @var \F3\Dao\ScraperDao $scraperDao */
        $scraperDao = $scraperDaoMock;
        /** @var \F3\Repo\Database $database */
        $database = $databaseMock;

        $workoutService = new WorkoutService($memberService, $scraperDao, $workoutRepo, $database);
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
        $workoutRepoMock = $this->getMockBuilder(WorkoutRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $memberServiceMock = $this->getMockBuilder(MemberService::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $scraperDaoMock = $this->getMockBuilder(ScraperDao::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $databaseMock = $this->getMockBuilder(Database::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $pdoMock = $this->getMockBuilder(PDO::class)
                               ->disableOriginalConstructor()
                               ->getMock();
    
        /** @var \F3\Repo\WorkoutRepository $workoutRepo */
        $workoutRepo = $workoutRepoMock;
        /** @var \F3\Service\MemberService $memberService */
        $memberService = $memberServiceMock;
        /** @var \F3\Dao\ScraperDao $scraperDao */
        $scraperDao = $scraperDaoMock;
        /** @var \F3\Repo\Database $database */
        $database = $databaseMock;

        $databaseMock->method('getDatabase')
                     ->willReturn($pdoMock);

        $workoutService = new WorkoutService($memberService, $scraperDao, $workoutRepo, $database);
        $result = $workoutService->deleteWorkout(1);

        $this->assertTrue($result, 'delete expected to be true');
    }


    public function testDeleteWorkoutFalure() {
        $workoutRepoMock = $this->getMockBuilder(WorkoutRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $memberServiceMock = $this->getMockBuilder(MemberService::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $scraperDaoMock = $this->getMockBuilder(ScraperDao::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $databaseMock = $this->getMockBuilder(Database::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $pdoMock = $this->getMockBuilder(PDO::class)
                               ->disableOriginalConstructor()
                               ->getMock();
    
        /** @var \F3\Repo\WorkoutRepository $workoutRepo */
        $workoutRepo = $workoutRepoMock;
        /** @var \F3\Service\MemberService $memberService */
        $memberService = $memberServiceMock;
        /** @var \F3\Dao\ScraperDao $scraperDao */
        $scraperDao = $scraperDaoMock;
        /** @var \F3\Repo\Database $database */
        $database = $databaseMock;

        $databaseMock->method('getDatabase')
                     ->willReturn($pdoMock);
        $pdoMock->method('beginTransaction')
                ->willThrowException(new Exception());

        $workoutService = new WorkoutService($memberService, $scraperDao, $workoutRepo, $database);
        $result = $workoutService->deleteWorkout(1);

        $this->assertFalse($result, 'delete expected to be false');
    }
}
