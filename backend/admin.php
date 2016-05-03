<?php

header("Refresh:3; url=admin.php");

require "Salem.php";
$salem = new Salem();

//create folder structure  if it doesn't exist
if( !file_exists("log") ){
	mkdir("log");
}

if( !file_exists("server.json") ){
    
	//create file
    touch("server.json");

    //initalize default server settings
    $status = "offline";
    $questions_per_player = 5;
    $cards_per_player = 2;
    $facts_per_card = 2;

    $data = [
    	"status" => $status,
    	"questions_per_player" => $questions_per_player,
    	"cards_per_player" => $cards_per_player,
    	"facts_per_card" => $facts_per_card,
    	"start_time" => "not started"
    ];

    //save to file
    file_put_contents( "server.json", json_encode($data, JSON_PRETTY_PRINT) );
}

//read server file
$server = json_decode( file_get_contents("server.json") );

//game status, true = running, false = offline
$game_running = strcmp($server->status, "online") == 0 ? true : false;

//player count
$player_count = $salem->countPlayers();

//start time
$start_time = $server->start_time;

//for msgs
$notice = "";

if(isset($_GET["start"])){

	if(!$game_running){

		//change server info to online
		$server->status = "online";

		date_default_timezone_set("Europe/Stockholm"); 

		//add time of server start
		$server->start_time = date("d. F H:i:s");

		//save changes to server file
		file_put_contents( "server.json", json_encode($server, JSON_PRETTY_PRINT) );

		//update notice
		$notice = "Started";

	} else{

		//update notice
		$notice = "Game already running!";

	}
	
} elseif( isset($_GET["log"]) ){

	$salem->saveLog($start_time);
	$salem->saveScores();

} elseif( isset($_GET["logint"]) ){

	$salem->saveInteractionLog();

} elseif(isset($_GET["stop"]) && $game_running){

	//save log
	$salem->saveLog($start_time);
	$salem->saveScores();

	//drop all data
	$salem->reset();

	//change server info to offline
	$server->status = "offline";
	$server->start_time = "not started";

	//SAVE LOG AND EVERYTHING GAME START TIME TO FILE

	//save changes to server file
	file_put_contents( "server.json", json_encode($server, JSON_PRETTY_PRINT) );

	//update notice
	$notice = "Game stopped!";
}

//front end
include("admin_template.php");

/*
 * for debugging
 */
function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>
