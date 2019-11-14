<?php

namespace App\Core;

use PDO;

class Db
{
    /**
     * @var HOST
     */
    const HOST = "127.0.0.1";

    /**
     * @var PORT
     */
    const PORT = 3306;
    
    /**
     * @var USER
     */
    const USER = 'root';
    
    /**
     * @var PASSWORD
     */
    const PASSWORD = 'root';
    
    /**
     * @var DATABASE
     */
    const DATABASE = 'app';

    /**
     * @var Conn
     */
    public $conn;

    /**
     * Db constructor.
     */
    public function __construct()
    {
        $this->conn = new PDO('mysql:host='.self::HOST.';dbname='.self::DATABASE, self::USER, self::PASSWORD);
    }
}
