<?php
	//I am storing the userId and token in a SESSION variable
	session_start();
	
	require 'vendor/autoload.php';
	require 'globals.php';
	$session = getSession();

	try
	{
		//If the user presses cancel it just sends them back to the home screen (index.php)
		if($_GET['error'] == "access_denied")
		{
			header('Location: index.php');
		}
		//Otherwise info was submitted
		else
		{
			// Request a access token using the code from Spotify
			$session->requestAccessToken($_GET['code']);
			$accessToken = $session->getAccessToken();
			$refreshToken = $session->getRefreshToken();
			$db = getDB();
			$id = uniqid();

			//This uses the API immediately to get the userId of the user and insert it into the database
			$api = getAPI($accessToken);

			$api->setReturnType(SpotifyWebAPI\SpotifyWebAPI::RETURN_ASSOC);

		    $me = $api->me();
		    $userId = $me['id'];

			$_SESSION["accessToken"] = $accessToken;
		    $_SESSION["userId"] = $userId;
		    $_SESSION["refreshToken"] = $refreshToken;
		    $_SESSION["artistCount"] = null;
		    $_SESSION["allTracks"] = null;
			header('Location: main.php');
			die();
		}
	}
	catch (Exception $e)
	{
		header('Location: index.php');
		die();
	}
?>