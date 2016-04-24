<?php

require "Salem.php";
$salem = new Salem();

//check if ID is being sent (and target id = scanned person)
if( isset($_GET["id"]) && isset($_GET["t_id"]) ){
	
	$id = $_GET["id"];
	$t_id = $_GET["t_id"];

	//update score
	$salem->setScore($id, $t_id);

} else{
	echo "missing id/s";
}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}
?>