<?php

header("refresh:4");

require "Salem.php";

$salem = new Salem();

$scores = $salem->getScores();

$table = "<table>";

foreach($scores as $score){
	$table .= "<tr>";
		$table .= "<td>".$score["nick"]."</td>";
		$table .= "<td>".$score["score"]."</td>";
	$table .= "</tr>";
}

echo $table;
?>