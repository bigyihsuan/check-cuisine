<?php
include_once "servers.php";
include_once "rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$hostname = "localhost";
$username = "test";
$password = "test";
$database = "test";

$publish_connection = new AMQPStreamConnection(rabbit_server, 5672, data_server_creds[0], data_server_creds[1]);
$consume_connection = new AMQPStreamConnection(rabbit_server, 5672, data_server_creds[0], data_server_creds[1]);

// $db = new mysqli($hostname, $username, $password, $database);
// if ($db->connect_errno) {
//     exit();
// }

$publish_channel = $publish_connection->channel();
$publish_channel = $consume_connection->channel();

$publish_channel->queue_declare(BACK_DATA, false, true, false, false);

print("[DATA] waiting for messages...");

$handle_back = function (AMQPMessage $request) {
    global $db;
    print("[DATA] received query from back");
    // separate the prefix from the query
    list($prefix, $query) = explode(" ", $request->body, 2);
    print("[DATA] $query");
    // execute the query
    ($result = $db->query($query)) or die();
    // get the rows
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    // convert to json for transmision
    $json = json_encode($rows);
    // create message
    $message = new AMQPMessage("$prefix $json", array("correlation_id" => $request->get("correlation_id")));
    // send message
    print("[DATA] sending results to back...");
    $request->getChannel()->basic_publish($message, '', $request->get("reply_to"));
    $request->ack();
    print("[DATA] sent results to back");
};

$test_handle_back = function (AMQPMessage $msg) {
    global $consume_channel;
    print("[DATA] received query from back-end");

    $body = $msg->getBody();
    print("[DATA] message = \"$body\"");
    print("[DATA] appending DATA to message and sending...");
    $body .= "\nDATA receieved\nDATA sent";

    $response = new AMQPMessage($body);
    $consume_channel->basic_publish($response, "", BACK_DATA);
    print("[DATA] sent to BACK");
};

$publish_channel->basic_consume(BACK_DATA, "", $test_handle_back);

while ($publish_channel->is_open()) {
    $publish_channel->wait();
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();