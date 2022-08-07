<?php
	session_start();
	require 'sql-connect.php'
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>Projects</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="css/main-style.css">
        <link type="text/css" rel="stylesheet" href="css/post-entry.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base target="_blank">
</head>
<body>
	<?php require "header.php"; ?>
	<div id="wrapper" class="max-width-container">
			<?php 
				if(isset($_SESSION["activeUser"]) && $_SESSION["activeUser"] == "JavaHusky") {
					include "post-entry.html"; 
				} 
			?>
			<section id="posts">
				<?php
				$result = $connection->query("SELECT id FROM blog_posts ORDER BY id DESC LIMIT 5");
				foreach($result as $row){
					echo "<div class='post'>";
					$post_id = $row["id"];
					echo "<p class='post_id' id='$post_id'>#$post_id</p>";
					$post_content = $connection->query("SELECT * FROM blog_content WHERE blog_post_id = $post_id");
					foreach($post_content as $item){
						$data = $item;
						$type = $data["type"];
						$content = $data["content"];
						if($type == 1){
							$content = htmlentities($content);
							echo "<pre>$content</pre>";
						}elseif($type == 2){
							echo "<img src='$content'>";
						}elseif($type == 3){
							echo "<a href='$content'>" . substr($content, 6) . "</a>";
						}
					}
					echo "</div>";
				}
				?>
			</section>
		
		
	</div>
	<script type="text/javascript">
		ajaxReady = true;
		body = document.querySelector("body");
		body.onscroll = function(event){
			scrollDistance = window.scrollY;
			maxScroll = body.clientHeight - window.innerHeight - 30;
			if(scrollDistance > maxScroll){
				if(ajaxReady){
					postContainer = document.getElementById("posts");
					posts = postContainer.children;
					nodes = posts[posts.length - 1].children;
					var postID;
					for(i = 0; i < nodes.length; i++){
						if(nodes[i].className === "post_id")
							postID = nodes[i].id;
					}
					request = new XMLHttpRequest();
					request.open("GET", "controllers/getPosts.php?post_id=" + postID);
					request.send();
					ajaxReady = false;
					request.onreadystatechange = function(){
						if(request.readyState === 4){
							if(request.status === 200){
								console.log(request.response);
								var response = JSON.parse(request.response);
								console.log(response);
								
								displayPosts(response);
								
							}
							ajaxReady = true;
						}
					}
				}
				
			}
		}
		
		function displayPosts(json_code){
			for(i = 0; i < json_code.length; i++){
				post = document.createElement("div");
				post.classList.add("post");
				postID = document.createElement("p");
				postID.innerHTML= "#" + json_code[i].PostID;
				postID.classList.add("post_id");
				postID.id = json_code[i].PostID;
				post.appendChild(postID);
				
				postItems = json_code[i].items;
				
				for(j = 0; j < postItems.length; j++){
					console.log(postItems[j]);
					if(postItems[j].type === 1){
						itemToAppend = document.createElement("pre");
						itemToAppend.innerHTML = htmlEntities(postItems[j].content);
						post.appendChild(itemToAppend);
					}else if(postItems[j].type === 2){
						itemToAppend = document.createElement("img");
						itemToAppend.src = postItems[j].content;
						post.appendChild(itemToAppend);
					}else if(postItems[j].type === 3){
						itemToAppend = document.createElement("a");
						itemToAppend.innerHTML = postItems[j].content;
						itemToAppend.href = postItems[j].content;
						post.appendChild(itemToAppend);
					}
				}
				document.getElementById("posts").appendChild(post);
			}
		}
		
	function htmlEntities(str) {
    		return str.replaceAll("&", '&amp;').replaceAll("<", '&lt;').replaceAll(">", '&gt;').replaceAll('"', '&quot;');
	}
	</script>

</body>
</html>
