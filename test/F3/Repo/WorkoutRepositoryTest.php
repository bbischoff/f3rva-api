<?php
namespace F3\Repo;

use F3\Util\DateUtil;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Repo\WorkoutRepository
 * @backupGlobals enabled
 */
class WorkoutRepositoryTest extends TestCase {
    
    /** @var \PHPUnit\Framework\MockObject\MockObject $databaseMock */
    private $databaseMock;
    /** @var \PHPUnit\Framework\MockObject\MockObject $pdoMock */
    private $pdoMock;
    /** @var \F3\Repo\Database $database */
    private $database;
    /** @var \PDO\PDO $pdo */
    private $pdo;

    protected function setUp(): void
    {
        $this->databaseMock = $this->getMockBuilder(Database::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();
        $this->pdoMock = $this->getMockBuilder(\PDO::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        
        $this->database = $this->databaseMock;
        $this->pdo = $this->pdoMock;

        $this->databaseMock->method('getDatabase')
                           ->willReturn($this->pdoMock);
    }

    public function testDeleteWorkoutAos() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->deleteWorkoutAos(1);
    }

    public function testDeleteWorkoutMembers() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->deleteWorkoutMembers(1);
    }

    public function testDeleteWorkoutQs() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->deleteWorkoutQs(1);
    }

    public function testDeleteWorkout() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->deleteWorkout(1);
    }

    public function testFind() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->find(1);
    }

    public function testFindAllByDateRange() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAllByDateRange('2021-12-26', '2021-12-31');
    }

    public function testFindAllByAo() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAllByAo(1);
    }

    public function testFindAllByQ() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAllByQ(1);
    }

    public function testFindAllByPax() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAllByPax(1);
    }

    public function testFindCount() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') > 0, 'expected WORKOUT_DATE');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute')
                      ->with($this->callback(function ($value): bool {
                         // should have stard and end date parameters
                         $this->assertEquals(2, count($value), 'expected two parameters');
                         $this->assertEquals('2021-12-26', $value[0], 'start date mismatch');
                         $this->assertEquals('2021-12-31', $value[1], 'end date mismatch');

                         return true;
                     } ));
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findCount('2021-12-26', '2021-12-31');
    }

    public function testFindCountNoDates() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') === false, 'did not expected WORKOUT_DATE');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findCount(null, null);
    }

    public function testFindPax() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findPax(1);
    }

    public function testFindWorkoutMember() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findWorkoutMember(1, 1);
    }

    public function testFindWorkoutQ() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findWorkoutQ(1, 1);
    }

    public function testFindAo() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAo(1);
    }

    public function testFindWorktoutsGroupByDayOfWeek() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') > 0, 'expected WORKOUT_DATE');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute')
                      ->with($this->callback(function ($value): bool {
                         // should have stard and end date parameters
                         $this->assertEquals(2, count($value), 'expected two parameters');
                         $this->assertEquals('2021-12-26', $value[0], 'start date mismatch');
                         $this->assertEquals('2021-12-31', $value[1], 'end date mismatch');

                         return true;
                     } ));
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findWorkoutsGroupByDayOfWeek('2021-12-26', '2021-12-31');
    }

    public function testFindWorktoutsGroupByDayOfWeekNoDates() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') === false, 'did not expected WORKOUT_DATE');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findWorkoutsGroupByDayOfWeek(null, null);
    }

    public function testFindAverageAttendanceByAo() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') > 0, 'expected WORKOUT_DATE');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute')
                      ->with($this->callback(function ($value): bool {
                         // should have stard and end date parameters
                         $this->assertEquals(2, count($value), 'expected two parameters');
                         $this->assertEquals('2021-12-26', $value[0], 'start date mismatch');
                         $this->assertEquals('2021-12-31', $value[1], 'end date mismatch');

                         return true;
                     } ));
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAverageAttendanceByAO('2021-12-26', '2021-12-31');
    }

    public function testFindAverageAttendanceByAoNoDates() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') === false, 'did not expected WORKOUT_DATE');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->findAverageAttendanceByAO(null, null);
    }

    public function testFindMostRecentWorkoutDate() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $results = array();
        $results["MAX_DATE"] = '2021-12-31';

        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch')
                      ->willReturn($results);

        $workoutRepo = new WorkoutRepository($this->database);
        $recent = $workoutRepo->findMostRecentWorkoutDate();
        $this->assertEquals($results["MAX_DATE"], $recent, 'most recent date mismatch');
    }

    public function testSave() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                       ->willReturn($statementMock);
        $this->pdoMock->method('lastInsertId')
                              ->willReturn('1');
                        
        $statementMock->expects($this->once())
                      ->method('execute')
                      ->with($this->callback(function ($array): bool {
                        // check parameters passed correctly
                        $this->assertEquals('test title', $array[0], 'title mismatch');
                        $this->assertEquals('2021-12-31', $array[1], 'date format mismatch');
                        $this->assertEquals('https://testurl', $array[2], 'url mismatch');

                        return true;
                      } ));

        $workoutRepo = new WorkoutRepository($this->database);

        $dateArray = array();
        $dateArray['year'] = '2021';
        $dateArray['month'] = '12';
        $dateArray['day'] = '31';
        $id = $workoutRepo->save('test title', $dateArray, 'https://testurl');
        $this->assertEquals('1', $id, 'inserted ID mismatch');
    }

    public function testSaveDefaultDate() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                       ->willReturn($statementMock);
        $this->pdoMock->method('lastInsertId')
                              ->willReturn('1');
                        
        $statementMock->expects($this->once())
                      ->method('execute')
                      ->with($this->callback(function ($array): bool {
                          // check parameters passed correctly
                          $this->assertEquals('test title', $array[0], 'title mismatch');
                          $this->assertEquals(DateUtil::getDefaultDate(null), $array[1], 'date format mismatch');
                          $this->assertEquals('https://testurl', $array[2], 'url mismatch');

                          return true;
                      } ));

        $workoutRepo = new WorkoutRepository($this->database);

        $id = $workoutRepo->save('test title', null, 'https://testurl');
        $this->assertEquals('1', $id, 'inserted ID mismatch');
    }

    public function testSaveWorkoutMember() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->method('fetch')
                      ->willReturn(1);

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->saveWorkoutMember(1, 1);
    }

    public function testSaveWorkoutMemberNotFound() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->method('fetch')
                      ->willReturn(false);
        $statementMock->expects($this->exactly(2))
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->saveWorkoutMember(1, 1);
    }

    public function testSaveWorkoutQ() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->method('fetch')
                      ->willReturn(1);

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->saveWorkoutQ(1, 1);
    }

    public function testSaveWorkoutQNotFound() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->method('fetch')
                      ->willReturn(false);
        $statementMock->expects($this->exactly(2))
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->saveWorkoutQ(1, 1);
    }

    public function testSaveWorkoutAo() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->saveWorkoutAo(1, 1);
    }

    public function testSelectOrAddAo() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $aoArray = array();
        $aoArray['AO_ID'] = '5';
        $aoArray['DESCRIPTION'] = 'test description';

        $statementMock->method('fetch')
                      ->willReturn($aoArray);

        $workoutRepo = new WorkoutRepository($this->database);
        $result = $workoutRepo->selectOrAddAo('description');

        $this->assertEquals('5', $result->aoId, 'aoId mismatch');
        $this->assertEquals('test description', $result->description, 'description mismatch');
    }

    public function testSelectOrAddAoNotFound() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                              ->willReturn($statementMock);
        $this->pdoMock->method('lastInsertId')
                              ->willReturn('6');
                        
        $statementMock->method('fetch')
                      ->willReturn(false);

        $workoutRepo = new WorkoutRepository($this->database);
        $result = $workoutRepo->selectOrAddAo('description');

        $this->assertEquals('6', $result->aoId, 'aoId mismatch');
        $this->assertEquals('description', $result->description, 'description mismatch');
    }

    public function testUpdate() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $workoutRepo = new WorkoutRepository($this->database);
        $workoutRepo->update(1, 'title', null, 'https://testurl');
    }

}
?>
