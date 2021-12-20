<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function dbCon(){
    
    $host = "localhost";
    $user = "webadmin";
    $password = "123";
    $db = "usersdb";

    $con = mysqli_connect($host,$user,$password,$db);
    
}

//$s = "SELECT * FROM users WHERE username = '$username'";

// $result = mysqli_query($con, $s);
// $num = mysqli_num_rows($result);

//if ($num == 1) {
//    echo "Username Already Taken";
//} else {
//    $reg = "INSERT INTO users(username, password) VALUES ('$username', '$password')";
//    mysqli_query($con, $reg);
//    echo "Registration Successful";
//}

//if (mysqli_connect_errno()) {
//    exit();
//}

return $con;

?>
