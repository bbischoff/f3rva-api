<?php
namespace F3\Model;

class AO implements \JsonSerializable {
	private $id = null;
	private $description = null;
	
	public function getId(): string {
		return $this->id;
	}
	
	public function setId($id): void {
		$this->id = $id;
	}
	
	public function getDescription(): string {
		return $this->description;
	}
	
	public function setDescription($description): void {
		$this->description = $description;
	}

	public function jsonSerialize(): array
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