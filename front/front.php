<?php
include_once __DIR__ . "/../servers.php";
include_once __DIR__ . "/../rabbit_endpoints.php";
include_once __DIR__ . "/login.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use PhpAmqpLib\Exchange\AMQPExchangeType;

$exchange = 'router';

$publish_connection = new AMQPStreamConnection(rabbit_server, 5672, front_server_creds[0], front_server_creds[1]);
$consume_connection = new AMQPStreamConnection(rabbit_server, 5672, front_server_creds[0], front_server_creds[1]);
$publish_channel = $publish_connection->channel();
$consume_channel = $consume_connection->channel();

$publish_channel->queue_declare(FRONT_BACK, false, true, false, false);
// $channel->queue_declare(BACK_FRONT, false, true, false, false);

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

// $backend_client = new Client($connection, FRONT_BACK);
// $body = $backend_client->send_query($body, "");

$body = readline("Enter message content: ");
print("[FRONT] sending message to BACK...\n");
print("[FRONT] message = \"$body\"\n");
$message = new AMQPMessage($body);
$publish_channel->basic_publish($message, "", FRONT_BACK);

$handle_back_to_front = function (AMQPMessage $message) {
    print("[FRONT] received message from BACK!\n");
    $body = $message->getBody();
    print("[FRONT] appending FRONT to message and printing...\n");
    $body .= "\nFRONT receieved";

    print("[FRONT] message = \"$body\"\n");
    print("[FRONT] finished\n");
};

$consume_channel->basic_consume(FRONT_BACK, "", $handle_back_to_front);

while ($consume_channel->is_open()) {
    $consume_channel->wait();
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();

/*
if (isset($argv[0])) {
    $msg = new AMQPMessage($argv[0]);
    $publish_channel->basic_publish($msg, '', FRONT_BACK);
    echo "Sent '{$msg->getBody()}'\n";
}

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
    global $publish_channel;

    echo ' [x] Received ', $msg->body, "\n";
    $m = readline("Message: ");
    $msg = new AMQPMessage($m);
    $publish_channel->basic_publish($msg, '', BACK_DATA);
    echo "Sent '$m'\n";
};

// basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
$consume_channel->basic_consume(BACK_DATA, '', false, true, false, false, $callback);

while ($consume_channel->is_open()) {
    $consume_channel->wait();
}

$publish->close();
$consume->close();
$publish_channel->close();
$consume_channel->close();
*/