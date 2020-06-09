<?php


namespace App\services;

use PDO;

class DB
{
    private static $instance = null;
    private $server;

    private function __construct()
    {
        $config = require(__DIR__ . '/../config/db.php');
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']}";
        $this->server = new PDO($dsn, $config['user'], $config['password']);
    }

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getStatement($sql)
    {
        return $this->server->prepare($sql);
    }

    public function getLastInsertId()
    {
        return $this->server->lastInsertId();
    }
}