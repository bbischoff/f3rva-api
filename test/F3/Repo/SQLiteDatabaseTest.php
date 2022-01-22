<?php
namespace F3\Repo;

use F3\Settings;
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
        $container = Settings::getDIContainer();
        $db = $container->get(SQLiteDatabase::class);
        
        $this->assertTrue($db->getDatabase() instanceof PDO, 'pdo instance check');
    }
}
?>
