<?php
namespace F3\Repo;

use DI\Attribute\Inject;
use PDO;

/**
 * @author bbischoff
 */
class SQLiteDatabase implements Database
{
    private $_db;

    public function __construct(#[Inject("db.local.path")] $localDbPath) {
        $dsn = "sqlite:" . $localDbPath;
    
        $this->_db = new PDO($dsn);
    }

    public function getDatabase() {
        return $this->_db;
    }
}

?>