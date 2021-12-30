<?php

use F3\Model\Member;
use F3\Repo\MemberRepository;
use F3\Service\MemberService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Service\MemberService
 * @backupGlobals enabled
 */
class MemberServiceTest extends TestCase {

    public function testGetMembers() {
        $mock = $this->getMockBuilder(MemberRepository::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';
        $memberArray = array();
        $memberArray['1'] = $member;

        $mock->method('findAll')
             ->willReturn($memberArray);

        /** @var \F3\Repo\MemberRepository $memberRepo */
        $memberRepo = $mock;
        $memberService = new MemberService($memberRepo);
        $result = $memberService->getMembers();

        $this->assertEquals('1', $result["1"]->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result["1"]->getF3Name(), 'name mismatch');
    }

    public function testGetMemberByName() {
        $mock = $this->getMockBuilder(MemberRepository::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';

        $mock->method('findByF3NameOrAlias')
             ->willReturn($member);

        /** @var \F3\Repo\MemberRepository $memberRepo */
        $memberRepo = $mock;
        $memberService = new MemberService($memberRepo);
        $result = $memberService->getMember('Splinter');

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result->getF3Name(), 'name mismatch');
    }

    public function testGetMemberById() {
        $mock = $this->getMockBuilder(MemberRepository::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';

        $mock->method('find')
             ->willReturn($member);
        
        $alias = array();
        $alias["F3_ALIAS"] = 'Splint';
        $aliasArray = array();
        array_push($aliasArray, $alias);

        $mock->method('findAliases')
             ->willReturn($aliasArray);

        /** @var \F3\Repo\MemberRepository $memberRepo */
        $memberRepo = $mock;
        $memberService = new MemberService($memberRepo);
        $result = $memberService->getMemberById(1);

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result->getF3Name(), 'name mismatch');
        $this->assertEquals('Splint', $result->getAliases()['Splint'], 'alias mismatch');
    }

    public function testGetOrAddMemberFound() {
        $mock = $this->getMockBuilder(MemberRepository::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        // create mocked response
        $member = array();
        $member["MEMBER_ID"] = '1';
        $member["F3_NAME"] = 'Splinter';

        $mock->method('findByF3NameOrAlias')
             ->willReturn($member);

        /** @var \F3\Repo\MemberRepository $memberRepo */
        $memberRepo = $mock;
        $memberService = new MemberService($memberRepo);
        $result = $memberService->getOrAddMember('Splinter');

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('Splinter', $result->getF3Name(), 'name mismatch');
    }

    public function testGetOrAddMemberNotFound() {
        $mock = $this->getMockBuilder(MemberRepository::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        $mock->method('findByF3NameOrAlias')
             ->willReturn(null);
        $mock->method('save')
             ->willReturn('2');

        /** @var \F3\Repo\MemberRepository $memberRepo */
        $memberRepo = $mock;
        $memberService = new MemberService($memberRepo);
        $result = $memberService->getOrAddMember('THE Yankee Aggressor');

        $this->assertEquals('2', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('THE Yankee Aggressor', $result->getF3Name(), 'name mismatch');
    }

    public function testGetMemberStats() {
        $mock = $this->getMockBuilder(MemberRepository::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        // create mocked response
        $memberStats = array();
        $memberStats["NUM_WORKOUTS"] = '52';
        $memberStats["NUM_QS"] = '24';
        $memberStats["Q_RATIO"] = '46.2%';

        $mock->method('findMemberStats')
             ->willReturn($memberStats);

        /** @var \F3\Repo\MemberRepository $memberRepo */
        $memberRepo = $mock;
        $memberService = new MemberService($memberRepo);
        $result = $memberService->getMemberStats(1);

        $this->assertEquals('1', $result->getMemberId(), 'member id mismatch');
        $this->assertEquals('52', $result->getNumWorkouts(), 'number of workouts mismatch');
        $this->assertEquals('24', $result->getNumQs(), 'number of qs mismatch');
        $this->assertEquals('46.2%', $result->getQRatio(), 'q ratio mismatch');
    }

    public function testAssignAlias() {
        // TODO:  need to refactor code to remove singleton dependency
        $this->assertTrue(true);
    }
}
?>
