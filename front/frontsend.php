<?php
include("servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare('FRONT_BACK', false, false, false, false);


body = readline("Enter message content: ");
print("[FRONT] sending message to BACK...\n");
print("[FRONT] message = \"$body\"\n");
$message = new AMQPMessage($body);
$publish_channel->basic_publish($message, "", FRONT_BACK);

$handle_back_to_front = function (AMQPMessage $message) {
    print("[FRONT] received message from BACK!\n");
    $body = $message->getBody();
    print("[FRONT] appending FRONT to message and printing...\n");
    $body .= "\nFRONT receieved";

    print("[FRONT] message = \"$body\"\n");
    print("[FRONT] finished\n");
};
/*
for ($i = 0; $i < $end; $i++) {
    $m = readline("Message $i: ");
    $msg = new AMQPMessage("$i $m");
    $channel->basic_publish($msg, '', 'FRONT_BACK');

    echo " [$i/$end] Sent '$m'\n";
}
*/
$channel->close();
$connection->close();
