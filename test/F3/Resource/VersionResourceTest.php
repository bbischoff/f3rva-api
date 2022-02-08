<?php
namespace F3\Resource;

use F3\Model\Version;
use F3\Settings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Resource\VersionResource
 * @backupGlobals enabled
 */
class VersionResourceTest extends TestCase {

    protected function setUp(): void
    {
    }

    public function testProcessRequest() {
        $expected = new Version();
        $expected->setVersion(Settings::VERSION);

        $versionResource = new VersionResource();
        $result = $versionResource->processRequest(RequestMethod::GET);

        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_OK), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertEquals(json_encode($expected), $result[WorkoutResource::BODY_KEY], 'expected json result');
    }

    public function testProcessRequestDefault() {
        $versionResource = new VersionResource();
        $result = $versionResource->processRequest(RequestMethod::PUT);
        
        $this->assertEquals(HttpStatusCode::httpHeaderFor(HttpStatusCode::HTTP_METHOD_NOT_ALLOWED), $result[WorkoutResource::HEADER_KEY], 'status code mismatch');
        $this->assertNull($result[WorkoutResource::BODY_KEY], 'null body');
    }
}
?>
