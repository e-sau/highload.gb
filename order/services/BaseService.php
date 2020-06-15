<?php


namespace App\services;

require_once (__DIR__ . '/../../vendor/autoload.php');


abstract class BaseService
{
    abstract protected function getCallback();

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $rabbitMQService = new RabbitMQService($this->config);

        $rabbitMQService->declareExchangeTopic($this->exchange);
        $channel = $rabbitMQService->getChannel();

        list($queue_name, ,) = $channel->queue_declare(
            "",
            false,
            true,
            true,
            false
        );

        $channel->queue_bind($queue_name, $this->exchange, $this->binding_key);

        $callback = $this->getCallback();

        $channel->basic_consume(
            $queue_name,
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $rabbitMQService->close();
    }
}