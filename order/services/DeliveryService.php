<?php


namespace App\services;


use App\models\Order;

require_once (__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../config/config.php');

class DeliveryService extends BaseService
{
    protected $exchange = 'order';
    protected $binding_key = 'order_paid';
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

                if ($this->updateOrderToDelivered($data)) {
                    $this->createOrderDeliveredEvent($data);
                }
            }
        };
    }

    public function updateOrderToDelivered($data)
    {
        $order = new Order();
        sleep(10);
        if (!empty($data['order_id'])) {
            return $order->updateIsDelivered($data['order_id'], true);
        }
        return false;
    }

    protected function createOrderDeliveredEvent($data)
    {
        $routing_key = 'order_delivered';

        $rabbitMQService = new RabbitMQService($this->config);

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $rabbitMQService->sendMessage($this->exchange, $routing_key, $data);
    }
}

$deliveryService = new DeliveryService($config);
$deliveryService->run();