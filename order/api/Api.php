<?php

namespace App\api;

require_once('../../vendor/autoload.php');
$config = require('../config/config.php');

use App\models\User;
use App\services\RabbitMQService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Message\AMQPMessage;

class Api
{
    private $request;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function exec($request)
    {
        $this->request = $request;
        $method = $this->getMethod();

        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function getMethod()
    {
        $event = $this->request['event'];

        if ($event) {
            $keys = array_map(function ($key) {
                return ucfirst($key);
            }, explode('_', $event));

            return "create" . implode("", $keys) . "Event";
        }

        return null;
    }

    protected function createNewOrderEvent()
    {
        $exchange = 'order';
        $routing_key = 'new_user';

        $rabbitMQService = new RabbitMQService($this->config);

        $data = json_encode($this->request, JSON_UNESCAPED_UNICODE);

        $rabbitMQService->sendMessage($exchange, $routing_key, $data);


//        $user_id = $this->createUser();
//
//        if ($user_id) {
//            $order = new Order();
//            $order->setUserId($user_id);
//            $data = [
//                $this->request['sandwich'],
//                $this->request['additions'],
//            ];
//            $order->setData($data);
//            $order->setAddress($this->request['address']);
//
//            return $order->create();
//        }
//
//        return false;
    }
}

$api = new Api($config);
$api->exec($_POST);
