<?php
namespace F3\Repo;

use F3\Settings;
use PDO;

/**
 * @author bbischoff
 */
class SQLiteDatabase implements Database
{
    private $_db;

    /**
     * @Inject({"localDbPath" = "db.local.path"})
     */
    public function __construct($localDbPath) {
        $dsn = "sqlite:" . $localDbPath;
    
        $this->_db = new PDO($dsn);
    }

    public function getDatabase() {
        return $this->_db;
    }
}

?>