<?php

//set reply to json format
header('Content-Type: application/json');

require "Salem.php";
$salem = new Salem();

//read pool of cards, save who has gotten what

//TODO: mark cards as solved? (for reconnection cases)

//check if ID is being sent
if( isset($_GET["id"]) ){

	$id = $_GET["id"];

	//read game settings
	$file = json_decode( file_get_contents("server.json") );
	$card_qty = $file->cards_per_player;
	$fact_qty = $file->facts_per_card;

	//cards to send to client
	$cards = [];

	//check if person has cards
	if( !$salem->hasCards($id) ){

		/* make new cards */

		//generate cards + save to db
		$cards = $salem->makeCards($id, $card_qty, $fact_qty);

	//if has cards, check if there are any unsolved cards (= reconnect)
	} elseif( $salem->hasUnsolved($id) ){
		
		/* fetch existing cards */

		$cards = $salem->getCards($id);


	//if every card is solved, generate new ones
	} else{

		/* make new cards considering already existing ones */

		$cards = $salem->makeCards($id, $card_qty, $fact_qty, true);

	}

	//send the cards
	echo json_encode($cards);

}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>