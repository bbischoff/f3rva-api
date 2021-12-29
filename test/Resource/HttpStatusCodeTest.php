<?php

use PHPUnit\Framework\TestCase;
use F3\Resource\HttpStatusCode;

/**
 * @covers \F3\Resource\HttpStatusCode
 * @backupGlobals enabled
 */
class HttpStatusCodeTest extends TestCase {

    public function testHttpHeaderFor200OK() {
        $this->assertEquals('HTTP/1.1 200 OK', HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), '200 OK');
    }

    public function testGetMessageFor200OK() {
        $this->assertEquals('200 OK', HttpStatusCode::getMessageForCode(HttpStatusCode::HTTP_OK), '200 OK');
    }

    public function testIsError() {
        $this->assertFalse(HttpStatusCode::isError(HttpStatusCode::HTTP_OK), '200 OK');
        $this->assertTrue(HttpStatusCode::isError(HttpStatusCode::HTTP_BAD_REQUEST), '400 BAD REQUEST');
    }
}
?>
