<?php
include_once "servers.php";
include_once "frontsend.php";

require_once __DIR__ . '/../vendor/autoload.php';

// check passwords
// get info from post
//$username = $_POST['username'];
//$password = $_POST['password'];

//////////////////////////////////////////////////
//require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare('front-send', false, true, false, false);

$end = 2;
for ($i = 0; $i < $end; $i++) {

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
//////////////////////////////////////////////////

// login as given user
$result = run_query(Prefix::LOGIN);
list(, $is_success) = explode(" ", $result, 2);
$is_success = $result === "true" ? true : false;

if ($is_success) {
    // redirect to homepage
    session_start();
    $_SESSION['logged_user'] = $username;
    header("refresh:0; url=frontend.html");
} else {
    // echo out a fail message
    echo "<h1>Error: incorrect username or password!</h1>";
    header("refresh:2; url=login.html");
}
