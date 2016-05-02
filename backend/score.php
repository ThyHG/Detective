<?php

require "Salem.php";
$salem = new Salem();


//check if ID is being sent 
if( isset($_GET["id"]) ){

	$id = $_GET["id"];

	//check if it's a fail
	if( isset($_GET["fail"]) ){

		//log failure
		$salem->log($id, "fail");

	//check if target id is being sent with (= scanned person)
	} elseif( isset($_GET["t_id"]) ){
	
		$t_id = $_GET["t_id"];

		//update score
		$data = $salem->setScore($id, $t_id);

		//log success
		$salem->log($id, "success", $t_id);

		//return data to client, i.e. count + if has unsolved
		echo json_encode($data);
	}

} else{
	echo "missing id/s";
}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}
?>