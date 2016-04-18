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

		//create score file for future scoring
		startScore();

		$notice = "Started!";

	} else{

		$notice = "Game already running!";

	}
	
} elseif(isset($_GET["stop"]) && $game_running){

	$game_running = false;

	//delete "on" file
	unlink("j.on");

	//delete game data i.e. client facts and associated cards
	//facts: collected answers per ID - save: keeping track which IDs were used for xy ID already
	array_map("unlink", glob("data/facts/*"));
	array_map("unlink", glob("data/save/*"));

	//delete score file
	unlink("data/score.json");

	$notice = "Game stopped!";
}

//front end
include("admin_template.php");

header("Refresh:7; url=admin.php");


/**
 * reads current players and creates a score file
 */
function startScore(){

	//read player ids (= participatns)
	$name_ids = array_map("basename", glob("data/facts/*"));

	//data to write
	$data = [];

	//prepare data, {id: score}
	foreach($name_ids as $id){
		$data[$id] = 0;
	}

	//write (intitial) score file with 0 scores each player
	$file = "data/score.json";
	$handle = fopen($file, "w");
	fwrite($handle, json_encode($data, JSON_PRETTY_PRINT));
	fclose($handle);

}

/*
 * for debugging
 */
function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>
