<?php
namespace F3\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Model\Response
 * @backupGlobals enabled
 */
class ResponseTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testJsonSerialize() {
        $model = new Response();
        $model->setCode(Response::SUCCESS);
        $model->setId(1);
        $model->setMessage('message');
        $model->setResults('results');
        
        $expected = [ 'response' => [
            'id' => 1,
            'code' => Response::SUCCESS,
            'message' => 'message',
            'results' => 'results'
        ]];

        $this->assertEquals($expected, $model->jsonSerialize(), 'json mismatch');
    }
}
?>
