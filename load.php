<?php
	set_time_limit(1200);
	//this methods gets every single track contained within a user's playlists
	function getAllTracks($api, $userId, &$allTracks)
	{
		$playlistIds = array();		
		$playlistUserIds = array();

		//index is used in the creation of the all tracks array
		$index = 0;

		//get all the playlists ids
		try
		{
			$playlists = $api->getUserPlaylists($userId);
		}
		catch(Exception $e)
		{
			//echo("<script>console.log('PHP: ".$e->getMessage()."');</script>");
			//echo $e->getMessage() . "<br>";
			/*if ($e->getCode() == 429) 
			{ 
				// 429 is Too Many Requests
				//echo("<script>console.log('PHP: ".$data."');</script>");
				//echo "Rate Limited :/<br>";
				$retryAfter = $lastResponse['headers']['Retry-After']; // Number of seconds to wait before sending another request
				//echo " retry after " . $retryAfter . " seconds";
			}
			else
			{
				//echo "Something went really wrong getting your playlists, come back later<br>";
			}*/
		}
		
		//places the playlist ids and user ids in two arrays
		foreach ($playlists->items as $playlist)
		{
			array_push($playlistIds, $playlist->id);
			array_push($playlistUserIds, $playlist->owner->uri);
			//echo "Playlist id: " . $playlistIds[count($playlistIds)-1] . " Playlist name: " . ($playlist->name) . "<br>";
		}

		//get EVERY SINGLE SONG in all the above playlists
		//echo "All the tracks in all the users playlists:<br>";
		for($row = 0; $row < count($playlistIds); $row++)
		{
			try
			{
				$playlistTracks = $api->getUserPlaylistTracks($playlistUserIds[$row], $playlistIds[$row]);
			}
			catch(Exception $e)
			{
				echo("<script>console.log('PHP: ".$e->getMessage()."');</script>");
				/*echo $e->getMessage() . "<br>";
				if ($e->getCode() == 429) 
				{ 
					// 429 is Too Many Requests
					echo "Rate Limited :/<br>";
					$retryAfter = $lastResponse['headers']['Retry-After']; // Number of seconds to wait before sending another request
					echo " retry after " . $retryAfter . " seconds";
				}
				else
				{
					echo "Something went really wrong getting your playlist tracks, come back later<br>";
				}*/
			}

			foreach ($playlistTracks->items as $track) 
			{
				$allTracks[$index][0] = $track->track->name;
				$allTracks[$index][1] = $track->track->id;
				//array_push($allTracks, $trackName);
				//echo "Track Name: ". $allTracks[$index][0] . " Track id:" . $allTracks[$index][1] . "<br>";
				$index++;
			}
		}
		return $allTracks;
	}
	
	//this gets all the tracks in a user's saved section. This is currently unused as it drastically increased loading times
	function getSavedTracks($api, &$allTracks)
	{
		$index = count($allTracks);
		try
		{
			$savedTracks = $api->getMySavedTracks();
		}
		catch(Exception $e)
		{
			//echo("<script>console.log('PHP: ".$e->getMessage()."');</script>");
			/*echo $e->getMessage() . "<br>";
			if ($e->getCode() == 429) 
			{ 
				// 429 is Too Many Requests
				echo "Rate Limited :/<br>";
				$retryAfter = $lastResponse['headers']['Retry-After']; // Number of seconds to wait before sending another request
				echo " retry after " . $retryAfter . " seconds";
			}
			else
			{
				echo "Something went really wrong getting your saved tracks, come back later<br>";
			}*/
		}

		//places the track ids and names into an array
		foreach ($savedTracks->items as $track) 
		{
			$allTracks[$index][0] = ($track->track->name);
			$allTracks[$index][1] = ($track->track->id);
			//echo "Track Name: ". $allTracks[$index][0] . " Track id:" . $allTracks[$index][1] . "<br>";
			$index++;
		}
		return $allTracks;
	}

	//This method sorts the tracks into artists + how many times they appear, then places the weighted values into an array with the genres associated with the artist
	function getGenres($api, $allTracks)
	{
		//This sorts the tracks into artists and counts how many times a given artist appears

		$artistCount = array();

		for($index = 0; $index < count($allTracks); $index++)
		{
			if($allTracks[$index][1]!="")
			{
				try
				{
					$track = $api->GetTrack($allTracks[$index][1]);
				}
				catch(Exception $e)
				{
					//echo("<script>console.log('PHP: ".$e->getMessage()."');</script>");
					/*echo $e->getMessage() . "<br>";
					if ($e->getCode() == 429) 
					{ 
						// 429 is Too Many Requests
						echo "Rate Limited :/<br>";
						$retryAfter = $lastResponse['headers']['Retry-After']; // Number of seconds to wait before sending another request
						echo " retry after " . $retryAfter . " seconds";
					}
					else
					{
						echo "Something went really wrong getting your tracks, come back later<br>";
					}*/
				}

				$artistName = $track->artists[0]->name;
				$artistId = $track->artists[0]->id;

				//this method figures out if an artist is new and adds them to a new row, otherwise it adds one to the number of times seen
				//echo "Artist Name: " . $artistName . ". Artist Id: " . $artistId. " Number of times Mentioned: ";
				if(count($artistCount)!=0)
				{
					$count = count($artistCount);

					for($col = 0; $col < $count; $col++)
					{
						if($artistId == $artistCount[$col][1])
						{
							$artistCount[$col][2]+=1;
							//echo $artistCount[$col][2] . "<br>";
							$col = count($artistCount)+1;
						}
						else if($col == count($artistCount)-1)
						{
							$artistCount[$col+1][0] = $artistName;
							$artistCount[$col+1][1] = $artistId;
							$artistCount[$col+1][2]=1;
							//echo $artistCount[$col+1][2] . "<br>";
							$col = count($artistCount)*2;
						}
					}
				}
				else
				{
					$artistCount[0][0] = $artistName;
					$artistCount[0][1] = $artistId;
					$artistCount[0][2] = 1;
					//echo $artistCount[0][2] . "<br>";
				}
			}
		}

		//get the genres for everysingle track
		//currently, the only way to access genres is by getting the genres that make up the artist of a given track
		//This is pretty representative and will still work with my program
		//echo "Length of artistCount: " . count($artistCount) . " numDifferentArtists: " . $numDifferentArtists ."<br>";
		for($index = 0; $index < count($artistCount); $index++)
		{
			$genres = array();
			//This protects against the error of a track having no id
			//This is possible as users on the desktop client can insert their own songs that are locally downloaded but are not avaliable on spotify
			//Because I am doing this by track, even if an artist is recognized the track will not count

			//echo "Artist Name: " . $artistCount[$index][0] . ". Genres Listed: ";
			try
			{
				$genres = $api->getArtist($artistCount[$index][1])->genres;
			}
			catch(Exception $e)
			{
				//echo("<script>console.log('PHP: ".$e->getMessage()."');</script>");
				/*echo $e->getMessage() . "<br>";
				if ($e->getCode() == 429) 
				{ 
					// 429 is Too Many Requests
					echo "Rate Limited :/<br>";
					$retryAfter = $lastResponse['headers']['Retry-After']; // Number of seconds to wait before sending another request
					echo " retry after " . $retryAfter . " seconds";
				}
				else
				{
					echo "Something went really wrong when get genres, come back later<br>";
				}*/
			}

			for($col = 0; $col < count($genres); $col++)
			{
				$artistCount[$index][$col+3] = $genres[$col];
				
				/*echo $artistCount[$index][$col+3];
				if($col != (count($genres)-1))
				{
					echo ", ";
				}
				else
				{
					echo ".";
				}*/
			}
			//echo " index is ". $index . ". Times mentioned: " . $artistCount[$index][2]. "<br>";
		}
		return $artistCount;
	}					
?>