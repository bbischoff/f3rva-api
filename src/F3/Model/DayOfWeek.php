<?php
namespace F3\Model;

class DayOfWeek implements \JsonSerializable {
	private $count = null;
	private $dayId = null;
	
	public function getCount() {
		return $this->count;
	}
	
	public function setCount($count) {
		$this->count = $count;
	}
	
	public function getDayId() {
		return $this->dayId;
	}
	
	public function setDayId($dayId) {
		$this->dayId = $dayId;
	}
	
	public function getDayText() {
		$dayText = "Unknown";
		
		switch ($this->dayId) {
			case 1:
				$dayText = "Sunday";
				break;
			case 2:
				$dayText = "Monday";
				break;
			case 3:
				$dayText = "Tuesday";
				break;
			case 4:
				$dayText = "Wednesday";
				break;
			case 5:
				$dayText = "Thursday";
				break;
			case 6:
				$dayText = "Friday";
				break;
			case 7:
				$dayText = "Saturday";
				break;
			default:
				$dayText = "Unknown";
				break;
		}
		
		return $dayText;
	}

	public function jsonSerialize()
	{
		return [
			'dayOfWeek' => [
				'id' => $this->getDayId(),
				'description' => $this->getDayText(),
				'count' => $this->getCount()
			]
		];
	}
}

?>