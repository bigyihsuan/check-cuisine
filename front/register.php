<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Check Cuisine Register Page</title>
</head>
<script>
function checkPasswords(form) {
    let match = form.password.value == form.confirm.value;
    if (!match) {
        alert("Passwords don't match.");
    }
    return match;
}
</script>

<body>
    <style>
    * {
        text-align: center;
    }

    input {
        border: 1px solid black;
        break-after: auto;
    }
    </style>
    <p> Registration Page </p>

    <?php
    include_once __DIR__ . "/../servers.php";
    include_once __DIR__ . "/../rabbit_endpoints.php";

    require_once __DIR__ . '/../vendor/autoload.php';
    // include_once __DIR__ . "/../database/db.php";
    // include_once __DIR__ . "/frontsend.php";


    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Message\AMQPMessage;

    $connection = new AMQPStreamConnection(rabbit_server, 5672, front_server_creds[0], front_server_creds[1]);
    $channel = $connection->channel();

    // queue_declare(name, passive?, durable?, exclusive?, auto_delete?, nowait?)
    $channel->queue_declare('front-send', false, true, false, false);
    // check passwords
    // get info from post
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (strcmp($password, $confirm) !== 0) { ?>
    <h1>Passwords don't match!</h1>
    <?php }

    // passwords match, send to the backend
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
        $channel->wait();
        $channel->close();
        $connection->close();
    }

    //echo "Sent login info to backend \n";

    $channel->close();
    $connection->close();

    $_POST['password'] = password_hash($password . $username, null);
    include "./frontsend.php";
    $result = run_query(Prefix::REGISTER);
    list(, $is_success) = explode(" ", $result, 2);
    $is_success = $result === "true" ? true : false;

    if ($is_success) {
        // registration happened goodly
        // login as new user
        $result = run_query(Prefix::LOGIN);
        list(, $is_success) = explode(" ", $result, 2);
        $is_success = $result === "true" ? true : false;
        if ($is_success) {
            // redirect to homepage
            session_start();
            $_SESSION['logged_user'] = $username;
            header("refresh:0; url=frontend.html");
        }
    } else { ?>
    <h1>Error: Error while registering new user</h1>
    <?php
    }
    ?>

    <form method="POST">

        <input name="username" type="text" placeholder="Enter your username"
            required /><br>

        <input type="password" name="password" placeholder="Enter password"
            required /><br>

        <input type="password" name="confirm" placeholder="Re-Enter password"
            required /><br>

        <input type="submit" value="Register" />

        <figcaption><a href="login.html"> Log In Here </a>
            <figcaption>
    </form>

</body>

</html>