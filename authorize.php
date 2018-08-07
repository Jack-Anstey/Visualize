<?php
	require_once 'vendor/autoload.php';
	require 'globals.php';

    //connect to spotify
	$session = getSession();

	//These options will allow me to access all the things that I need without storing a users private data
	$options = [
    	'scope' => [
    		//playlist read private introduces a major bug that I don't understand
        	'playlist-read-private',
        	'playlist-read-collaborative',
        	//'user-library-read'
    	],
    	//this makes it so that the user has to relog every single time
    	//'show_dialog' => 'true',
	];

	header('Location: ' . $session->getAuthorizeUrl($options));
	die();
?>