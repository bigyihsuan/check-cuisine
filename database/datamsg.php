<?php
include("servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$publish = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$consume = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$publish_channel = $publish->channel();
$consume_channel = $consume->channel();
$consumeFront_channel = $consume->channel();
$publishFront_channel = $publish->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$publish_channel->queue_declare('back-data', false, true, false, false);
$consume_channel->queue_declare('data-back', false, true, false, false);
$consumeFront_channel->queue_declare('data-front', false, true, false, false);
$publishFront_channel->queue_declare('data-front', false, true, false, false);


if (isset($argv[1])) {
    $msg = new AMQPMessage($argv[1]);
    //$publish_channel->basic_publish($msg, '', FRONT_BACK);
    $consume_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '{$msg->getBody()}'\n";
}

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
    global $publish_channel;
    global $consume_channel;
    global $consumeData_channel;
    
    echo ' [x] Received ', $msg->body, "\n";
   
    
    $consume_channel->basic_publish($msg, '', 'data-front');
    
    $m2 = readline("Message: ");
    $msg2 = new AMQPMessage($m2);
    $publish_channel->basic_publish($msg2, '', 'back-data');
    echo "Sent '$m2'\n";

    //echo "Sent '$msg'\n";

};

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
$consume_channel->basic_consume('data-back', '', false, true, false, false, $callback);

while ($consume_channel->is_open()) {
    $consume_channel->wait();
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();
