
<?php
//Require Once All Other Necessary Rabbit Files
require_once ('db.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function register($username, $password){
   $con = dbCon();
  
   $check = "SELECT * FROM users WHERE username = '$username'";

   $result = mysqli_query($con, $check);
   $num = mysqli_num_rows($result);

   if ($num == 0) {
      return true; //username available
   }else{
      return false; //username not available
   }
   
   $hashedpass = passHash($password);
   $reg = "INSERT INTO users(username, hashedpass) VALUES ('$username', '$hashedpass')";
   mysqli_query($con, $reg);
   echo "Registration Successful";

function login($username, $password){
   $con = dbCon();
}

function passHash($password){
	$new = $password . 'sdawfegrthyjuhtgrfeddwadasxcx0720';
	$hash = hash('sha256',$new);
	return $hash;
}
 
?>  
