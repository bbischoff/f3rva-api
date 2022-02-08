<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\Version
 * @backupGlobals enabled
 */
class VersionTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new Version();
        $model->setVersion('v1');
        
        $expected = [ 'version' => 'v1' ];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }
}
?>
