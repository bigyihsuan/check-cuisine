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
print("[FRONT] sending message to BACK...");
print("[FRONT] message = \"$body\"");
$backend_client = new Client($connection, FRONT_BACK);
$body = $backend_client->send_query($body, 1234);
print("[FRONT] appending FRONT to message and printing...");
$body .= "\nFRONT receieved";
print("[FRONT] message = \"$body\"");
print("[FRONT] finished");