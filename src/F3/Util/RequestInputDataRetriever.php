<?php
namespace F3\Util;

/**
 * Data Retriever implementation for pulling from posted input data.
 *
 * @author bbischoff
 */
class RequestInputDataRetriever implements DataRetriever {
	
	public function retrieve() {
        return file_get_contents('php://input');
    }
}

?>