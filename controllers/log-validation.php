<?php
session_start();

/*
programming_log
---------------
id (auto increment)
log_date (YYYY-MM-DD)
description (text)
duration (##.##)
rate (always use 14.00)
paid (bool 0 or 1)

log_tags
---------------
id (auto increment)
tag (char(15))

log_to_tag
---------------
log_id
tag_id

*/

require '../sql-connect.php';
if(!isset($_SESSION["activeUser"])) die();

$rate = 14.00;
$date_today = date("Y-m-d");
$date = $connection->real_escape_string($_POST['entry-date']);
$description = $connection->real_escape_string($_POST["log-description"]);
$hours = $connection->real_escape_string($_POST["hours"]);
$paid = 0;
$entry_existing = false;
$tags = explode("#", preg_replace('/\s+/', '', $connection->real_escape_string($_POST["tag-text"])));
$week_start_date = (date("l", strtotime($date_today)) == "Monday") ? $date_today : date("Y-m-d", strtotime($date_today . "previous monday"));

$update_hours = ((strtotime($week_start_date) <= strtotime($date)) || $_SESSION["activeUser"] == "Tamino") ? true : false;
$valid_entry = (time() > strtotime($date)) ? true : false;

#remove empty string from start of array
array_shift($tags);

#check if an entry exists or if the entry is new
$query = "SELECT id FROM programming_log WHERE log_date = '$date';";
$result = $connection->query($query);
$entry_existing = (mysqli_num_rows($result) > 0);

#upload entry to programming_log
if($entry_existing){
	if($update_hours){
		$query = "UPDATE programming_log 
			SET description = '$description', 
			duration = $hours WHERE log_date = '$date';";
	}else{
		$query = "UPDATE programming_log 
			SET description = '$description'
	 		WHERE log_date = '$date';";
	}		
}elseif(!$entry_existing && $valid_entry){
	if($update_hours){
		$query = "INSERT INTO programming_log(log_date, description, duration, rate, paid) 
			  VALUES('$date', '$description', $hours, $rate, $paid);";
	}else{
		$query = "INSERT INTO programming_log(log_date, description, rate, paid) 
			  VALUES('$date', '$description', $rate, $paid);";
	}
}else{
	die("Invalid entry: log entry cannot be made for future dates.");
}

$connection->query($query);



#Get the log ID using a search for post from date
$query = "SELECT id FROM programming_log WHERE log_date = '$date';";
$result = $connection->query($query);
$log_id = $result->fetch_assoc()["id"];

#if updating tags, remove old tags
if($entry_existing){
	$query = "DELETE FROM log_to_tag WHERE log_id = $log_id;";
	$connection->query($query);
}


#create function to check tag table for tag existence
function getTagId($tag, $conn){
	$query = "SELECT id FROM log_tags WHERE tag = '$tag';";
	$result = $conn->query($query);
	if(mysqli_num_rows($result) > 0){
		return intval($result->fetch_assoc()["id"]);
	}else{
		return -1;
	}
}


#if tag exists get id and link it to log_id in log_to_tag
#if tag doesn't exist insert it into log_tags and then link in log_to_tag 

foreach($tags as $tag){
	$tag = "#" . $tag;
	$tag_id = getTagId($tag, $connection);
	if($tag_id < 0){
		$query = "INSERT INTO log_tags(tag) VALUES('$tag');";
		$connection->query($query);		
		$tag_id = getTagId($tag, $connection);
	}
	$query = "INSERT INTO log_to_tag(log_id, tag_id) VALUES($log_id, $tag_id);";
	$connection->query($query);
}
?>
