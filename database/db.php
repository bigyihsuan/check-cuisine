<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = mysqli_connect('localhost', 'webadmin', '123');
mysqli_select_db($db, 'usersdb');

$username = $_POST['username'];
$password = $_POST['password'];

$s = "SELECT * FROM users WHERE username = '$username'";

$result = mysqli_query($db, $s);
$num = mysqli_num_rows($result);

if ($num == 1) {
    echo "Username Already Taken";
} else {
    $reg = "INSERT INTO users(username, password) VALUES ('$username', '$password')";
    mysqli_query($db, $reg);
    echo "Registration Successful";
}

if (mysqli_connect_errno()) {
    exit();
}
global $db;