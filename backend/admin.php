<?php

//game status, true = running, false = offline
$game_running = file_exists("on");

if(isset($_GET["start"])){

	if(!$game_running){

		//create file to indicate game state
		touch("on");
		
	} else{

		echo "Game already running";

	}
	
} elseif(isset($_GET["stop"]) && $game_running){
	$game_running = false;

	//delete "on" file
	unlink("on");

	//delete game data i.e. client questions
	array_map("unlink", glob("data/*.*"));

}
/*
if(isset($_GET["id"]) && $settings->status == "on"){
	
} else{
		return "Access denied";
}

if($settings->status == "off"){
	return "Game isn't running";
}
*/

?>
<html>
	<table>
		<tr>
			<td>Game status</td>
			<td><?php echo $game_running ? "online" : "offline"; ?></td>
		</tr>
		<tr>
			<td>Players</td>
			<td><?php  ?></td>
		</tr>
		<tr>
			<td><a href="admin.php?start">Start game</td>
			<td><a href="admin.php?stop">Stop game</td>
		</tr>
	</table>
</html>

<?php
/*
 * for debugging
 */
function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}
?>
