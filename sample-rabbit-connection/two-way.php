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
$publish_channel->queue_declare(FRONT_BACK, false, true, false, false);

if (isset($argv[1])) {
    $msg = new AMQPMessage($argv[1]);
    $publish_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '{$msg->getBody()}'\n";
}

echo " [*] Waiting for messages. To exit press CTRL+C\n";
$msgnum == 0;

$callback = function (AMQPMessage $msg) {
    global $publish_channel;

    echo ' [x] Received ', $msg->body, "\n";
    $m = readline("Message: ");
    $msg = new AMQPMessage($m);
    $msgnum = 1;
    $publish_channel->basic_publish($msg, '', FRONT_BACK);
    echo "Sent '$m'\n";
};

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
$consume_channel->basic_consume(FRONT_BACK, '', false, true, false, true, $callback);

while ($consume_channel->is_open()) {
    $consume_channel->wait();
    
    if (msgnum == 1) {
        $consume_channel->close();
    }
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();
