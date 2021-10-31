<?php
include("servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare('FRONT_BACK', false, false, false, false);

$end = 1;
for ($i = 0; $i < $end; $i++) {
    $m = readline("Message $i: ");
    $msg = new AMQPMessage("$i $m");
    $channel->basic_publish($msg, '', 'FRONT_BACK');

    echo "Sent '$m'\n";
}

$channel->close();
$connection->close();
