<?php
include("servers.php");
include_once "rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$publish = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$consume = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$publish_channel = $publish->channel();
$consume_channel = $consume->channel();
$consumeData_channel = $consume->channel();
$publishReturn_channel = $publish->channel();
$consumeReturn_channel = $consume->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$publish_channel->queue_declare('front-send', false, true, false, false);
//$consume_channel->queue_declare('back-data', 'Test', true, false, false);
$consume_channel->queue_declare('back-data', false, true, false, false);
//$consumeData_channel->queue_declare('data-back', 'Data', true, false, false);
$consumeData_channel->queue_declare('data-back', false, true, false, false);
$publishReturn_channel->queue_declare('front-receive', false, true, false, false);
$consumeReturn_channel->queue_declare('data-return-back', false, true, false, false);

/*
if (isset($argv[1])) {
    $msg = new AMQPMessage($argv[1]);
    $msg->setPriority(2);
    $msg2 = new AMQPMessage($argv[1]);
    //$publish_channel->basic_publish($msg, '', FRONT_BACK);
    $consume_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '{$msg->getBody()}'\n";
}
*/
while (true) {
    echo " [*] Waiting for messages. To exit press CTRL+C\n";
    /*
$callback2 = function (AMQPMessage $msg2) {
    global $consume_channel;
    echo ' [x] Received ', $msg2->body, "\n";
    
};

$consume_channel->basic_consume('front-send', '', false, true, false, false, $callback2);

 $m3 = readline("Message to database: ");
    $msg3 = new AMQPMessage($m3);
    $consumeData_channel->basic_publish($msg3, '', 'data-back');
    echo "Sent '$m3'\n";
*/
    $callback = function (AMQPMessage $msg) {
        global $publish_channel;
        global $consume_channel;
        global $consumeData_channel;
        global $consumeReturn_channel;
        global $publishReturn_channel;

        echo ' [x] Received ', $msg->body, "\n";


        //$consume_channel->basic_publish($msg, '', 'back-data');
        $consume_channel->basic_publish($msg, '', 'back-data');


        //$m3 = readline("Message to database: ");
        $m3 = $msg->body . "\n1 hello from the back";
        $msg3 = new AMQPMessage($m3);
        $consumeData_channel->basic_publish($msg3, '', 'data-back');
        echo "Sent '$m3'\n";


        ///RECIEVEING MSGS FROM DATA//////
        echo " [*] Waiting to receive data. To exit press CTRL+C\n";

        $callback2 = function (AMQPMessage $msg4) {
            global $publish_channel;
            global $consume_channel;
            global $consumeReturn_channel;
            global $publishReturn_channel;

            echo ' [x] Received ', $msg4->body, "\n";


            $consumeReturn_channel->basic_publish($msg4, '', 'back-return-front');


            //$m4 = readline("Message to front: ");
            $m4 = $msg4->body . "\n3 hello again from the back";
            $msg4 = new AMQPMessage($m4);
            $publishReturn_channel->basic_publish($msg4, '', 'front-receive');
            echo "Sent '$m4'\n";
        };

        $consumeReturn_channel->basic_consume('data-return-back', '', false, true, false, false, $callback2);

        while ($consumeReturn_channel->is_open()) {
            $consumeReturn_channel->wait();
        }
        ///-----------------------//////

    };

    // basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
    $consume_channel->basic_qos(null, 2, null);
    $consume_channel->basic_consume('front-send', '', false, true, false, false, $callback);


    while ($consume_channel->is_open()) {
        $consume_channel->wait();
    }
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();