<?php
//Require Once All Other Necessary Rabbit Files
require_once('db.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function register($username, $password)
{
   $con = dbCon();

   $check = "SELECT * FROM users WHERE username = '$username'";

   $result = mysqli_query($con, $check);
   $num = mysqli_num_rows($result);

   $username_available = $num == 0; // true if username is available

   if (!$username_available) {
      echo "Registration failed";
      return;
   }

   $hashedpass = passHash($password);
   $reg = "INSERT INTO users(username, hashedpass) VALUES ('$username', '$hashedpass')";
   $query_status = mysqli_query($con, $reg);
   echo $query_status ? "Registration Successful" : "Registration failed";
}

function login($username, $password)
{
   $con = dbCon();
   $query = "SELECT * FROM users WHERE username = '$username'";
	$result = $con->query($query);
   if($result){
		if($result->num_rows == 0){
			echo("No users in table.");
         return false;
      }else{
			while($row = $result->fetch_assoc()){
				$hashedpass = passHash($password);
				if ($row['hashedpass'] == $hashedpass){
               echo "User Authenicated".PHP_EOL;
					return 1;
				}
         }
      }
   }
}

function passHash($password)
{
   $new = $password . 'abcdefghjiklmaopqrstuvwxyz1234567890';
   $hash = hash('sha256', $new);
   return $hash;
}
