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
        $model->setAo(array("1" => "Spider Run"));
        $model->setBackblastUrl("url");
        $member = new Member();
        $member->setMemberId("1");
        $member->setF3Name("Splinter");
        $member->setAliases(null);
        $model->setPax(array("1" => $member));
        $model->setPaxCount("1");
        $model->setQ(array("1" => "Splinter"));
        $model->setPaxCount("12");
        $model->setTitle("title");
        $model->setWorkoutDate("2023-02-06");
        $model->setWorkoutId("123");

        $expected = [ "workout" => [
            "id" => "123",
            "backblastUrl" => "url",
            "title" => "title",
            "ao" => array("1" => "Spider Run"),
            "q" => array("1" => "Splinter"),
            "pax" => array("1" => $member),
            "paxCount" => "12",
            "workoutDate" => "2023-02-06"
        ]];

        $this->assertEquals($expected, $model->jsonSerialize(), "json mismatch");
    }
}
?>
