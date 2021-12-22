<?php
namespace F3\Model;

class MemberStats implements \JsonSerializable {
	private $memberId;
	private $memberName;
	private $numWorkouts;
	private $numQs;
	private $qRatio;
	
	public function getMemberId() {
		return $this->memberId;
	}
	
	public function setMemberId($memberId) {
		$this->memberId = $memberId;
	}
	
	public function getMemberName() {
		return $this->memberName;
	}
	
	public function setMemberName($memberName) {
		$this->memberName = $memberName;
	}
	
	public function getNumWorkouts() {
		return $this->numWorkouts;
	}
	
	public function setNumWorkouts($numWorkouts) {
		$this->numWorkouts= $numWorkouts;
	}
	
	public function getNumQs() {
		return $this->numQs;
	}
	
	public function setNumQs($numQs) {
		$this->numQs = $numQs;
	}
	
	public function getQRatio() {
		return $this->qRatio;
	}
	
	public function setQRatio($qRatio) {
		$this->qRatio = $qRatio;
	}

	public function jsonSerialize()
	{
		return [
			'memberStats' => [
				'id' => $this->getMemberId(),
				'name' => $this->getMemberName(),
				'numWorkouts' => $this->getNumWorkouts(),
				'numQs' => $this->getNumQs(),
				'qRatio' => $this->getQRatio()
			]
		];
	}
}

?>