<?php
namespace F3\Repo;

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
}
?>
