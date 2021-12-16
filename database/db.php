<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = mysqli_connect('localhost', 'webadmin', '123');
mysqli_select_db($db, 'usersdb');
if(mysqli_connect_errno()){
exit();
}
global $db;
