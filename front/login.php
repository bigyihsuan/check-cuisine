<?php
include_once __DIR__ . "/../servers.php";
include_once __DIR__ . "/../rabbit_endpoints.php";
include_once __DIR__ . "/frontsend.php";

// check passwords
// get info from post
$username = $_POST['username'];
$password = $_POST['password'];

$data = $_POST;

if (empty($data['username']) || empty($data['password'])) {
  die('Username or password are required!');
}

$username = $data['username'];
$password = $data['password'];

$dsn = 'mysql:dbname=usersdb;host=localhost';
$dbUser = 'webadmin';
$dbPassword = '123';

try {
  $connection = new PDO($dsn, $dbUser, $dbPassword);
} catch (PDOException $exception) {
  die('Connection failed: ' . $exception->getMessage());
}

$statement = $connection->prepare('SELECT * FROM users WHERE username = :username');
$statement->execute([':username' => $username]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

if (empty($result)) {
  die('No such user with the username!');
}

$user = array_shift($result);

if ($user['username'] === $username && $user['password'] === $password) {
  echo 'You have successfully logged in!';
} else {
  die('Incorrect username or password!');
}

if ($is_success) {
    // redirect to homepage
    session_start();
    $_SESSION['logged_user'] = $username;
    header("refresh:0; url=frontend.html");
} else {
    // echo out a fail message
    echo "<h1>Error: incorrect username or password!</h1>";
    header("refresh:2; url=login.html");
}
