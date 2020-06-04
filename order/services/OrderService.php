<?php


namespace App\services;

use App\models\Order;

require_once('../../vendor/autoload.php');
$config = require('../config/config.php');

class OrderService extends BaseService
{
    private $exchange = 'order';
    private $binding_key = 'new_order';
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    protected function getCallback()
    {
        return function ($msg) {
            if (!empty($msg)) {
                echo ' [x] ', $msg->delivery_info['routing_key'], "\n";
                $data = json_decode($msg->body, true);
                $order_id = $this->createOrder($data);

                if ($order_id) {
                    echo json_encode(["order_id" => $order_id]);
                }
            }
        };
    }

    public function createOrder($data)
    {
        $order = new Order();

        $user_id = (int) $data['user_id'];
        $data = [
            $data['sandwich'],
            $data['additions']
        ];
        $address = htmlspecialchars($data['address']);

        $order->setUserId($user_id);
        $order->setData($data);
        $order->setAddress($address);

        $order_id = $order->create();

        return $order_id ?: false;
    }

    protected function createNewOrderEvent($data)
    {
        $routing_key = 'order_created';

        $rabbitMQService = new RabbitMQService($this->config);

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $rabbitMQService->sendMessage($this->exchange, $routing_key, $data);
    }
}