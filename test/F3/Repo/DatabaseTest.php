<?php
namespace F3\Repo;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Repo\Database
 * @backupGlobals enabled
 */
class DatabaseTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testGetDatabase() {
        $db = new Database();
        
        $this->assertTrue($db->getDatabase() instanceof PDO, 'pdo instance check');
    }

    public function testGetInstance() {
        $instance1 = Database::getInstance();
        $instance2 = Database::getInstance();
        
        $this->assertEquals($instance1, $instance2, 'singleton check');
    }
}
?>
