<?php
namespace F3\Repo;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Repo\SQLiteDatabase
 * @backupGlobals enabled
 */
class SQLiteDatabaseTest extends TestCase {
    
    protected function setUp(): void
    {
    }

    public function testGetDatabase() {
        $db = new SQLiteDatabase();
        
        $this->assertTrue($db->getDatabase() instanceof PDO, 'pdo instance check');
    }
}
?>
