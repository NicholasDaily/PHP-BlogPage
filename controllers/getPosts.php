<?php
	require '../sql-connect.php';
	$id = $connection->real_escape_string($_GET["post_id"]);
	$limit = 5;
	
	$result = $connection->query("SELECT id FROM blog_posts WHERE id < $id ORDER BY id DESC LIMIT $limit");
				echo "[\n";
				$x = 1;
				
				foreach($result as $row){
					echo "{\n";
					$post_id = $row["id"];
					echo "\t\"PostID\":$post_id,\n";
					echo "\t\"items\":\n\t[\n";
					$post_content = $connection->query("SELECT * FROM blog_content WHERE blog_post_id = $post_id");
					$maxY = $post_content->num_rows;
					$y = 1;
					foreach($post_content as $item){
						$type = $item["type"];
						$content = $item["content"];
						echo "\t\t{\n";
						echo "\t\t\t\"type\":$type,\n";
						if($type == 1)
							$content = jsonentities($content);
						echo "\t\t\t\"content\":\"$content\"\n";
						echo "\t\t}";
						if($y !== $maxY)
							echo ",";
						echo "\n";
						$y++;
					}
					
					echo "\t]\n}";
					if($x !== $result->num_rows)
						echo ",";
					echo "\n";
					$x++;
				}
				echo "]";
	function jsonentities($something){
		//$something = htmlentities(htmlentities($something));
		$something = str_replace('\\', '\\\\', $something);
		//$something = str_replace("'", "\\'", $something);
		$something = str_replace('"', '\\"', $something);
		$something = str_replace("\n", "\\n", $something);
		$something = str_replace("\r", "\\r", $something);
		$something = str_replace("\t", "\\t", $something);
		return $something;
	}
?>
