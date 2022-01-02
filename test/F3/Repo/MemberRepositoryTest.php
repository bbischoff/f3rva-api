<?php
namespace F3\Repo;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Repo\MemberRepository
 * @backupGlobals enabled
 */
class MemberRepositoryTest extends TestCase {
    
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
    
    public function testFindAll() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('query')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findAll();
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
                      ->method('fetch');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->find(1);
    }

    public function testFindByF3NameOrAlias() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findByF3NameOrAlias('Splinter');
    }

    public function testFindExistingAlias() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findExistingAlias(1, 2);
    }

    public function testFindDuplicateWorkoutMembers() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findDuplicateWorkoutMembers(1, 2);
    }

    public function testFindAliases() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findAliases('Splinter');
    }

    public function testFindAttendanceCountsDefaults() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE') === false, 'there should not be a WORKOUT_DATE descriminator');
                          $this->assertTrue(strpos($value, 'order by WORKOUT_COUNT') > 0, 'should be ordered by WORKOUT_COUNT');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findAttendanceCounts(null, null, null);
    }

    public function testFindAttendanceCountsWithDatesAndOrder() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'where w.WORKOUT_DATE between ? and ?') > 0, 'expected WORKOUT_DATE');
                          $this->assertTrue(strpos($value, 'order by WORKOUT_COUNT') > 0, 'should be ordered by WORKOUT_COUNT');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute')
                      ->with($this->callback(function ($params): bool {
                        // we should have 4 parameters thate aling to the 4 workout dates
                        $this->assertTrue(count($params) === 4, 'invalid number of date parameters');

                        return true;
                    } ));
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findAttendanceCounts('2021-01-01', '2021-12-31', 'workout');
    }

    public function testFindAttendanceCountsQOrder() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'order by Q_COUNT') > 0, 'should be ordered by Q_COUNT');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findAttendanceCounts(null, null, 'q');
    }

    public function testFindAttendanceCountsRatioOrder() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->with($this->callback(function ($value): bool {
                          // check that the sql was built the correct way
                          $this->assertTrue(strpos($value, 'order by Q_RATIO') > 0, 'should be ordered by Q_RATIO');

                          return true;
                      } ))
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetchAll');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findAttendanceCounts(null, null, 'ratio');
    }

    public function testMemberStats() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');
        $statementMock->expects($this->once())
                      ->method('fetch');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->findMemberStats(1);
    }

    public function testSave() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                              ->willReturn($statementMock);
        $this->pdoMock->method('lastInsertId')
                              ->willReturn(1);
                        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $id = $memberRepo->save('Splinter');
        $this->assertEquals(1, $id, 'inserted ID mismatch');
    }

    public function testDelete() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                              ->willReturn($statementMock);
                        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->delete(1);
    }

    public function testCreateAlias() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->createAlias(1, 2);
    }

    public function testRelinkWorkoutPax() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->relinkWorkoutPax(1, 2);
    }

    public function testRelinkWorkoutQ() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->relinkWorkoutQ(1, 2);
    }

    public function testMemberAlias() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->relinkMemberAlias(1, 2);
    }

    public function testRemoveMemberFromWorkout() {
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->pdoMock->method('prepare')
                      ->willReturn($statementMock);
        
        $statementMock->expects($this->once())
                      ->method('execute');

        $memberRepo = new MemberRepository($this->database);
        $memberRepo->removeMemberFromWorkout(1, 2);
    }
}

?>
