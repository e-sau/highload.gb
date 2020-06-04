<?php


require_once 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Message\AMQPMessage;

try {
    $connection = new AMQPStreamConnection(
        'localhost',
        5672,
        'guest',
        'guest'
    );

    $channel = $connection->channel();
    $channel->exchange_declare(
        'topic_logs',
        'topic',
        false,
        false,
        false
    );

    $routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'anonymous.info';

    $data = implode(' ', array_slice($argv, 2));

    if (empty($data)) {
        $data = 'One latte, please';
    }

    $msg = new AMQPMessage($data);

    $channel->basic_publish($msg, 'topic_logs', $routing_key);

    echo ' [x] Sent ', $routing_key, ":", $data, "\n";

    $channel->close();
    $connection->close();
} catch (AMQPProtocolChannelException $e) {
    echo $e->getMessage();
} catch (AMQPException $e) {
    echo $e->getMessage();
}