<?php 
namespace F3\Model;

class Response implements \JsonSerializable {
    const FAILURE = 0;
    const SUCCESS = 1;
	const NOT_APPLICABLE = 2;
	const NOT_FOUND = 3;
	const PARTIAL = 4;

    private $id = null;
	private $code = null;
	private $message = null;
	private $results = null;

	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function setMessage($message) {
		$this->message = $message;
	}

	public function getResults() {
		return $this->results;
	}

	public function setResults($results) {
		$this->results = $results;
	}
	
	public function jsonSerialize()
	{
		return [
			'code' => $this->getCode(),
			'id' => $this->getId(),
			'message' => $this->getMessage(),
			'results' => $this->getResults()
		];
	}
}

?>