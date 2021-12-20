<?php
include("servers.php");
include_once "rabbit_endpoints.php";
include("db.php");


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
$consumeFront_channel->queue_declare('data-return-back', false, true, false, false);
$publishFront_channel->queue_declare('data-return-back', false, true, false, false);

/*
if (isset($argv[1])) {
    $msg = new AMQPMessage($argv[1]);
    //$publish_channel->basic_publish($msg, '', FRONT_BACK);
    $consume_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '{$msg->getBody()}'\n";
}
*/

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
    include(__DIR__ . "/db.php");
    global $publish_channel;
    global $consume_channel;
    global $consumeFront_channel;
    global $publishFront_channel;


    $db = dbCon();

    echo ' [x] Received ', $msg->body, "\n";

    ($result = $db->query("SELECT * FROM users;")) or die("Query Failed");
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $json = json_encode($rows);

    //$consumeFront_channel->basic_consume($msg, '', 'data-back');

    // $m2 = readline("Message: ");
    // $m2 = $msg->body . "\n2 hello from the data";
    $msg2 = new AMQPMessage($json);
    $publishFront_channel->basic_publish($msg2, '', 'data-return-back');
    echo "Sent '$json'\n";

    /*
    while ($publishFront_channel->is_open()) {
        $publish_channel->close();
        $connection->close();
    }
    */
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