<?php

namespace App\services;

require_once('../../vendor/autoload.php');
$config = require('../config/config.php');

use App\models\User;

class UserService extends BaseService
{
    private $exchange = 'order';
    private $binding_key = 'new_user';
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
                $user_id = $this->createUser($data);

                if ($user_id) {
                    $data['user_id'] = $user_id;
                    $this->createNewOrderEvent($data);
                }
            }
        };
    }

    public function createUser($data)
    {
        $user = new User();

        $name = htmlspecialchars($data['name']);
        $phone = preg_match('/^\+7|8\d{10}$/', $data['phone'])
            ? $data['phone'] : '';

        $user->setName($name);
        $user->setPhone($phone);

        $user_id = $user->create();

        return $user_id ?: false;
    }

    protected function createNewOrderEvent($data)
    {
        $routing_key = 'new_order';

        $rabbitMQService = new RabbitMQService($this->config);

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $rabbitMQService->sendMessage($this->exchange, $routing_key, $data);
    }
}

$userService = new UserService($config);
$userService->run();