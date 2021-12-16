<?php
$data = $_POST;

if (empty($data['username']) || empty($data['password'])) {
  die('Username or password are required!');
  }
  
  $username = $data['username'];
  $password = $data['password'];
  
  $dsn = 'mysql:dbname=usersdb;host=localhost';
  $dbUser = 'webadmin';
  $dbPassword = '123';
  
  try{
    $connection = new PDO($dsn, $dbUser, $dbPassword);
  } catch (PDOException $exception) {
      die('Connection failed: ' . $exception->getMessage()));
  }
  
  $statement = $connection->prepare( statement: 'SELECT * FROM users WHERE username = :username');
  $statement->execute([':username' => $username]);
  $result = $statement->fetchAll(fetch_style PDO::FETCH_ASSOC);
  
  if(empty($result)) {
    die('No such user with the username!');
 }
 
 $user = array_shift(&array: $result);
 
 if ($user['username'] === $username && $user['password'] === $password) {
    echo 'You have successfully logged in!';
}else{
    die('Incorrect username or password!');
}
  
