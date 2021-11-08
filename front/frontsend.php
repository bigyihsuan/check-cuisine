<?php
include("servers.php");
include_once "rabbit_endpoints.php";
include "login.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare('front-send', false, true, false, false);
/*
$end = 1;
for ($i = 0; $i < $end; $i++) {
    $m = readline("Message $i: ");
    $msg = new AMQPMessage("$i $m");
    $channel->basic_publish($msg, '', 'front-send');

    echo "Sent '$m'\n";
}
*/
$end = 2;
for ($i = 0; $i < $end; $i++) {
    /*
    if ($i = 1) {
        $m = readline("Username: ");
        $msg1 = new AMQPMessage("$m");
    }
    
    if ($i = 2) {
        $m = readline("Password: ");
        $msg2 = new AMQPMessage("$m");
    }
  
    $channel->basic_publish($msg1, '', 'front-send');
     $channel->basic_publish($msg2, '', 'front-send');

    echo "Sent login info to backend \n";
    */

    if ($i = 1) {
        //$username = readline("Username: ");
        //$password = readline("Password: ");
        
        $username = $_POST['username'];
        $password = $_POST['password'];

        $userpass = array($username => $password);
        $userpass_json = json_encode($userpass);

        $msg1 = new AMQPMessage($userpass_json);
        $channel->basic_publish($msg1, '', 'front-send');

        // $msg2 = new AMQPMessage($m);
        // $channel->basic_publish($msg2, '', 'front-send');

        $channel->queue_declare('front-receive', false, true, false, false);

        echo "Sent login info to backend \n\n";

        echo " [*] Receiving data. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };

        // basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
        $channel->basic_consume('front-receive', '', false, true, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }
    }

    //echo "Sent login info to backend \n";
}

$channel->close();
$connection->close();
