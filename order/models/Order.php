<?php


namespace App\models;

require_once (__DIR__ . '/../../vendor/autoload.php');

use App\services\DB;

class Order
{
    protected $table = 'orders';

    public $id;
    public $user_id;
    public $data;
    public $address;
    public $is_delivered;

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

    public function setIsDelivered($is_delivered = false)
    {
        $this->is_delivered = (int) $is_delivered;
    }

    public function create()
    {
        $sql = "INSERT INTO {$this->table} (user_id, data, address) VALUES (:userId, :data, :address)";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':userId', $this->user_id, \PDO::PARAM_INT);
        $sth->bindParam(':data', $this->data, \PDO::PARAM_STR);
        $sth->bindParam(':address', $this->address, \PDO::PARAM_STR);
        $sth->execute();

        $this->id = DB::getInstance()->getLastInsertId();
        return $this->id;
    }

    public function updateIsDelivered($id, $is_delivered)
    {
        $sql = "UPDATE {$this->table} SET is_delivered = :is_delivered WHERE id = :id";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':is_delivered', $is_delivered, \PDO::PARAM_INT);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);

        return $sth->execute();
    }

    public function selectByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id=:user_id";

        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function selectById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id=:id";

        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}