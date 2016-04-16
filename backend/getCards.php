<?php

//set reply to json format
header('Content-Type: application/json');

//read pool of cards, save who has gotten what

//check if ID is being sent
if( isset($_GET["id"]) ){

	$id = $_GET["id"];

	//TODO: decide on quanitity / set settings in admin.php
	$card_qty = 2;
	$fact_qty = 2;

	$ppl_pool = glob("data/*");

	$cards = [];

	//choose x cards
	//USE COUNTER INSTEAD OF FOR?
	for($i = 0; sizeof($cards) < $card_qty; $i++){

		//random number between 0 and size of array
		$rand_c = rand(0, (sizeof($ppl_pool)-1) );

		//get filename only
		$file_id = basename( $ppl_pool[$rand_c] );

		//echo $i." ".$file_id."<br>";
		
		//if data-file choosen does not belong to the requestor
		if($file_id != $id){
	
			//contains all facts of 1 person
			$facts = [];

			//read data file
			$file = $ppl_pool[$rand_c];
			$handle = fopen($file, "r");
			$txt = fread($handle, filesize($file));
			fclose($handle);

			$fact_pool = explode(PHP_EOL, $txt);
			
			//choose x facts
			for($x = 0; $x < $fact_qty; $x++){
				//random number between 0 and size of array
				$rand_f = rand(0, (sizeof($fact_pool)-1) );

				//a1 => answerbla
				$facts["a".$x] = $fact_pool[$rand_f];	

				//remove to prevent duplicate
				unset($fact_pool[$rand_f]);

				//update array
				$fact_pool = array_values($fact_pool);
			}

			//save everything in the container
			$container["id"] = $file_id;
			$container["answers"] = $facts;

			//add to existing cards
			$cards[] = $container;

			//remove from pool to prevent duplicate
			unset($ppl_pool[$rand_c]);

			//update array
			$ppl_pool = array_values($ppl_pool);
		}
	}

	//echo json_encode($cards, JSON_PRETTY_PRINT);
	echo json_encode($cards);


	//error handling when not enough participants
	//error handling when not enough facts of 1 person
	//assumption this wont happen?

}

//point handling php: mark cards as done?

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>