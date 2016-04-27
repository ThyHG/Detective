<?php

header("refresh:4");

require "Salem.php";

$salem = new Salem();

$scores = $salem->getScores();

?>

<html>
<head>
	<title>Highscore</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<meta charset="utf-8"/>
  	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,600' rel='stylesheet' type='text/css'>
  	<link href="../frontend/css/normalize.css" type="text/css" rel="stylesheet">
  	<link href="../frontend/css/skeleton.css" type="text/css" rel="stylesheet">
  	<link href="css/scoreboard.css" type="text/css" rel="stylesheet">

  	<style>

	  	td{ font-size: 2.5em; }
		h3{ text-align: center; }
		
  	</style>
</head>
<body>

<table class="container">
	<thead>
		<tr>
			<th colspan="2"><h3>Highscore</h3></th>
		</tr>
	</thead>
	<tbody>
<?php

foreach($scores as $score){
	echo "<tr>";
	echo "<td>".$score["nick"]."</td>";
	echo "<td>".$score["score"]."</td>";
	echo "</tr>";
}

?>
	</tbody>
</table>
</body>
</html>