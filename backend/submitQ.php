<?php

//set reply to json format
header('Content-Type: application/json');

require "Salem.php";
$salem = new Salem();

//check if data was sent
if( isset($_POST["id"]) && isset($_POST["answers"]) ){ 

	$id = $_POST["id"];
	$answers = $_POST["answers"];
	$nick = $_POST["nick"];

	//defining placeholder
	$placeholder = ":::answer:::";

	//answers with placeholder
	$salem->setFacts($id, NULL, true, $answers);

	//insert nick+id into name table
	$salem->setNick($id, $nick);

	//tell client about success
	echo json_encode("it goat saved");

} else{
	echo json_encode("no id provided OR no data found, i.e. no questionnaire requested");
}

function dump($array) {
	echo htmlentities(print_r($array, 1));
}

?>