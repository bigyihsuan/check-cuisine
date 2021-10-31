<?php
include("servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();

$channel->queue_declare('FRONT_BACK', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
   
};

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
$channel->basic_consume('FRONT_BACK', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
