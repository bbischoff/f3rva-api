<?php 
namespace F3\Model;

class Workout implements \JsonSerializable {
	private $workoutId = null;
	private $backblastUrl = null;
	private $title = null;
	private $ao = null;
	private $q = null;
	private $pax = null;
	private $paxCount = null;
	private $workoutDate = null;

	public function getWorkoutId(): string {
		return $this->workoutId;
	}
	
	public function setWorkoutId($workoutId): void {
		$this->workoutId = $workoutId;
	}

	public function getBackblastUrl(): string {
		return $this->backblastUrl;
	}
	
	public function setBackblastUrl($backblastUrl): void {
		$this->backblastUrl= $backblastUrl;
	}
	
	public function getTitle(): string {
		return $this->title;
	}
	
	public function setTitle($title): void {
		$this->title= $title;
	}
	
	public function getAo(): array {
		return $this->ao;
	}
	
	public function setAo($ao): void {
		$this->ao = $ao;
	}
	
	public function getQ(): array {
		return $this->q;
	}
	
	public function setQ($q): void {
		$this->q = $q;
	}
	
	public function getPax(): ?array {
		return $this->pax;
	}
	
	public function setPax($pax): void {
		$this->pax = $pax;
	}
	
	public function getPaxCount(): string {
		return $this->paxCount;
	}
	
	public function setPaxCount($paxCount): void {
		$this->paxCount = $paxCount;
	}
	
	public function getWorkoutDate(): string {
		return $this->workoutDate;
	}
	
	public function setWorkoutDate($workoutDate): void {
		$this->workoutDate = $workoutDate;
	}
	
	public function jsonSerialize(): array
	{
		return [
			'workout' => [
				'id' => $this->getWorkoutId(),
				'backblastUrl' => $this->getBackblastUrl(),
				'title' => $this->getTitle(),
				'ao' => $this->getAo(),
				'q' => $this->getQ(),
				'pax' => $this->getPax(),
				'paxCount' => $this->getPaxCount(),
				'workoutDate' => $this->getWorkoutDate()
			]
		];
	}
}

?>