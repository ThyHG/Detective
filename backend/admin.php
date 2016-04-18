<?php

//create folder structure  if it doesn't exist
if( !file_exists("data") ){
    mkdir("data");
    mkdir("data".DIRECTORY_SEPARATOR."facts");
    mkdir("data".DIRECTORY_SEPARATOR."save");
}

//game status, true = running, false = offline
$game_running = file_exists("j.on");

$player_count = count(glob("data/facts/*"));

//for msgs
$notice = "";

if(isset($_GET["start"])){

	if(!$game_running){

		//create file to indicate game state
		touch("j.on");

		$notice = "Started!";

	} else{

		$notice = "Game already running!";

	}
	
} elseif(isset($_GET["stop"]) && $game_running){

	$game_running = false;

	//delete "on" file
	unlink("j.on");

	//delete game data i.e. client facts and associated cards
	array_map("unlink", glob("data/facts/*"));
	array_map("unlink", glob("data/save/*"));

	$notice = "Game stopped!";
}

//front end
include("admin_template.php");

header("Refresh:7; url=admin.php");

/*
 * for debugging
 */
function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>
