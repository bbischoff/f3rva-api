<?php
namespace F3\Model;

use F3\Dao\ScraperDao;
use F3\Util\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Dao\ScraperDao
 * @backupGlobals enabled
 */
class ScraperDaoTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testParsePost() {
        $httpRequestMock = $this->getMockBuilder(HttpRequest::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        /** @var \F3\Util\HttpRequest $httpRequest */
        $httpRequest = $httpRequestMock;
        
        $html =         "<!DOCTYPE html>";
        $html = $html . "<html>";
        $html = $html . "<body>";
        $html = $html . "   <article>";
        $html = $html . "       <header>";
        $html = $html . "           <h1 class='post-title'>test title</h1>";
        $html = $html . "           <span class='reviewer'><a>author</a></span>";
        $html = $html . "           <span class='cats tagcloud'><a rel='tag'>Spider Run</a></span>";
        $html = $html . "       </header>";
        $html = $html . "       <div><ul>";
        $html = $html . "           <li><strong>When:</strong>1/3/2022</li>";
        $html = $html . "           <li><strong>QIC:</strong>Splinter</li>";
        $html = $html . "           <li><strong>The PAX:</strong>Lockjaw, Bleeder, Upchuck</li>";
        $html = $html . "       </div></ul>";
        $html = $html . "   </article>";
        $html = $html . "</body>";
        $html = $html . "</html>";
    
        $httpRequestMock->method('execute')
                        ->willReturn($html);        

        $scraperDao = new ScraperDao($httpRequest);
        $result = $scraperDao->parsePost('https://testurl');

        $this->assertEquals('author', $result->author, 'author mismatch');
        $this->assertEquals(date_parse('1/3/2022'), $result->date, 'date mismatch');
        $this->assertEquals(['Lockjaw', 'Bleeder', 'Upchuck'], $result->pax, 'pax mismatch');
        $this->assertEquals(['Splinter'], $result->q, 'q mismatch');
        $this->assertEquals(['Spider Run'], $result->tags, 'tags mismatch');
        $this->assertEquals('test title', $result->title, 'title mismatch');
    }
}
?>
