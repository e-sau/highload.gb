<?php

namespace App\models;

require_once (__DIR__ . '/../../vendor/autoload.php');

use App\services\DB;

class User
{
    protected $table = 'users';

    public $id;
    public $name;
    public $phone;
    public $hash;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function create()
    {
        $sql = "INSERT INTO {$this->table} (name, phone, hash) VALUES (:name, :phone, :hash)";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->execute([
            ":name" => $this->name,
            ":phone" => $this->phone,
            ":hash" => $this->hash
        ]);

        $this->id = DB::getInstance()->getLastInsertId();
        return $this->id;
    }

    public function selectById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id=:id";

        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function selectByHash($hash)
    {
        $sql = "SELECT * FROM {$this->table} WHERE hash=:hash";

        $sth = DB::getInstance()->getStatement($sql);
        $sth->execute([
            ":hash" => $hash
        ]);

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}