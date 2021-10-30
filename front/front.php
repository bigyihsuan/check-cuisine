<?php
include_once "servers.php";
include_once "rabbit_endpoints.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
/*
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
*/
    
$consume_channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

$consume_channel->queue_bind($queue, $exchange);

/**
 * @param \PhpAmqpLib\Message\AMQPMessage $message
 */
function process_message($message)
{
    echo "\n--------\n";
    echo $message->body;
    echo "\n--------\n";

    $message->ack();

    // Send a message with the string "quit" to cancel the consumer.
    if ($message->body === 'quit') {
        $message->getChannel()->basic_cancel($message->getConsumerTag());
    }
}

/*
    queue: Queue from where to get the messages
    consumer_tag: Consumer identifier
    no_local: Don't receive messages published by this consumer.
    no_ack: If set to true, automatic acknowledgement mode will be used by this consumer. See https://www.rabbitmq.com/confirms.html for details.
    exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
    nowait:
    callback: A PHP Callback
*/

$consume_channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

/**
 * @param \PhpAmqpLib\Channel\AMQPChannel $channel
 * @param \PhpAmqpLib\Connection\AbstractConnection $connection
 */
function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);

// Loop as long as the channel has callbacks registered
while ($channel->is_consuming()) {
    $channel->wait();
}
