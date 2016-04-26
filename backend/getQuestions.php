<?php

//set reply to json format
header('Content-Type: application/json');

require "Salem.php";
$salem = new Salem();

//questions per player
$qpp = json_decode( file_get_contents("server.json") )->questions_per_player; 

//check if ID is being sent
if( isset($_GET["id"]) ){

	$id = $_GET["id"];

	//check if the client has submitted answers yet
	//client receives (new) questions as long as answers arent submitted
	if( !$salem->hasAnswered($id) ){

		/* proceed with question generation + sending */

		//read the question file
		$file = "questions.txt";
		$handle = fopen($file, "r");
		$txt = fread($handle, filesize($file));
		fclose($handle);

		//questions and answers
		$q_and_a = explode(PHP_EOL, $txt);

		//questions for requestor (id)
		$customQ = [];
		$index = 1;

		//data to write to file
		$toSave = [];
		$i = 0;
		//fetch x random questions
		while(sizeof($customQ) < $qpp){

			//random number between 0 and size of array
			$rand = rand(0, (sizeof($q_and_a)-1) );

			//even numbers contain questions
			if($rand % 2 == 0){

				//save q1 => question
				$customQ["q".$index] = $q_and_a[$rand];
				$index++;

				//prepare data for db
				$toSave[] = $q_and_a[$rand+1];

				//remove from question pool to avoid duplicates
				unset($q_and_a[$rand]);
				unset($q_and_a[$rand+1]);

				//re-index array
				$q_and_a = array_values($q_and_a);
			}
		}

		//save facts (= answers with placeholders)
		$salem->setFacts($id, $toSave);

		//dump($toSave);

		//send questions to client
		echo json_encode($customQ);

	} else{
		echo json_encode("DUDE CLIENT HAS ANSWERED IT ALREADY");
	}

} else{
	echo json_encode("no id provided");
}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>