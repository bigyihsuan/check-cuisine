<?php
include "./servers.php";

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Client
{
    private $connection;
    private $channel;
    private $queue_name;
    private $callback_queue;
    private $response;
    private $correlation_id;

    public function __construct(AMQPStreamConnection $connection, string $queue_name)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
        list($this->callback_queue,,) = $this->channel->queue_declare("", false, false, true, false);
        $this->channel->basic_consume($this->callback_queue, "", false, true, false, false, array($this, "onResponse"));
        $this->queue_name = $queue_name;
    }

    public function onResponse(AMQPMessage $rep)
    {
        if ($rep->get('correlation_id') == $this->correlation_id) {
            $this->response = $rep->body;
        }
    }

    public function send_query(string $query, int $code): string
    {
        $this->response = null;
        $this->correlation_id = uniqid();

        $message = new AMQPMessage($query, array(
            "correlation_id" => $this->correlation_id,
            "reply_to" => $this->callback_queue
        ));
        $this->channel->basic_publish($message, $code, $this->$queue_name);

        while (!$this->response) {
            $this->channel->wait();
        }

        return $this->response;
    }
}