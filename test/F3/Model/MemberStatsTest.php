<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\MemberStats
 * @backupGlobals enabled
 */
class MemberStatsTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new MemberStats();
        $model->setMemberId(1);
        $model->setMemberName('Splinter');
        $model->setNumWorkouts(84);
        $model->setNumQs(26);
        $model->setQRatio('31.0%');
        
        $expected = [
            'id' => 1,
            'name' => 'Splinter',
            'numWorkouts' => 84,
            'numQs' => 26,
            'qRatio' => '31.0%'
        ];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }
}
?>
