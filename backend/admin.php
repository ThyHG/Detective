<?php

//create folder structure  if it doesn't exist
if( !file_exists("data") ){
    mkdir("data");
    mkdir("data".DIRECTORY_SEPARATOR."facts");
    mkdir("data".DIRECTORY_SEPARATOR."save");
}

//game status, true = running, false = offline
$game_running = file_get_contents("j.on") == "online" ? true : false;

$player_count = count(glob("data/facts/*"));

//for msgs
$notice = "";

if(isset($_GET["start"])){

	if(!$game_running){

		//create file to indicate game state
		file_put_contents("j.on", "online");

		//create score file for future scoring
		startScore();

		$notice = "Started!";

	} else{

		$notice = "Game already running!";

	}
	
} elseif(isset($_GET["stop"]) && $game_running){

	$game_running = false;

	//delete "on" file
	file_put_contents("j.on", "offline");

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

/**
 * reads current players and creates a score file
 */
function startScore(){

	//read player ids (= participatns)
	$name_ids = array_map("basename", glob("data/facts/*"));

	//data to write
	$data = [];

	//prepare data, [{id:" ", score:0}, {...}]
	foreach($name_ids as $id){
		$temp = [];

		$temp["id"] = $id;
		$temp["score"] = 0;
		
		$data[] = $temp;
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
