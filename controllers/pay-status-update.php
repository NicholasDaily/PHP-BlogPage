<?php

session_start();
require '../sql-connect.php';
if(!isset($_SESSION["activeUser"])) die();
$date = $_POST["identifier"];
$value = ($_POST["value"] == "TRUE") ? 1 : 0;
$is_week = (isset($_POST["isWeek"]) && $_POST["isWeek"] == "TRUE") ? true : false;

if($is_week){
	$week_start = (date("l", strtotime($date)) == "Monday") ? $date : date("Y-m-d", strtotime($date . "previous monday"));
	$week_end =  date("Y-m-d", strtotime($week_start . " +6 days"));
	$query = "UPDATE programming_log SET paid = " . $connection->real_escape_string($value) . " WHERE log_date BETWEEN '"
	. $connection->real_escape_string($week_start) . "' AND '" . $connection->real_escape_string($week_end) . "';";
	$connection->query($query);
}else{
	$query = "UPDATE programming_log SET paid = " . $connection->real_escape_string($value) . " WHERE log_date = '" . $connection->real_escape_string($date) . "';";
	$connection->query($query);
	echo $date;
}
?>
