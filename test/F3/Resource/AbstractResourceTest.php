<?php
namespace F3\Resource;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Resource\AbstractResource
 * @backupGlobals enabled
 */
class AbstractResourceTest extends TestCase {

    public function testCreateResponse() {
        // protected function createResponse($statusCode, $body) {
        //     $response[self::HEADER_KEY] = HttpStatusCode::httpHeaderFor($statusCode);
        //     $response[self::BODY_KEY] = $body;
    
        //     return $response;
        // }
        $class = new \ReflectionClass('\F3\Resource\AbstractResource');
        $method = $class->getMethod('createResponse');
        $method->setAccessible(true);
        $mock = $this->getMockForAbstractClass(AbstractResource::class);
        $result = $method->invokeArgs($mock, array('200', 'body'));

        $this->assertEquals('HTTP/1.1 200 OK', $result[AbstractResource::HEADER_KEY], 'header mismatch');
        $this->assertEquals('body', $result[AbstractResource::BODY_KEY], 'body mismatch');
    }
}
?>
