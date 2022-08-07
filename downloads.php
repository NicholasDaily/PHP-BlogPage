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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <base target="_blank">
</head>

<body>
        <?php require "header.php"; ?>

        <div id="wrapper" class="max-width-container">
                        <section id="posts">
                                <?php
                                $result = $connection->query("SELECT * FROM blog_content WHERE type = 3 ORDER BY id DESC;");
                                echo "<div class='post'>";
				echo "<h1>Download links</h1>";
				foreach($result as $row){
                                        $link = $row["content"];
                                        echo "<a href='$link' download>" . substr($link, 6) . "</a>";
                                }
				echo "</div>"
                                ?>
                       </section>


        </div>
</html>
