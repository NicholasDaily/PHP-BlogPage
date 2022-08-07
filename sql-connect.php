<?php
$servername = "127.0.0.1";
$username = "username";
$password = "password";
$database = "db";

$connection = new mysqli($servername, $username, $password, $database);
$connection->autocommit(true);
if($connection->connect_error){
	die("Connection failed: " . $connection->connect_error);
        echo "could not connect to database!";
}
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
?>
