<?php
session_start();
require '../sql-connect.php';
 if(!(isset($_SESSION["activeUser"]) && $_SESSION["activeUser"] == "JavaHusky")) die();
$img_target_dir = "../img/";
$file_target_dir = "../files/";


$imgs = $_FILES["upload-image"]['tmp_name'];
$files = $_FILES["upload-file"]['tmp_name'];

$total_images = count($imgs);
$total_files = count($files);

$connection->query("INSERT INTO blog_posts(user_id, date) VALUES(1, NOW());");
$result = $connection->query("SELECT id FROM blog_posts ORDER BY id DESC LIMIT 1;");
$row = $result->fetch_assoc();
$post_id = $row["id"];
echo "POST ID: " . $post_id;
echo '<div class="post" id="' . $post_id . '">';


if($_POST["post-text"] != ""){
	$post_text = $connection->real_escape_string($_POST["post-text"]);
	$sql = "INSERT INTO blog_content(blog_post_id, type, content) VALUES($post_id, 1, '$post_text');"; 
	if($connection->query($sql) === FALSE){
		echo "ERROR: " . $connection->error . "<br>";
                echo "$sql";
	}
	echo      '<pre>' . $_POST["post-text"] . '</pre>';
}
for($i = 0; $i < $total_images; $i++){
	$tmpFilePath = $imgs[$i];
	if($tmpFilePath != ""){
		$newFilePath =  $img_target_dir . basename($_FILES["upload-image"]["name"][$i]);
		if(move_uploaded_file($tmpFilePath, $newFilePath)){
			$connection->query("INSERT INTO blog_content(blog_post_id, type, content)
			                   VALUES($post_id, 2, \"$newFilePath\");");
			echo '<img src="' . $newFilePath . '">';
		}
	}
}

for($i = 0; $i < $total_files; $i++){
	$tmpFilePath = $files[$i];
	if($tmpFilePath != ""){
		$newFilePath = $file_target_dir . basename($_FILES["upload-file"]["name"][$i]);
		if(move_uploaded_file($tmpFilePath, $newFilePath)){
			$connection->query("INSERT INTO blog_content(blog_post_id, type, content)
			                   VALUES($post_id, 3, \"$newFilePath\");");
			echo '<a href="' . $newFilePath . '">' . $newFilePath . '</a>';
		}	
	}
}

echo '</div>';

?>
