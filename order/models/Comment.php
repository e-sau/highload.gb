<?php


namespace App\models;

require_once (__DIR__ . '/../../vendor/autoload.php');

use App\services\DB;

class Comment
{
    protected $table = 'comments';

    public $id;
    public $user_id;
    public $order_id;
    public $message;

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function setMessage($message)
    {
        $this->message = htmlspecialchars($message);
    }

    public function create()
    {
        $sql = "INSERT INTO {$this->table} (user_id, order_id, message) VALUES (:userId, :orderId, :message)";
        $sth = DB::getInstance()->getStatement($sql);
        $sth->bindParam(':userId', $this->user_id, \PDO::PARAM_INT);
        $sth->bindParam(':orderId', $this->order_id, \PDO::PARAM_INT);
        $sth->bindParam(':message', $this->message, \PDO::PARAM_STR);
        $sth->execute();

        $this->id = DB::getInstance()->getLastInsertId();
        return $this->id;
    }
}