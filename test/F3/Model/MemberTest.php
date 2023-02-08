<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\Member
 * @backupGlobals enabled
 */
class MemberTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new Member();
        $model->setMemberId(1);
        $model->setF3Name("Splinter");
        $model->setAliases(array(
            "Splendid Splinter" => "Splendid Splinter"
        ));
        
        $expected = [ "member" => [
            "id" => 1,
            "f3Name" => "Splinter",
            "aliases" => array(
                "Splendid Splinter" => "Splendid Splinter"
            )
        ]];

        $this->assertEquals($expected, $model->jsonSerialize(), "json mismatch");
    }
}
?>
