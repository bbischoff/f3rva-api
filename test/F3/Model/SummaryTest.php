<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\Summary
 * @backupGlobals enabled
 */
class SummaryTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new Summary();
        $model->setId(1);
        $model->setDescription('description');
        $model->setValue('value');
        
        $expected = [ 'summary' => [
            'id' => 1,
            'description' => 'description',
            'value' => 'value'
        ]];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }
}
?>
