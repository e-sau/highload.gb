<?php

namespace App\services;

require_once (__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../config/config.php');

use App\models\User;

class UserService extends BaseService
{
    protected $exchange = 'order';
    protected $binding_key = 'new_user';
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
        $hash = (int) $data['hash'];

        $user->setName($name);
        $user->setPhone($phone);
        $user->setHash($hash);

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