<?php

namespace database;

/**
 * MySQLConnection 
 * 
 * @property \PDO $connection Connection to MySQL Database
 */
class MySQLConnection implements DBConnection
{
    private $connection;
    
    /**
     * __construct
     *
     * @param  string $host     Database Host with port
     * @param  string $dbName   Database name
     * @param  string $username Username for the database
     * @param  string $password Password for the database
     */
    public function __construct(string $host, string $dbName, string $username, string $password)
    {
        $this->connection = new \PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        $this->connection->exec("SET NAMES 'utf8';");
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
