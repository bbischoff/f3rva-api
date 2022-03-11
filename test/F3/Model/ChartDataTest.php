<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\ChartData
 * @backupGlobals enabled
 */
class ChartDataTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new ChartData();
        $labels = array( 
            "2021" => "2021",
            "2020" => "2020"
        );
        $model->setXLabels($labels);
        $series = array(
            "1/1" => array (
                "2021" => "5",
                "2020" => "3"
            ),
            "1/2" => array (
                "2021" => "9",
                "2020" => "7"
            )
        );
        $model->setSeries($series);
        
        $expected = [
            'xLabels' => $labels,
            'series' => $series
        ];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }

    public function testGetSeriesImploded() {
        $model = new ChartData();
        $labels = array( 
            "2021" => "2021",
            "2020" => "2020"
        );
        $model->setXLabels($labels);
        $series = array(
            "1/1" => array (
                "2021" => "5",
                "2020" => "3"
            ),
            "1/2" => array (
                "2021" => "9",
                "2020" => "7"
            ),
            "1/3" => "11"
        );
        $model->setSeries($series);
        
        $expected = [
            'xLabels' => $labels,
            'series' => $series
        ];

        $this->assertEquals('[1/1,5,3],[1/2,9,7],[1/3,11]', $model->getSeriesImploded(), 'series imploded mismatch');
    }

    public function testGetSeriesKeysImploded() {
        $model = new ChartData();
        $labels = array( 
            "2021" => "2021",
            "2020" => "2020"
        );
        $model->setXLabels($labels);
        $series = array(
            "1/1" => array (
                "2021" => "5",
                "2020" => "3"
            ),
            "1/2" => array (
                "2021" => "9",
                "2020" => "7"
            )
        );
        $model->setSeries($series);
        
        $expected = [
            'xLabels' => $labels,
            'series' => $series
        ];

        $this->assertEquals("'1/1','1/2'", $model->getSeriesKeysImploded(), 'series keys imploded mismatch');
    }

    public function testGetXLabels() {
        $model = new ChartData();
        $labels = array( 
            "2021" => "2021",
            "2020" => "2020"
        );
        $model->setXLabels($labels);

        $expected = array("2021", "2020");

        $this->assertEquals($expected, $model->getXLabelsKeys(), 'keys mismatch');
    }

}
?>
