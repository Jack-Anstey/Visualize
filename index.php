<!DOCTYPE HTML>
 <html>
 		<?php
		require("config.php");	
		?>
 	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type = "text/css" href="style.css"/>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
		<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300" rel="stylesheet">

 		<title>
 			Visualize - Login
 		 </title>
	 </head>

	 <body onload="start()">
	 	<div>
	 		<div>
	 			<div class = "container">
	 				<p>Visualize Your Spotify Profile</p>
	 				<br>
	 				<p><a href = "authorize.php" class = "button">Login and View Your Music</a></p>
	 				<br>
	 				<br>
	 				<p><a href = "information.php" class ="button">Information about Privacy and Data Usage</a></p>
	 			</div>
	 			<script src = "visualizer.js"></script>
	 		</div>
	 	</div>
	  </body>
</html>