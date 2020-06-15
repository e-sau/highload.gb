<?php

namespace App\api;

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once (__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../config/config.php');

use App\models\Order;
use App\services\RabbitMQService;

class Api
{
    private $exchange = 'order';
    private $request;
    private $config;
    private $params;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function exec($request)
    {
        $this->request = $request;
        $method = $this->getMethod();

        if (method_exists($this, $method)) {
            $this->$method($this->params);
        }
    }

    protected function getMethod()
    {
        if (!empty($_POST['data'])) {
            $post = json_decode($_POST['data'], true);

            if (!empty($post['method'])) {
                $this->params = $post['orderId'];
                return $post['method'];
            }
        }

        return $this->getEvent();
    }

    protected function getEvent()
    {
        if (!empty($this->request['event'])) {
            $event = $this->request['event'];
            $keys = array_map(function ($key) {
                return ucfirst($key);
            }, explode('_', $event));

            return "create" . implode("", $keys) . "Event";
        }

        return null;
    }

    protected function createNewOrderEvent()
    {
        $this->sendRabbitMQMessage('new_user');
        $this->sendNewOrderMessageToUser();
    }

    protected function createNewCommentEvent()
    {
        $this->sendRabbitMQMessage('new_comment');
        $this->sendNewCommentMessageToUser();
    }

    protected function sendRabbitMQMessage($routing_key)
    {
        $rabbitMQService = new RabbitMQService($this->config);

        $data = json_encode($this->request, JSON_UNESCAPED_UNICODE);

        $rabbitMQService->sendMessage($this->exchange, $routing_key, $data);
    }

    protected function isOrderDelivered($order_id)
    {
        $order = (new Order())->selectById($order_id);

        echo json_encode(["is_delivered" => !empty($order['is_delivered'])]);

    }

    protected function sendNewOrderMessageToUser()
    {
        echo json_encode(['OK' => 'Спасибо за заказ!'], JSON_UNESCAPED_UNICODE);
    }

    protected function sendNewCommentMessageToUser()
    {
        echo json_encode(['OK' => 'Спасибо за отзыв!'], JSON_UNESCAPED_UNICODE);
    }
}

$api = new Api($config);
$api->exec($_POST);
