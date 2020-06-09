<?php


namespace App\models;

require_once (__DIR__ . '/../../vendor/autoload.php');

use App\services\DB;

class Billing
{
    protected $table = 'billings';

    public $id;
    public $order_id;
    public $is_paid;

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function setIsPaid($is_paid = false)
    {
        $this->is_paid = (int) $is_paid;
    }

    public function create()
    {
        $sql = "INSERT INTO {$this->table} (order_id, is_paid) VALUES (:orderId, :isPaid)";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':orderId', $this->order_id, \PDO::PARAM_INT);
        $sth->bindParam(':isPaid', $this->is_paid, \PDO::PARAM_INT);
        $sth->execute();

        $this->id = DB::getInstance()->getLastInsertId();
        return $this->id;
    }

    public function selectByOrderId($order_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id=:orderId";

        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':orderId', $order_id, \PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}