<?php
namespace F3\Model;

class ChartData implements \JsonSerializable {
	private $xLabels = null;
	private $series = null;
	
	public function getXLabels() {
		return $this->xLabels;
	}
	
	public function getXLabelsKeys() {
		return array_keys($this->xLabels);
	}
	
	public function setXLabels($xLabels) {
		$this->xLabels= $xLabels;
	}
	
	public function getSeries() {
		return $this->series;
	}
	
	public function getSeriesImploded() {
		$allSeries = array();

		foreach ($this->getSeries() as $key=>$ser) {
			if (is_array($ser)) {
				array_push($allSeries, '[' . $key . ',' . implode(",", $ser) . ']');
			}
			else {
				array_push($allSeries, '[' . $key . ',' . $ser . ']');
			}
		}
		
		return implode(",", $allSeries);
	}
	
	public function getSeriesKeysImploded() {
		return "'" . implode("','", array_keys($this->getSeries())) . "'";
	}
	
	public function setSeries($series) {
		$this->series = $series;
	}

	public function jsonSerialize(): array
	{
		return [
			'chartData' => [
				'xLabels' => $this->getXLabels(),
				'series' => $this->getSeries()
			]
		];
	}
}

?>