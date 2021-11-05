<?php
include_once "servers.php";
include_once "rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$front_publish_connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$front_consume_connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$data_publish_connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
$data_consume_connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);

$front_publish_channel = $front_publish_connection->channel();
$front_consume_channel = $front_consume_connection->channel();
$data_publish_channel = $data_publish_connection->channel();
$data_consume_channel = $data_consume_connection->channel();


// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
// $channel->queue_declare(BACK_FRONT, false, true, false, false);
// $channel->queue_declare(BACK_DATA, false, true, false, false);

$front_publish_channel->queue_declare('front-send', false, true, false, false);
$data_publish_channel->queue_declare('back-data', false, true, false, false);
//$data_publish_channel->queue_declare(FRONT_BACK, false, true, false, false);

// $channel->basic_consume(FRONT_BACK, '', false, true, false, false, $handle_messages_from_front);

print("[BACK] waiting for messages...");

$handle_messages_from_front = function (AMQPMessage $request) {
    print("[BACK] received message from front-end");

    $channel = $request->getChannel();
    // for sending queries to the database
    //$database_client = new Client($channel->getConnection(), BACK_DATA);
    $database_client = new Client($channel->getConnection(), back-data);

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
    global $data_publish_channel, $data_consume_channel;
    print("[BACK] received query from front-end\n");

    $body = $msg->getBody();
    print("[BACK] message = \"$body\"\n");
    print("[BACK] appending BACK to message and sending...\n");
    $body .= "\nBACK receieved";
    $response = new AMQPMessage($body);
    
    echo ' [x] Received ', $->body, "\n";
    $data_publish_channel->basic_publish($msg "", 'back-data');
    /*
    print("[BACK] sending message to DATA\n");
    // $database_client = new Client($msg->getConnection(), BACK_DATA);
    $data_publish_channel->basic_publish($response, "", BACK_DATA);
    print("[BACK] sent message to DATA\n");
    */

    $test_handle_data = function (AMQPMessage $msg) {
        global $front_publish_channel;
        // $result = $database_client->send_query($body, $msg->getExchange());
        print("[BACK] received message from DATA\n");

        $body = $msg->getBody();
        print("[BACK] message = \"$body\"\n");
        print("[BACK] appending BACK to message and sending...\n");
        $body .= "\nBACK sent";

        $response = new AMQPMessage($body);
        //$front_publish_channel->basic_publish($response, "", 'front-send');
        $data_publish_channel->basic_publish($response, "", 'back-data');

        print("[BACK] sent to FRONT\n");
    };
    global $front_publish_channel;
    $body = $msg->getBody();
    print("[BACK] message = \"$body\"\n");
    print("[BACK] appending BACK to message and sending...\n");
    $body .= "\nBACK sent";

    $response = new AMQPMessage($body);
    //$front_publish_channel->basic_publish($response, "", 'front-send');
    $data_publish_channel->basic_publish($response, "", 'back-data');

    print("[BACK] sent to FRONT\n");

    /*
    $data_consume_channel->basic_consume(BACK_DATA, "", $test_handle_data);
    while ($data_consume_channel->is_open()) {
        $data_consume_channel->wait();
    }
    $data_consume_channel->close();
    $data_consume_connection->close();
    */
};

// for sending responses back to the frontend
$front_consume_channel->basic_consume('front-send', "", $test_handle_front);

while ($front_consume_channel->is_open()) {
    $front_consume_channel->wait();
}

$front_publish_connection->close();
$front_consume_connection->close();
$data_publish_connection->close();

$front_publish_channel->close();
$front_consume_channel->close();
$data_publish_channel->close();

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
