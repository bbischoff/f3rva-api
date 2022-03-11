<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\AO
 * @backupGlobals enabled
 */
class AOTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new AO();
        $model->setId('1');
        $model->setDescription('ao description');
        
        $expected = [
            'id' => '1',
            'description' => 'ao description'
        ];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }
}
?>
