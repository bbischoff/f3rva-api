<?php
namespace F3\Service;

use F3\Model\Member;
use F3\Repo\Database;
use F3\Repo\MemberRepository;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Service\MemberService
 * @backupGlobals enabled
 */
class MemberServiceTest extends TestCase {

    /** @var \PHPUnit\Framework\MockObject\MockObject $memberRepoMock */
    private $memberRepoMock;
    /** @var \PHPUnit\Framework\MockObject\MockObject $databaseMock */
    private $databaseMock;
    /** @var \F3\Repo\MemberRepository $memberRepo */
    private $memberRepo;
    /** @var \F3\Repo\Database $database */
    private $database;
    
    protected function setUp(): void
    {
        $this->memberRepoMock = $this->getMockBuilder(MemberRepository::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->databaseMock = $this->getMockBuilder(Database::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();
        
        $this->memberRepo = $this->memberRepoMock;
        $this->database = $this->databaseMock;
    }
    
    public function testGetMembers() {
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';
        $memberArray = array();
        $memberArray['1'] = $member;

        $this->memberRepoMock->method('findAll')
                             ->willReturn($memberArray);

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->getMembers();

        $this->assertEquals('1', $result["1"]->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result["1"]->getF3Name(), 'name mismatch');
    }

    public function testGetMemberByName() {
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';

        $this->memberRepoMock->method('findByF3NameOrAlias')
                             ->willReturn($member);

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->getMember('Splinter');

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result->getF3Name(), 'name mismatch');
    }

    public function testGetMemberById() {
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';

        $this->memberRepoMock->method('find')
                             ->willReturn($member);
        
        $alias = array();
        $alias["F3_ALIAS"] = 'Splint';
        $aliasArray = array();
        array_push($aliasArray, $alias);

        $this->memberRepoMock->method('findAliases')
                             ->willReturn($aliasArray);

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->getMemberById(1);

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result->getF3Name(), 'name mismatch');
        $this->assertEquals('Splint', $result->getAliases()['Splint'], 'alias mismatch');
    }

    public function testGetOrAddMemberFound() {
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';

        $this->memberRepoMock->method('findByF3NameOrAlias')
             ->willReturn($member);

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->getOrAddMember('Splinter');

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result->getF3Name(), 'name mismatch');
    }

    public function testGetOrAddMemberNotFound() {
        $this->memberRepoMock->method('findByF3NameOrAlias')
                             ->willReturn(null);
        $this->memberRepoMock->method('save')
                             ->willReturn('2');

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->getOrAddMember('THE Yankee Aggressor');

        $this->assertEquals('2', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('THE Yankee Aggressor', $result->getF3Name(), 'name mismatch');
    }

    public function testGetMemberStats() {
        // create mocked response
        $memberStats = array();
        $memberStats["NUM_WORKOUTS"] = '52';
        $memberStats["NUM_QS"] = '24';
        $memberStats["Q_RATIO"] = '46.2%';

        $this->memberRepoMock->method('findMemberStats')
                             ->willReturn($memberStats);

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->getMemberStats(1);

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('52', $result->getNumWorkouts(), 'number of workouts mismatch');
        $this->assertEquals('24', $result->getNumQs(), 'number of qs mismatch');
        $this->assertEquals('46.2%', $result->getQRatio(), 'q ratio mismatch');
    }

    public function testAssignAlias() {
        $pdoMock = $this->getMockBuilder(PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();
    
        $this->databaseMock->method('getDatabase')
                           ->willReturn($pdoMock);

        $pdoMock->expects($this->once())
                ->method('beginTransaction');
        $this->memberRepoMock->method('findExistingAlias')
                             ->willReturn(false);
        $this->memberRepoMock->method('findDuplicateWorkoutMembers')
                             ->willReturn(array());
        $this->memberRepoMock->expects($this->once())
                             ->method('createAlias');
        $this->memberRepoMock->expects($this->once())
                             ->method('relinkWorkoutPax');
        $this->memberRepoMock->expects($this->once())
                             ->method('relinkWorkoutQ');
        $this->memberRepoMock->expects($this->once())
                             ->method('relinkMemberAlias');
        $this->memberRepoMock->expects($this->once())
                             ->method('delete');

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->assignAlias(1, 2);
    }

    public function testAssignAliasAlreadyExisting() {
        $pdoMock = $this->getMockBuilder(PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();
    
        $this->databaseMock->method('getDatabase')
                           ->willReturn($pdoMock);

        $this->memberRepoMock->method('findExistingAlias')
                             ->willReturn(true);
        $this->memberRepoMock->method('findDuplicateWorkoutMembers')
                             ->willReturn(array());
        $this->memberRepoMock->expects($this->never())
                             ->method('createAlias');

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->assignAlias(1, 2);
    }

    public function testAssignAliasRemoveDuplicates() {
        $pdoMock = $this->getMockBuilder(PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();
    
        $this->databaseMock->method('getDatabase')
                           ->willReturn($pdoMock);

        $this->memberRepoMock->method('findExistingAlias')
                             ->willReturn(true);
        
        $dupes = array();
        $dupeEntry = array();
        $dupeEntry['WORKOUT_ID'] = '1';
        array_push($dupes, $dupeEntry);

        $this->memberRepoMock->method('findDuplicateWorkoutMembers')
                             ->willReturn($dupes);
        $this->memberRepoMock->expects($this->once())
                             ->method('removeMemberFromWorkout');

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->assignAlias(1, 2);
    }

    public function testAssignAliasFailure() {
        $pdoMock = $this->getMockBuilder(PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();
    
        $this->databaseMock->method('getDatabase')
                           ->willReturn($pdoMock);
        $pdoMock->method('beginTransaction')
                ->willThrowException(new \Exception());

        $pdoMock->expects($this->once())
                ->method('rollBack');

        $memberService = new MemberService($this->memberRepo, $this->database);
        $result = $memberService->assignAlias(1, 2);
    }
}
?>
