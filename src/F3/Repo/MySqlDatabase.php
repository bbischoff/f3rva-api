<?php
namespace F3\Repo;

use F3\Settings;
use PDO;

/**
 * @author bbischoff
 */
class MySqlDatabase implements Database
{
    private $_db;

    public function __construct() {
        $host = Settings::DB_HOST;
        $db   = Settings::DB_NAME;
        $user = Settings::DB_USER;
        $pass = Settings::DB_PASS; 
        $charset = Settings::DB_CHARSET;

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    
        $this->_db = new PDO($dsn, $user, $pass, $opt);
    }

    public function getDatabase() {
        return $this->_db;
    }
}

?>