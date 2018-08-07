<?php
	session_start();
?>
<!DOCTYPE HTML>
 <html>
 	<?php
		require 'config.php';
		require 'globals.php';
		require 'load.php';
		require_once 'vendor/autoload.php';
	?>

	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type = "text/css" href="style.css"/>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
		<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300" rel="stylesheet">
 		<title>
 			Visualize - Main
 		</title>
 		<script src="d3.min.js"></script>
		<script src="d3pie.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	 </head>

	 <body id = "pie">
	 	<div class = "container">
		 	<?php
		 		//I'm sorry but it takes a very long time to gather the data and 120 seconds unfortunately is not long enough to do so. 12 minutes for now. 6 minutes was not enough. I am not kidding
				set_time_limit(1200);
				$session = getSession();
				$loggedIn = false;

				//Gets the token and user id from the session
				if(!empty($_SESSION["accessToken"]))
				{
					$accessToken = $_SESSION["accessToken"];
					$refreshToken = $_SESSION["refreshToken"];
					$loggedIn = true;
				}
				else
				{
					echo "<br><br>You are not logged in.<br><br>";
				}

				//if you are logged in
				if($loggedIn)
				{
					$api = getAPI($accessToken);
					$session->refreshAccessToken($refreshToken);
					$accessToken = $session->getAccessToken();
					// Set our new access token on the API wrapper and continue to use the API as usual
					$api->setAccessToken($accessToken);

					$_SESSION["api"] = $api;
					$_SESSION["accessToken"] = $accessToken;
					$_SESSION["refreshToken"]  = $refreshToken;

					/*echo "<br>User id is: " . $userId . "<br>";
					echo "Token is: " . $accessToken . "<br>";
					echo "refreshToken is : " . $refreshToken . "<br>";*/
					$allTracks = array();
					$artistCount = array();

					$userId = $_SESSION["userId"];

					$db = getDB();
			  		$query = "SELECT username FROM janstey_spotify_users WHERE username = :username;";
					$statement = $db->prepare($query);
					$statement->execute(array('username' => $userId));
					$result = $statement->fetchAll();

					//simple boolean to see if you are in the data base
					$inBase = (!empty($result));

					//if there is a post code for a refresh and you are actually in the database
					if(($_POST) && ($inBase))
					{
						//learn how to get rid of the get at the top on the refresh
						$query = "DELETE FROM janstey_spotify_users WHERE username = :username;";
						$statement = $db->prepare($query);
						$statement->execute(array('username' => $userId));
						unset($_POST['refresh']);
						$inBase = false;
					}

					//if you are not in the database
					if(!$inBase)
					{
						?>
						<script>
							//This is AJAX That asynconously loads the spotify user data
							xmlhttp = new XMLHttpRequest();
							xmlhttp.onreadystatechange = function() 
							{
								if (this.readyState == 4 && this.status == 200) 
								{
									//place stuff from ajaxLoad into the insertPie div
					                document.getElementById("insertPie").innerHTML = this.responseText;
					                if(document.getElementById('run1')!=null)
					                {
					                	//console.log("please");
					                	//run the script in the div!
					                	eval(document.getElementById('run1').innerHTML);
					                	//console.log(artistCount);
					                }
					                //run the other script!
					                $.getScript("makePie.js", function(data, textStatus, jqxhr)
					                	{
					                		makePie(artistCount);
					                	});
								}

							};
							xmlhttp.open("GET",'ajaxLoad.php?userId=<?php echo $userId?>', true);
							xmlhttp.send();
							//<p>Loading...This can take a couple of minutes</p>
						</script>
						<div id = "insertPie">
							<p id = "loadingText">Loading Your Data...This may take a few Minutes</p>
							<div id = "loader-wrapper">
								<div class = "loader">
									<div id = "loaderLarge"></div>
									<div id = "loaderMedium"></div>
									<div id = "loaderSmall"></div>
									<div id  ="loaderSuperSmall"></div>
								</div>
							</div>	
						</div>
						<?php
					}
					//if you are in the database
					else
					{
						getData($allTracks, $artistCount, $db);
						if(count($artistCount) > 0)
						{
							?>
							<div id = pieChart></div>
							<script>var artistCount = <?php echo json_encode($artistCount); ?>;</script>
			 				<script src="makePie.js"></script>
			 				<script type="text/javascript">makePie(artistCount);</script>
			 				<?php
		 				}
		 				//incase there are no songs
		 				else
		 				{
		 					echo "You have no songs in your playlists.";
		 				}
					}
					//echo "Finished gathering all genres.";
				}
			?>
		 	<a href = "index.php" class ="button">Return Home</a>
		 	<?php
		 		//if you are logged in, show the button to refresh content
			 	if($loggedIn)
			 	{
		 	?>
		 	<br>
		 	<form action="main.php" method="post">
				<input class = "button" id ="button" type="submit" value="Refresh Your Library" name = "refresh">
			</form>
		 	<br><br>
		 	<?php
		 		}
 			?>
		</div>
	</body>

	  <?php
	  	//This menthod has been commented out so that it could be placed into ajaxLoad.php
	  	/*//This method places data into the sql table
	  	function fillSQL($allTracks, $artistCount, $db)
	  	{
	  		$userId = $_SESSION["userId"];
	  		$uniqueId = uniqid();
			$query = "INSERT INTO janstey_spotify_users (id, username) VALUES (:id, :username);";
			$statement = $db->prepare($query);
			$statement->execute(array('id' => $uniqueId, 'username' => $userId));

			for($index = 0; $index < count($allTracks); $index++)
			{
				if($allTracks[$index][1]!=null)
				{
					$query = "INSERT INTO janstey_spotify_tracks (trackid, trackname, userid) VALUES (:trackid, :trackname, :userid);";
					$statement = $db->prepare($query);
					$statement->execute(array('trackid' => $allTracks[$index][1],'trackname' => $allTracks[$index][0], 'userid' => $uniqueId));
				}
			}

			for($index = 0; $index < count($artistCount); $index++)
			{
				$query = "INSERT INTO janstey_spotify_artists (aname, aid, count, userid) VALUES (:aname, :aid, :count, :userid);";
				$statement = $db->prepare($query);
				$statement->execute(array('aname' => $artistCount[$index][0],'aid' => $artistCount[$index][1], 'count' => $artistCount[$index][2], 'userid' => $uniqueId));
			}

			for($index = 0; $index < count($artistCount); $index++)
			{
				for($col = 3; $col < count($artistCount[$index]); $col++)
				{
					$query = "INSERT INTO janstey_spotify_genres (aid, genrename, userid) VALUES (:aid, :genrename, :userid);";
					$statement = $db->prepare($query);
					$statement->execute(array('aid' => $artistCount[$index][1], 'genrename' => $artistCount[$index][$col], 'userid' => $uniqueId));	
				}	
			}
	  	}*/

	  	//get the data from mySQL and place it into the appropriate arrays
	  	function getData(&$allTracks, &$artistCount, $db)
	  	{
	  		$userId = $_SESSION["userId"];
	  		$query = "SELECT * FROM janstey_spotify_users WHERE username = :username;";
			$statement = $db->prepare($query);
			$statement->execute(array('username' => $userId));
			$result = $statement->fetchAll();

			//get the user's id and name
			$uniqueId = $result[0][0];
			$username = $result[0][1];

			//place the users tracks into the track array
			$query = "SELECT trackid, trackname FROM janstey_spotify_tracks WHERE userId = :userId;";
			$statement = $db->prepare($query);
			$statement->execute(array('userId' => $uniqueId));
			$result = $statement->fetchAll(PDO::FETCH_NUM);

			for($row = 0; $row < count($result); $row++)
			{
				for($col = 0; $col < count($result[$row]); $col++)
				{
					//echo $result[$row][$col] . "<br>";
					$allTracks[$row][$col] = $result[$row][$col];
				}
			}

			//place the into stuff into the artist count array
			$query = "SELECT aname, aid, count FROM janstey_spotify_artists WHERE userId = :userId;";
			$statement = $db->prepare($query);
			$statement->execute(array('userId' => $uniqueId));
			$result = $statement->fetchAll(PDO::FETCH_NUM);

			for($row = 0; $row < count($result); $row++)
			{
				for($col = 0; $col < count($result[$row]); $col++)
				{
					if($col == count($result[$row])-1)
					{
						//you have to cast it as an int otherwise things go bad in javascript
						$artistCount[$row][$col] = (int)$result[$row][$col];
					}
					else
					{
						$artistCount[$row][$col] = $result[$row][$col];
					}
				}

				//get the genres where the specific artist is listed and the user id is the same 
				$query = "SELECT genrename FROM janstey_spotify_genres WHERE aid = :aid AND userid = :userid;";
				$statement = $db->prepare($query);
				$statement->execute(array('aid' => $artistCount[$row][1], 'userid' => $uniqueId));
				$genreNames = $statement->fetchAll(PDO::FETCH_NUM);

				for($index = 0; $index < count($genreNames); $index++)
				{
					$artistCount[$row][$index+3] = $genreNames[$index][0];
				}
			}

			/*for($index = 0; $index < count($artistCount); $index++)
			{
				for($col= 0; $col < count($artistCount[$index]); $col++)
				{	
					echo $artistCount[$index][$col] ." ";
				}
				echo "<br>";
			}*/

			/** Database stucture (for future reference if I ever want to rebuild them)
				Spotify Users: 13 character uniqueid, userid from spotify
				spotify tracks: trackid from spotify, trackname from spotify, userid that it is accosiated with
				spotify genres: aid (artist id), genrename from spotify, userid
				spotify artists: aname (artist name), aid, count (how many times the artist is seen), userid
			*/
	  	}
	  ?>
</html>