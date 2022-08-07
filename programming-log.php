<?php
        session_start(); 
        require 'sql-connect.php';
	$version_number = "1-0-2";
?>
<!DOCTYPE HTML>
<html>

<head>
        <title>Projects</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Old+Standard+TT&display=swap" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="css/main-style.css?version=<?php echo $version_number; ?>">
	<link type="text/css" rel="stylesheet" href="css/log-style.css?version=<?php echo $version_number; ?>">

	<?php 
		$USER_SET = isset($_SESSION["activeUser"]);
		if($USER_SET){
			echo "<link type='text/css' rel='stylesheet' href='css/log-entry-style.css?version=$version_number'>";
			echo "<link type='text/css' rel='stylesheet' href='css/log-style-2.css?version=$version_number'>";
		}
	
	if($_SESSION["activeUser"] == "Tamino") echo '<script type="text/javascript" src="pay-status.js"></script>';	
	?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <base target="_blank">
</head>

<body>
        <?php require "header.php"; ?>

        <div id="wrapper" class="max-width-container">
		<?php
			if($USER_SET && $_SESSION["activeUser"]) {
				include "log-entry.php"; 
			} 
		?>

		<section id="posts">
                	<div class="post">
				<div id="entry-container">
<section id="log-headers" class="entry">
	<div class="date-duration">date/hours</div>
	<div class="description-tags">description/tags</div>
	<?php if($USER_SET){
		echo '<div class="edit">edit</div>
		<div class="pay-amount">pay</div>
		<div class="pay-status">status</div>';
	}?>
</section>
				<?php

				#set the week's bounds
				$get_start_date = isset($_GET["start_date"]) ? $_GET["start_date"] : date("Y-m-d");
				$week_start_date = (date("l", strtotime($get_start_date)) == "Monday") ? $get_start_date : date("Y-m-d", strtotime($get_start_date . "previous monday"));

				$week_end_date = date("Y-m-d", strtotime($week_start_date . " +6 days"));

				$query = "SELECT * FROM programming_log WHERE log_date BETWEEN '$week_start_date' AND '$week_end_date' ORDER BY log_date ASC;";
				$result = $connection->query($query);
				$row = $result->fetch_assoc();
				$pay_total = 00.00;
				$hours_total = 00.00;
				$week_paid = true;
				for($i = 0; $i < 7; $i++){
					$rate = 12.00;
					$date = date("Y-m-d", strtotime($week_start_date . "+$i days"));
					$date_formatted = date("d-m-Y", strtotime($date));
					$description = "No entry for $date_formatted.";
					$hours = 00.00;
					$paid = true;
					$tags = [];
					$pay = 00.00;
					if($row["log_date"] == $date){
						$description = $row["description"];
						$hours = floatval($row["duration"]);
						$paid = (intval($row["paid"]) == 1) ? true : false;
						if(!$paid) $week_paid = false;
						$query = "";
						$pay_total += $rate * $hours;
						$hours_total += $hours;
						$log_id = intval($row["id"]);
						$tag_query = "
						SELECT  log_tags.tag
						FROM log_to_tag
						INNER JOIN log_tags ON log_to_tag.tag_id=log_tags.id
						INNER JOIN programming_log ON log_to_tag.log_id=programming_log.id
						WHERE programming_log.id = $log_id;
						";
						$tag_result = $connection->query($tag_query);
						$someting = $connection->error;
						echo "<script>console.log(\"$someting\");</script>";
						while($tag_row = $tag_result->fetch_assoc()){
							array_push($tags, $tag_row["tag"]);
						}
					}
					echo "<section class='entry'>\n";
					echo "\t<div class='date-duration'>\n";
					echo "\t\t<p class='date'>$date_formatted</p>\n";
					echo "\t\t<p class='duration'>" . htmlentities($hours) . "hrs</p>\n";
					echo "\t</div>\n";
					if($description == "No entry for $date_formatted.")
						echo "\t<div class='description-tags no-description'>\n";
					else
						echo "\t<div class='description-tags'>\n";
					echo "\t\t<p class='description'>" . htmlentities($description) . "</p>\n";
					echo "\t\t<p class='tags'>";
					if(!empty($tags)){
						foreach($tags as $tag)	
							echo "<span class='tag'>" . htmlentities($tag) . "</span>";
					}
					echo "</p>\n";
					echo "\t</div>";
					if($USER_SET){
					echo "\t<div class='edit' onClick=\"updateLog('$date', this)\">✎</div>\n";
					echo "\t<div class='pay-amount'>£" . $rate * $hours . "</div>\n";
					$pay_symbol = $paid ? "✔" : "ⓧ"; 
					$pay_tag_id = $paid ? "payment-complete" : "payment-incomplete";
					$pay_status_params = $paid ? "'$date', 'FALSE'" : "'$date', 'TRUE'";
					if($description != "No entry for $date_formatted." && $_SESSION["activeUser"] == "Tamino")
						echo "\t<div class='pay-status'><p onClick=\"changePayStatus($pay_status_params, this)\" class='$pay_tag_id'>$pay_symbol</p></div>\n";
					else					
						echo "\t<div class='pay-status'><p class='$pay_tag_id'>$pay_symbol</p></div>\n";
					}
					echo "</section>";
					if($row["log_date"] == $date) $row = $result->fetch_assoc();
				}

				?>
				</div>
				<div id="log-totals">
				<p id="hours-total"><strong>Week hours total:</strong> <?php echo $hours_total ?></p>
				<?php
				if($USER_SET){
					echo "<p><strong>Rate:</strong> £12.00</p>";
					echo "<p id='pay-total'><strong>Week pay total:</strong> £$pay_total</p>";
					$pay_symbol = $week_paid ? "✔" : "ⓧ"; 
					$pay_tag_id = $week_paid ? "payment-complete" : "payment-incomplete";
					$pay_status_params = $week_paid ? "'$week_start_date', 'FALSE'" : "'$week_start_date', 'TRUE'";
					if($hours_total > 0.00 && $_SESSION["activeUser"] == "Tamino")
						echo "<p onClick=\"changePayStatusWeek($pay_status_params)\" id='paid-status-week' class='$pay_tag_id'>$pay_symbol</p>";
					else
						echo "<p id='paid-status-week' class='$pay_tag_id'>$pay_symbol</p>";
				}
				?>
				</div>
				<div id="date-control">
				<?php
					$previous_link = "http://unitedhuskies.co.uk/programming-log.php?start_date=" . date("Y-m-d", strtotime($week_start_date . "previous monday"));
					$next_link = "http://unitedhuskies.co.uk/programming-log.php?start_date=" . date("Y-m-d", strtotime($week_start_date . "next monday"));
				?>
				<div id="date-control-text">
					<a href="<?php echo $previous_link ?>" target="_self"  id="previous-week" class="date-button"><</a>
					<?php echo date("d-m-Y", strtotime($week_start_date)) ?> 
					<a href="<?php echo $next_link ?>" target="_self" id="next-week" class="date-button">></a></div>
				</div> 
			</div>
		</section>
	</div>
</html>

