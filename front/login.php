<?php
include_once __DIR__ . "/../servers.php";
include_once __DIR__ . "/../rabbit_endpoints.php";
include_once __DIR__ . "/frontsend.php";

// check passwords
// get info from post
$username = $_POST['username'];
$password = $_POST['password'];


// login as given user
$result = run_query(Prefix::LOGIN);
list(, $is_success) = explode(" ", $result, 2);
$is_success = $result === "true" ? true : false;

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