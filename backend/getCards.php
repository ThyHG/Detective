<?php

//set reply to json format
header('Content-Type: application/json');

//read pool of cards, save who has gotten what

//TODO: mark cards as solved? (for reconnection cases)

//check if ID is being sent
if( isset($_GET["id"]) ){

	$id = $_GET["id"];

	//check if person requests more or is a first time requestor
	//if file exists, person is requesting for more = true
	$more = file_exists("data/save/{$id}");

	//TODO: decide on quanitity / set settings in admin.php
	$card_qty = 2;
	$fact_qty = 2;

	//all ppl fact data
	$ppl_pool = glob("data/facts/*");

	//to be send to client
	$cards = [];

	//tracking which IDs were used to make cards
	$track_ids = [];

	//choose x cards
	while( sizeof($cards) < $card_qty ){

		//random number between 0 and size of array
		$rand_c = rand(0, (sizeof($ppl_pool)-1) );

		//get filename only
		$file_id = basename( $ppl_pool[$rand_c] );

		//echo $i." ".$file_id."<br>";
		
		//if data-file choosen does not belong to the requestor
		if($file_id != $id){
			
			//if requested for more, check previous data first to avoid duplicates
			if( $more && isDuplicate($file_id, $id) ){

				//skip rest
				continue;

			}

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

			//tracking id
			$track_ids[] = $file_id;

			//remove from pool to prevent duplicate
			unset($ppl_pool[$rand_c]);

			//update array
			$ppl_pool = array_values($ppl_pool);
		}
	}

	//save data => which cards (id) did the requestor get
	//automatically appends if file exists, else creates the file
	$handle = fopen("data/save/{$id}", "a");
	fwrite($handle, implode(PHP_EOL, $track_ids) );
	fwrite($handle, PHP_EOL );
	fclose($handle);

	//send the cards
	echo json_encode($cards);


	//error handling when not enough participants
	//error handling when not enough facts of 1 person
	//assumption this wont happen?

}

//point handling php: mark cards as done?
/**
 *	Checks if ID was already used for requestor ID
 *
 *	@param id		id to check if it has been used
 *	@param r_id		id of requestor, used to read its save file
 */
function isDuplicate($id, $r_id){

	//read file
	$file = file_get_contents("data/save/{$r_id}");
	$save_file = explode(PHP_EOL, $file);

	//check if its inside
	return in_array($id, $save_file);
}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>