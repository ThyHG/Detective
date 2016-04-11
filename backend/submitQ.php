<?php

//set reply to json format
header('Content-Type: application/json');

//getting id and answers
$answers = ["answer1", "answer2", "answer3", "answer4", "answer5"];


//check if the game is running
if( file_exists("on") ){
	
	//
	if( isset($_GET["id"])  && file_exists("data/{$id}") ){

		$id = $_GET["id"];

		//read data file of client
		$data = file_get_contents("data/{$id}");

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
		$handle = fopen("data/{$id}", "w");
		fwrite($handle, $data);
		fclose($handle);

		//tell client about success
		echo json_encode("positive msg blargh");	

	} else{
		echo json_encode("no id provided OR no data found, i.e. no questionnaire requested");
	}
	

} else{
	echo json_encode("game isn't running dude");
}

function dump($array) {
	echo htmlentities(print_r($array, 1));
}

?>