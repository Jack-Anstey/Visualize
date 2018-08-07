<!DOCTYPE HTML>
 <html>
 	<head>
 		<meta charset="UTF-8">
		<link rel="stylesheet" type = "text/css" href="style.css"/>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
		<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300" rel="stylesheet">
 		<title>
 			Visualize - Information
 		 </title>
	 </head>

	 <body>
	 	<div>
	 		<div class ="container">
	 			<p class = "bold">Important User Information</p>
	 			<p>User data is stored in a mySQL database so if you choose to come back, your data will load incredibly quickly.</p>
	 			<p>This is a student project for my Highschool CS Class. Here is a list of the data I gather from your Spotify account and why:</p>
	 			<div class = "inner">	
	 				<table>
	 					<tr>
	 						<th>Permission</th>
	 						<th>Reasoning</th>
	 					</tr>
	 					<tr>
	 						<th>Access your private playlists</th>
	 						<th>This allows Visualizer to see every song that you own</th>
	 					</tr>
	 					<tr>
	 						<th>Access your collaborative playlists</th>
	 						<th>This allows Visualizer to see all the songs that you own</th>
	 					</tr>
	 				</table>
	 			</div>
 				<p>Passwords are NEVER stored on this website or in a database. All password management is done by Spotify.</p>
 				<p>The "Refresh Your Library" button is for updating what is placed in the database if you have deleted or added playlists. This program does not do that on its own.</p>
 				<p>The "other" category is made up of genres that make up less than 0.5% of your account.</p>
 				<p>Thanks to Jonathan Wilsson on GitHub and other contributors in writing a PHP wrapper for the Spotify API</p>
 				<p>Thanks to Ben Keen for writing d3Pie to create the pie chart</p>
 				<p>I hope that you enjoy my website, Visualize</p>
 				<p><a href = "index.php" class ="button">Visualize Home</a></p>
	 		</div>
	 	</div>
	  </body>
</html>