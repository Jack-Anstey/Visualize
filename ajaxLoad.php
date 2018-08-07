<?php
	require 'globals.php';
	require 'load.php';
	require_once 'vendor/autoload.php';
	session_start();
?>
<script src="d3.min.js"></script>
<script src="d3pie.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<?php
	//Failed to load resource: the server responded with a status of 500 (Internal Server Error) getting this a lot
	//RIP trying to fix that bug
	/*
	try
	{
		fixingBugs();
	}
	catch(Failure $f)
	{
		echo cry();
	}
	*/

	//ajax everything
	$db = getDB();
	$allTracks = array();
	$artistCount = array();
	$allTracks = getAllTracks($_SESSION['api'], $_GET['userId'], $allTracks);
	//$allTracks = getSavedTracks($api, $allTracks);
	$artistCount = getGenres($_SESSION['api'], $allTracks);

	if(count($artistCount)==0)
	{
		echo "Looks like you don't have any playlists! Go make some and come back!";
	}
	fillSQL($allTracks, $artistCount, $db);

	//This method places data into the sql table
  	function fillSQL($allTracks, $artistCount, $db)
  	{
  		//get the userid
  		$userId = $_SESSION["userId"];
  		$uniqueId = uniqid();

  		//insert the user into the database
		$query = "INSERT INTO janstey_spotify_users (id, username) VALUES (:id, :username);";
		$statement = $db->prepare($query);
		$statement->execute(array('id' => $uniqueId, 'username' => $userId));

		//insert the tracks into the database
		for($index = 0; $index < count($allTracks); $index++)
		{
			if($allTracks[$index][1]!=null)
			{
				$query = "INSERT INTO janstey_spotify_tracks (trackid, trackname, userid) VALUES (:trackid, :trackname, :userid);";
				$statement = $db->prepare($query);
				$statement->execute(array('trackid' => $allTracks[$index][1],'trackname' => $allTracks[$index][0], 'userid' => $uniqueId));
			}
		}

		//insert the artists and how many times they appear into the database
		for($index = 0; $index < count($artistCount); $index++)
		{
			$query = "INSERT INTO janstey_spotify_artists (aname, aid, count, userid) VALUES (:aname, :aid, :count, :userid);";
			$statement = $db->prepare($query);
			$statement->execute(array('aname' => $artistCount[$index][0],'aid' => $artistCount[$index][1], 'count' => $artistCount[$index][2], 'userid' => $uniqueId));
		}

		//insert genre names and artist values into the database
		for($index = 0; $index < count($artistCount); $index++)
		{
			for($col = 3; $col < count($artistCount[$index]); $col++)
			{
				$query = "INSERT INTO janstey_spotify_genres (aid, genrename, userid) VALUES (:aid, :genrename, :userid);";
				$statement = $db->prepare($query);
				$statement->execute(array('aid' => $artistCount[$index][1], 'genrename' => $artistCount[$index][$col], 'userid' => $uniqueId));	
			}	
		}
  	}
?>
<div id = "pieChart"></div>
<script id = "run1">var artistCount = <?php echo json_encode($artistCount); ?>;</script>
<script id = "run2" src="makePie.js"></script>