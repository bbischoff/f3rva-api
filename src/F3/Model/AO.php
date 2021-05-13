<?php
namespace Src\F3\Model;

class AO implements \JsonSerializable {
	private $id = null;
	private $description = null;
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}

	public function jsonSerialize()
	{
		return [
			'ao' => [
				'id' => $this->getId(),
				'description' => $this->getDescription()
			]
		];
	}
}

?>