<?php
	function getSession()
	{
		$session = new SpotifyWebAPI\Session(
		//Client ID
		'7cc7efa894cd4d2bb776278897069dfe',
		//Secret ID you shouldn't know about!
		'7f6f1e9b9a224b3682d9ab99c802a78a',
		//Redirect URI
		'http://localhost/Websites/Final%20Project/callback.php'
		);
		return $session;
	}

	function getAPI($accessToken)
	{

		$api = new SpotifyWebAPI\SpotifyWebAPI();;
		$api->setAccessToken($accessToken);
		
		return $api;
	}

	function getDB()
	{
		require("config.php");

		//This function creates a link to your database
		//mysql
		$db = new 
			PDO("mysql:dbname={$GLOBALS["database"]};host={$GLOBALS["hostname"]}", 
		$GLOBALS["username"], $GLOBALS["password"]);

		// All computers need the rest of the lines
		$db->setAttribute(PDO::ATTR_ERRMODE, 
					       PDO::ERRMODE_EXCEPTION);
		return $db;
	}
?>