<?php
include("servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$publish = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$consume = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$publish_channel = $publish->channel();
$consume_channel = $consume->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$publish_channel->queue_declare('front-send', false, true, false, false);
$consume_channel->queue_declare('BACK_DATA', false, true, false, false);

if (isset($argv[1])) {
    $msg = new AMQPMessage($argv[1]);
    //$publish_channel->basic_publish($msg, '', FRONT_BACK);
    $consume_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '{$msg->getBody()}'\n";
}

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
    global $publish_channel;

    echo ' [x] Received ', $msg->body, "\n";
    $m = readline("Message: ");
    $msg = new AMQPMessage($m);
    $publish_channel->basic_publish($msg, '', BACK_DATA);
    //$consume_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '$m'\n";
};

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
$consume_channel->basic_consume('front-send', '', false, true, false, false, $callback);

while ($consume_channel->is_open()) {
    $consume_channel->wait();
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();
