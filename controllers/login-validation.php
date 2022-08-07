<?php 
	session_start();
	require '../sql-connect.php';
	if($_POST["email"] != "" && $_POST["password"] != ""){
		$cleanEmail = $connection->real_escape_string($_POST["email"]);
		$result = $connection->query("SELECT * FROM users WHERE email='$cleanEmail';");
		$userRow = $result->fetch_assoc();
		$isValid = $_POST["password"] === $userRow["password"];
		if($isValid){
			echo 1;
			$_SESSION["activeUser"] = $userRow["name"];
		}else{
			echo 0;
		}
		
	}
	
?>