<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\DayOfWeek
 * @backupGlobals enabled
 */
class DayOfWeekTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new DayOfWeek();
        $model->setDayId(1);
        $model->setCount(12);
        
        $expected = [ 'dayOfWeek' => [
            'id' => 1,
            'description' => 'Sunday',
            'count' => 12
        ]];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }

    public function testGetDayText() {
        $model = new DayOfWeek();
        $model->setDayId(1);
        $this->assertEquals('Sunday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(2);
        $this->assertEquals('Monday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(3);
        $this->assertEquals('Tuesday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(4);
        $this->assertEquals('Wednesday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(5);
        $this->assertEquals('Thursday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(6);
        $this->assertEquals('Friday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(7);
        $this->assertEquals('Saturday', $model->getDayText(), 'day of week text mismatch');

        $model->setDayId(8);
        $this->assertEquals('Unknown', $model->getDayText(), 'day of week text mismatch');
    }
}
?>
