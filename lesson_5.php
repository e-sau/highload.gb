<?php

const REDIS_SERVER = '127.0.0.1';
const REDIS_PORT = '6379';

class RedisService
{
    private $connection = null;

    private function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = new Redis();
            $this->connection->connect(REDIS_SERVER, REDIS_PORT);
        }
        return $this->connection;
    }

    public function get($key)
    {
        return unserialize($this->getConnection()->get($key));
    }

    public function set($key, $value, $time = 5)
    {
        $this->getConnection()->set($key, serialize($value), $time);
    }

    public function del($key)
    {
        $this->getConnection()->del($key);
    }

    public function clear()
    {
        $this->getConnection()->flushDB();
    }
}

Class MySqlDB
{
    private static $instance = null;
    private static $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=shop';
    private static $user = 'user';
    private static $pass = 'pass';
    private $server;

    private function __construct()
    {
        $this->server = new PDO(self::$dsn, self::$user, self::$pass);
    }

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function query($sql, $params = [])
    {
        $sth = $this->server->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}