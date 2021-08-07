<?php

namespace models;

use database\DBConnection;
use database\MySQLConnection;

/**
 * Model
 * 
 * @property DBConnection $db
 */
class Model
{
    protected $db;

    public function __construct(?DBConnection $db = null)
    {
        $this->db = $db ?? new MySQLConnection('localhost:3306', 'criptolocos', 'root', '');
    }
}
