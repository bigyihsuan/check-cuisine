<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>logged Check Cuisine</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>

<body>
    <div class="center">
        <?php
        include_once __DIR__ . "/../servers.php";
        include_once __DIR__ . "/../rabbit_endpoints.php";

        require_once __DIR__ . '/../vendor/autoload.php';

        use PhpAmqpLib\Connection\AMQPStreamConnection;
        use PhpAmqpLib\Message\AMQPMessage;

        $connection = new AMQPStreamConnection(rabbit_server, 5672, front_server_creds[0], front_server_creds[1]);
        $channel = $connection->channel();

        // queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
        $channel->queue_declare('front-send', false, true, false, false);

        function run_query($prefix)
        {
            global $connection, $username, $password;
            $backend_client = new Client($connection, 'front-send');
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
        /*
$end = 1;
for ($i = 0; $i < $end; $i++) {
    $m = readline("Message $i: ");
    $msg = new AMQPMessage("$i $m");
    $channel->basic_publish($msg, '', 'front-send');

    echo "Sent '$m'\n";
}
*/
        //$username = readline("Username: ");
        //$password = readline("Password: ");

        $username = $_GET['username'];
        $password = $_GET['password'];

        $userpass = array($username => $password);
        $userpass_json = json_encode($userpass);

        $msg1 = new AMQPMessage("0 $userpass_json");
        $channel->basic_publish($msg1, '', 'front-send');

        // $msg2 = new AMQPMessage($m);
        // $channel->basic_publish($msg2, '', 'front-send');

        $channel->queue_declare('front-receive', false, true, false, false);

        // echo "Sent login info to backend \n\n";

        // echo " [*] Receiving data. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo <<<HTML
            <h1>{$msg->body}</h1>
            HTML;
            // echo ' [x] Received ', $msg->body, "\n";
        };

        // basic_consume(queue name, consumer tag, no local?, no ack?, exclusive?, no wait?, callback)
        $channel->basic_consume('front-receive', '', false, true, false, false, $callback);

        while ($channel->is_open()) {
            error_log("waiting for message...");
            $channel->wait(null, true);
        }

        //echo "Sent login info to backend \n";

        $channel->close();
        $connection->close();
        ?>
    </div>
</body>