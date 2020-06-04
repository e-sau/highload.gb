<?php

namespace App\models;

require_once ('../../vendor/autoload.php');

use App\services\DB;

class User
{
    protected $table = 'users';

    public $id;
    public $name;
    public $phone;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function create()
    {
        $sql = "INSERT INTO {$this->table} (name, phone) VALUES (:name, :phone)";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->execute([
            ":name" => $this->name,
            ":phone" => $this->phone
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

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
}