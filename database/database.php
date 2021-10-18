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

$connection = new AMQPStreamConnection(rabbit_server, 5672, data_server_creds[0], data_server_creds[1]);

$db = new mysqli($hostname, $username, $password, $database);
if ($db->connect_errno) {
    exit();
}

$channel->queue_declare(DATA_BACK, true, false, false, false);
$channel->queue_declare(BACK_DATA, true, false, false, false);

print("[DATA] waiting for messages...");

$handle_back = function ($request) {
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

$channel->basic_consume(BACK_DATA, false, false, false, $handle_back);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();