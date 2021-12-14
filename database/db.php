<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if (!isset($db)) {
//     try {
//         $db = new mysqli('25.53.49.9', 'webadmin', '123', 'usersdb');
//         // $db = new PDO($conn, $dbuser, $dbpass);
//     } catch (Exception $e) {
//         var_export($e);
//         $db = null;
//     }
// }
$db = new mysqli('25.53.49.9', 'webadmin', '123', 'usersdb');
global $db;
