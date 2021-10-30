<?php
include("servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare('hello', false, false, false, false);

if (isset($argv[1])) {
    $msg = new AMQPMessage($argv[1]);
    $channel->basic_publish($msg, '', 'hello');
    echo "Sent '{$msg->getBody()}'\n";
}

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";

    $m = readline("Message: ");
    $msg = new AMQPMessage($m);
    $msg->getChannel()->basic_publish($msg, '', 'hello');
    echo "Sent '$m'\n";
};

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();