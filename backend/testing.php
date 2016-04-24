<?php

require "Salem.php";

$id = $_GET["id"];
$salem = new Salem();

$data = $salem->getCards($id);

dump(json_encode($data, JSON_PRETTY_PRINT));

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

?>