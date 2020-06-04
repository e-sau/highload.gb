<?php


namespace App\models;

require_once ('../../vendor/autoload.php');

use App\services\DB;

class Order
{
    protected $table = 'orders';

    public $id;
    public $user_id;
    public $data;
    public $address;

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setData($data)
    {
        $this->data = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function create()
    {
        $sql = "INSERT INTO {$this->table} (user_id, data, address) VALUES (:userId, :data, :address)";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->execute([
            ":userId" => $this->user_id,
            ":data" => $this->data,
            ":address" => $this->address,
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