<?php


namespace App\services;

require_once ('../../vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private $connection;
    private $channel;

    public function __construct($config)
    {
        try {
            $this->connection = new AMQPStreamConnection(
                $config['rabbitmq_host'],
                $config['rabbitmq_port'],
                $config['rabbitmq_user'],
                $config['rabbitmq_password']
            );

        } catch (\AMQPException $e) {
            echo $e->getMessage();
        }
    }

    public function sendMessage($exchange, $routing_key, $data)
    {
        $this->declareExchangeTopic($exchange);

        $msg = new AMQPMessage($data);

        $this->channel->basic_publish($msg, $exchange, $routing_key);
        echo ' [x] Sent ', $routing_key, ":", json_encode($data, JSON_UNESCAPED_UNICODE), "\n";
        $this->close();
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function declareExchangeTopic($exchange)
    {
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare(
            $exchange,
            'topic',
            false,
            false,
            false
        );
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}