<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if (!isset($db)) {
//     try {
//         $db = pg_connect("host=node87495-checkcuisinedb.ny-2.paas.massivegrid.net port=5432 dbname=usersdb user=webadmin password=1pQCRimGg5");
//         // $db = new PDO($conn, $dbuser, $dbpass);
//     } catch (Exception $e) {
//         var_export($e);
//         $db = null;
//     }
// }
$db = pg_connect("host=node87495-checkcuisinedb.ny-2.paas.massivegrid.net port=5432 dbname=usersdb user=webadmin password=1pQCRimGg5");
global $db;