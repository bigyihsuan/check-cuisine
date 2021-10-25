<?php
include_once "servers.php";
include_once "rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(rabbit_server, 5672, front_server_creds[0], front_server_creds[1]);
$channel = $connection->channel();

$channel->queue_declare(FRONT_BACK, true, false, false, false);
$channel->queue_declare(BACK_FRONT, true, false, false, false);

function run_query($prefix)
{
    global $connection, $username, $password;
    $backend_client = new Client($connection, FRONT_BACK);
    // create "query"
    switch ($prefix) {
        case Prefix::REGISTER: {
                $query = "$prefix $username $password";
                break;
            }
        case Prefix::LOGIN: {
                $query = "$prefix $username $password";
                break;
            }
        default: {
                break;
            }
    }

    return $backend_client->send_query($query, $prefix);
}

$body = readline("Enter message contnet: ");
print("[FRONT] sending message to BACK...\n");
print("[FRONT] message = \"$body\"\n");

// $backend_client = new Client($connection, FRONT_BACK);
// $body = $backend_client->send_query($body, "");

$message = new AMQPMessage($body);
$channel->basic_publish($message, "", FRONT_BACK);

$handle_back_to_front = function (AMQPMessage $message) {
    print("[FRONT] received message from BACK!\n");
    $body = $message->getBody();
    print("[FRONT] appending FRONT to message and printing...\n");
    $body .= "\nFRONT receieved";

    print("[FRONT] message = \"$body\"\n");
    print("[FRONT] finished\n");
};

$channel->basic_consume(BACK_FRONT, "", $handle_back_to_front);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();