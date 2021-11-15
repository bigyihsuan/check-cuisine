
<?php  
$conn = pg_connect("host=node87495-checkcuisinedb.ny-2.paas.massivegrid.net port=5432 dbname=usersdb user=webadmin password=1pQCRimGg5");  

if (!$conn) {  
 echo "An error occurred.\n";  
 exit;  
}  
$result = pg_query($conn, "SELECT * FROM users");  
if (!$result) {  
 echo "An error occurred.\n";  
 exit;  
}  
while ($row = pg_fetch_row($result)) {  
 echo "value1: $row[0]  value2: $row[1]";  
 echo "<br />\n";  
}  
?>  