<?php

//check if ID is being sent
if( isset($_GET["id"]) ){
	
	$id = "".$_GET["id"];

	//file for locking
	$file = "data/score.json";

	//c mode = read, write without truncating file
	$handle = fopen($file, "c+");

	//lock the file to prevent simultanous access
	if(flock($handle, LOCK_EX)){
		
		//read score file
		$file = fread($handle, filesize($file));
		rewind($handle);
		$json = json_decode($file);
		
		//update score
		foreach($json as $ele){
			if($ele->id == $id){
				$ele->score += 1;
				break;
			}
		}

		//update score
		//$json->{$id} += 1;
		//dump($json);

		//clear file
		ftruncate($handle, 0);

		//write changes to file
		fwrite($handle, json_encode($json, JSON_PRETTY_PRINT));
		
		//flush output (=writes changes now instead of buffering)
		fflush($handle);

		//unlock file
		flock($handle, LOCK_UN);
	} 

	//close file handle
	fclose($handle);

} else{
	echo "herp no ID given";
}

function dump($array) {
	echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}
?>