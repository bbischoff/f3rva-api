<?php
namespace F3\Util;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNull;

/**
 * @covers \F3\Util\RequestInputDataRetriever
 */
class RequestInputDataRetrieverTest extends TestCase {
    protected function setup() : void {

    }

    public function testRetrieve() {
        $requestInputRetriever = new RequestInputDataRetriever();
        $this->assertEmpty($requestInputRetriever->retrieve(), 'expected to be empty');
    }
}