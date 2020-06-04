<?php


namespace App\services;


abstract class BaseService
{
    private $config;
    private $exchange;
    private $binding_key;

    abstract protected function getCallback();

    public function run()
    {
        $rabbitMQService = new RabbitMQService($this->config);

        $rabbitMQService->declareExchangeTopic($this->exchange);
        $channel = $rabbitMQService->getChannel();

        list($queue_name, ,) = $channel->queue_declare(
            "",
            false,
            false,
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