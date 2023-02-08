<?php
namespace F3\Model;

class Member implements \JsonSerializable {
	private $memberId = null;
	private $f3Name = null;
	private $aliases = null;
	
	public function getMemberId(): string {
		return $this->memberId;
	}
	
	public function setMemberId($memberId): void {
		$this->memberId = $memberId;
	}
	
	public function getF3Name(): string {
		return $this->f3Name;
	}
	
	public function setF3Name($f3Name): void {
		$this->f3Name = $f3Name;
	}
	
	public function getAliases(): ?array {
		return $this->aliases;
	}
	
	public function setAliases($aliases): void {
		$this->aliases = $aliases;
	}

	public function jsonSerialize(): array
	{
		return [
			'member' => [
				'id' => $this->getMemberId(),
				'f3Name' => $this->getF3Name(),
				'aliases' => $this->getAliases()
			]
		];
	}
}

?>