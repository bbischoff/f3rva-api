<?php
namespace F3\Model;

class Version implements \JsonSerializable {
	private $version = null;
	
	public function getVersion() {
		return $this->version;
	}
	
	public function setVersion($version) {
		$this->version = $version;
	}

	public function jsonSerialize()
	{
		return [
			'version' => $this->getVersion()
		];
	}
}

?>