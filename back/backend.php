<?php
include("../servers.php");

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rabbit_server, 5672, $back_server_creds[0], $back_server_creds[1]);
$channel = $connection->channel();

// queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
$channel->queue_declare(FRONT_BACK, true, false, false, false);
$channel->queue_declare(BACK_FRONT, true, false, false, false);
$channel->queue_declare(BACK_DATA, true, false, false, false);
$channel->queue_declare(DATA_BACK, true, false, false, false);

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

// registration
$create_user_start = function ($msg) {
    // extract username and password
    list($username, $hashed_password) = explode(" | ", $msg->body);

    // create query: add a user
    $query = "INSERT INTO User (username, pass) VALUES ($username, $hashed_password);";
    $query_msg = new AMQPMessage($query);

    // send query to database
    // basic_publish(AMQmessage, exchange, queue name)
    $msg->getChannel()->basic_publish($query_msg, 'registration', BACK_DATA);
};

$create_user_end = function ($msg) {
    // extract username and password from response
    $response = new AMQPMessage("success");
    if (str_contains($msg->body, "error")) {
        $response = $msg;
    }
    $msg->getChannel()->basic_publish($response, 'registration', BACK_FRONT);
};

// log in
$authenticate_user_get = function ($msg) {
    // extract username and password
    list($username, $hashed_password) = explode(" | ", $msg->body);

    // create query: get a user
    $query = "SELECT * FROM User WHERE username=$username;";
    $query_msg = new AMQPMessage($query);

    // send query to database
    $msg->getChannel()->basic_publish($query_msg, 'login', BACK_DATA);
};

$authenticate_user_process = function ($msg) {
    // extract username and password from response
    $response = new AMQPMessage("success");
    if (str_contains($msg->body, "error")) {
        $response = $msg;
    }
    $msg->getChannel()->basic_publish($response, 'login', BACK_FRONT);
};

$channel->basic_consume(FRONT_BACK, 'registration', false, true, false, false, $create_user_start);
$channel->basic_consume(BACK_FRONT, 'registration', false, true, false, false, $create_user_end);
$channel->basic_consume(BACK_DATA, 'login', false, true, false, false, $authenticate_user_get);
$channel->basic_consume(DATA_BACK, 'login', false, true, false, false, $authenticate_user_process);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();