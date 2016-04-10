<?php

//set reply to json format
header('Content-Type: application/json');

//check if game is running
//and if ID is being sent
if(file_exists("on") && isset($_GET["id"]) ){

	$id = $_GET["id"];

	//if client has no data file = no questions generated yet
	//OR if the client has NOT answered the questions yet        //ALSO, this means, refreshing the page gets them a new set of questions = FEATURE OFC
	if( !file_exists("data/{$id}") || !hasAnswered($id) ){

		/* proceed with question generation + sending */

		//read the question file
		$file = "questions.txt";
		$handle = fopen($file, "r");
		$txt = fread($handle, filesize($file));
		fclose($handle);

		//questions and answers
		$q_and_a = explode(PHP_EOL, $txt);

		//questions for id
		$customQ = [];

		//making sure questions aren't used twice
		$uniques = [];

		//fetch 5 random questions
		while(sizeof($customQ) < 5){

			//random number
			$rand = rand(0, (sizeof($q_and_a)-1) );

			//even numbers contain questions
			if($rand % 2 == 0){

				//check if unique
				if(in_array($rand, $uniques)){

					//skip rest
					continue;

				}

				//question => answer
				$customQ[$q_and_a[$rand]] = $q_and_a[$rand+1];

				//mark used
				$uniques[] = $rand;
			} 
		}

		//dump($customQ);

		//write questions  (+ answer templates) to file named after id (pretty json-format)
		$handle = fopen("data/{$id}", "w");
		fwrite($handle, json_encode($customQ, JSON_PRETTY_PRINT) );
		fclose($handle);

		//return (only) questions to client
		echo json_encode(array_keys($customQ));

	} else{
		//TODO: jon wat reply do you want
		echo json_encode("DUDE CLIENT HAS ANSWERED IT ALREADY, REDIRECT HESHE PLZ");
	}

} else{
	echo json_encode("game isn't running OR no id provided");
}

/**
 * check if the client has answered the questions yet
 * if no: client is reconnecting or w/e
 *
 * @return      true: has answered, false: has NOT answered
 */
function hasAnswered($id){
	//if the file exists, check if they have been answered
	$file = file_get_contents("data/{$id}");

	//are placeholders left in the answer? = unanswered
	if( strpos($file, ":::answer:::") === false ) {

		return true;

	} else{

		return false;

	}
}


function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>