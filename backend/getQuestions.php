<?php

if(file_exists("on")){
	//get from Jon
	$id = 2;

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

	//TODO: update $id.. maybe
	$handle = fopen("data/{$id}.txt", "w");
	fwrite($handle, json_encode($customQ, JSON_PRETTY_PRINT) );
	fclose($handle);

	//WRITE TO FILE JSONNNN WITH FILE NAME = ID OF CLIENT
	//dump($j);

	//get all values: array_values

} else{
	echo "game isn't running";
}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>