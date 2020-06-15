<?php


namespace App\services;

use App\models\Billing;

require_once (__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../config/config.php');

class BillingService extends BaseService
{
    protected $exchange = 'order';
    protected $binding_key = 'order_created';
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
                $billing_id = $this->createBilling($data);

                if ($billing_id) {
                // paying
                    // then
                    $this->createOrderPaidEvent($data);
                }
            }
        };
    }

    public function createBilling($data)
    {
        $billing = new Billing();

        $order_id = (int) $data['order_id'];

        $billing->setOrderId($order_id);
        $billing->setIsPaid(true);

        $billing_id = $billing->create();

        return $billing_id ?: false;
    }

    protected function createOrderPaidEvent($data)
    {
        $routing_key = 'order_paid';

        $rabbitMQService = new RabbitMQService($this->config);

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $rabbitMQService->sendMessage($this->exchange, $routing_key, $data);
    }
}

$billingService = new BillingService($config);
$billingService->run();