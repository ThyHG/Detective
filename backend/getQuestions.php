<?php

//set reply to json format
header('Content-Type: application/json');


//if(file_exists("on") && isset($_GET["id"]) ){ //check if game is running

//check if ID is being sent
if( isset($_GET["id"]) ){

	$id = $_GET["id"];

	//if client has no data file = no questions generated yet
	//OR if the client has NOT answered the questions yet        //ALSO, this means, refreshing the page gets them a new set of questions = FEATURE OFC

	if( !file_exists("data/facts/{$id}") || !hasAnswered($id) ){

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
		$toFile = [];

		//fetch 5 random questions
		while(sizeof($customQ) < 5){

			//random number between 0 and size of array
			$rand = rand(0, (sizeof($q_and_a)-1) );

			//even numbers contain questions
			if($rand % 2 == 0){

				//save q1 => question
				$customQ["q".$index] = $q_and_a[$rand];
				$index++;

				//prepare data for data-file
				$toFile[] = $q_and_a[$rand+1];

				//remove from question pool to avoid duplicates
				unset($q_and_a[$rand]);
				unset($q_and_a[$rand+1]);

				//re-index array
				$q_and_a = array_values($q_and_a);
			} 
		}

		//write questions  (+ answer templates) to file named after id
		$handle = fopen("data/facts/{$id}", "w");
		fwrite($handle, implode(PHP_EOL, $toFile) );
		fclose($handle);
		
		//return questions to client
		echo json_encode($customQ);

	} else{
		//TODO: jon wat reply do you want
		echo json_encode("DUDE CLIENT HAS ANSWERED IT ALREADY, REDIRECT HESHE PLZ");
	}

} else{
	echo json_encode("no id provided");
}

/**
 * check if the client has answered the questions yet
 * if no: client is reconnecting or w/e
 *
 * @return      true: has answered, false: has NOT answered
 */
function hasAnswered($id){
	//if the file exists, check if they have been answered
	$file = file_get_contents("data/facts/{$id}");

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