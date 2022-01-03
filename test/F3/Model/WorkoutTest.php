<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\Workout
 * @backupGlobals enabled
 */
class WorkoutTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new Workout();
        $model->setWorkoutId(1);
        $model->setBackblastUrl('url');
        $model->setTitle('title');
        $model->setAo('ao');
        $model->setQ('q');
        $model->setPax('pax');
        $model->setPaxCount('count');
        $model->setWorkoutDate('date');
        
        $expected = [ 'workout' => [
            'id' => 1,
            'backblastUrl' => 'url',
            'title' => 'title',
            'ao' => 'ao',
            'q' => 'q',
            'pax' => 'pax',
            'paxCount' => 'count',
            'workoutDate' => 'date'
        ]];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }
}
?>
