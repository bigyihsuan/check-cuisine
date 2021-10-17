<?php
include "../servers.php";
include "../rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rabbit_server, 5672, $back_server_creds[0], $back_server_creds[1]);
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

    $backend_client->send_query($query, $prefix);
}