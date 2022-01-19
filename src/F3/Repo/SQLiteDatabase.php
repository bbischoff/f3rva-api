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

    public function __construct() {
        $dsn = "sqlite:" . Settings::DB_LOCAL_PATH;
    
        $this->_db = new PDO($dsn);
    }

    public function getDatabase() {
        return $this->_db;
    }
}

?>