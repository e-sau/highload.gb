<?php


namespace App\services;

use App\models\Order;

require_once (__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../config/config.php');

class OrderService extends BaseService
{
    protected $exchange = 'order';
    protected $binding_key = 'new_order';
    protected $config;

    protected function getCallback()
    {
        return function ($msg) {
            if (!empty($msg)) {
                file_put_contents(
                    'php://stdout',
                    ' [x] ' . $msg->delivery_info['routing_key'] . "\n"
                );

                $data = json_decode($msg->body, true);
                $order_id = $this->createOrder($data);

                if ($order_id) {
                    $data['order_id'] = $order_id;
                    $this->createNewOrderEvent($data);
                }
            }
        };
    }

    public function createOrder($data)
    {
        $order = new Order();

        $user_id = (int) $data['user_id'];

        $orderParams = [];
        if (!empty($data['sandwich'])) $orderParams['sandwich'] = $data['sandwich'];
        if (!empty($data['additions'])) $orderParams['additions'] = $data['additions'];

        $address = (!empty($data['address'])) ? htmlspecialchars($data['address']) : '';

        $order->setUserId($user_id);
        $order->setData($orderParams);
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

$orderService = new OrderService($config);
$orderService->run();