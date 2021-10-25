<?php
include_once "servers.php";
include_once "rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$channel = $connection->channel();


// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare(FRONT_BACK, true, false, false, false);
$channel->queue_declare(BACK_FRONT, true, false, false, false);
$channel->queue_declare(BACK_DATA, true, false, false, false);
$channel->queue_declare(DATA_BACK, true, false, false, false);

// $channel->basic_consume(FRONT_BACK, '', false, true, false, false, $handle_messages_from_front);

print("[BACK] waiting for messages...");

$handle_messages_from_front = function (AMQPMessage $request) {
    print("[BACK] received message from front-end");

    $channel = $request->getChannel();
    // for sending queries to the database
    $database_client = new Client($channel->getConnection(), BACK_DATA);

    list($prefix, $body) = explode(" ", $request->body, 2);

    switch ($prefix) {
        case Prefix::LOGIN: {
                // body is username and password
                list($username, $password) = explode(" ", $body, 2);
                // create query from user and password
                $query = "SELECT * FROM Users WHERE username=$username && password=$password;";
                break;
            }
        case Prefix::REGISTER: {
                // body is username and password
                list($username, $password) = explode(" ", $body, 2);
                // create query from user and password
                $query = "INSERT INTO Users (username, password) VALUES ($username, $password);";
                break;
            }
        default: {
                break;
            }
    }
    print("[BACK] sending query $query");
    $result = $database_client->send_query($query, $prefix);
    print("[BACK] received result from database");
    // generate message, based on prefix
    list($prefix, $body) = explode(" ", $result, 2);
    $json = json_decode($body, $associative = true);

    switch ($prefix) {
        case Prefix::LOGIN:
        case Prefix::REGISTER: { // send a response code based on how it responded
                // response is the user from the requested username and password
                if (count($json) == 1) {
                    // there is exactly 1 user returned from the thing
                    $result = true;
                }
                break;
            }
        default: {
                break;
            }
    }

    print("[BACK] sending result to front...");
    $message = new AMQPMessage("$prefix $result", array("correlation_id" => $request->get("correlation_id")));
    $request->getChannel()->basic_publish($message, '', $request->get("reply_to"));
    $request->ack();
    print("[BACK] sent result to front");
};

$test_handle_front = function (AMQPMessage $msg) {
    $channel = $msg->getChannel();
    print("[BACK] received query from front-end\n");

    $body = $msg->getBody();
    print("[BACK] message = \"$body\"\n");
    print("[BACK] appending BACK to message and sending...\n");
    $body .= "\nBACK receieved";

    print("[BACK] sending message to DATA\n");
    // $database_client = new Client($msg->getConnection(), BACK_DATA);

    print("[BACK] sent message to DATA\n");
    -$test_handle_data = function (AMQPMessage $msg) {
        // $result = $database_client->send_query($body, $msg->getExchange());
        $channel = $msg->getChannel();
        print("[BACK] received message from DATA\n");

        $body = $msg->getBody();
        print("[BACK] message = \"$body\"\n");
        print("[BACK] appending BACK to message and sending...\n");
        $body .= "\nBACK sent";

        $response = new AMQPMessage($body);
        $channel->basic_publish($response, "", BACK_FRONT);

        print("[BACK] sent to FRONT\n");
    };

    $channel->basic_consume(DATA_BACK, false, true, false, $test_handle_data);

    while ($channel->is_open()) {
        $channel->wait();
    }

    $channel->close();
    $channel->getConnection()->close();
};

// for sending responses back to the frontend
$channel->basic_consume(FRONT_BACK, false, true, false, $test_handle_front);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
// $channel->basic_consume(FRONT_BACK, 'login-credentials', false, true, false, false, $callback);

// assume messaging is finished; assume data received from front-end
/*
CREATE TABLE User (
    user_id INT UNIQUE NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL, 
    PRIMARY KEY (user_id)
);
*/