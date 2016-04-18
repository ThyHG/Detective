<?php

//set reply to json format
header('Content-Type: application/json');

//check if the game is running
//if( file_exists("j.on") ){
//if( isset($_POST["id"])  && isset($_POST["answers"]) && file_exists("data/{$id}") ){

//check if data was sent
if( isset($_POST["id"]) && isset($_POST["answers"]) ){ 

	$id = $_POST["id"];
	$answers = $_POST["answers"];

	//read data file of client
	$data = file_get_contents("data/facts/{$id}");

	//defining placeholder
	$placeholder = ":::answer:::";

	//replacing each placeholder with the actual answer
	for($i = 0; $i < sizeof($answers); $i++){

		//find placeholder position
		$pos = strpos($data, $placeholder);

		//replace it
		$data = substr_replace($data, $answers[$i], $pos, strlen($placeholder));

	}

	//dump($data);

	//write changes to file
	$handle = fopen("data/facts/{$id}", "w");
	fwrite($handle, $data);
	fclose($handle);

	//tell client about success
	echo json_encode("it goat saved");

} else{
	echo json_encode("no id provided OR no data found, i.e. no questionnaire requested");
}
	

function dump($array) {
	echo htmlentities(print_r($array, 1));
}

?>