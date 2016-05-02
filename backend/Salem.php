<?php

/**
 * Handles the database
 */
class Salem{

	//db file
	private $db_file;

	//pdo object
	private $db;

	/*
	 * constructor
	 */
	function __construct(){

		$this->db_file = "db.sqlite3";

		try{

			//if db file doesn't exist
			if( !file_exists($this->db_file) ){

				//create db
				$this->db = new PDO("sqlite:{$this->db_file}");

				/* create tables */

				//client table
				$this->db->exec(
				"CREATE TABLE IF NOT EXISTS clients (
					id INTEGER PRIMARY KEY AUTOINCREMENT, 
					client_id INTEGER,
					nick TEXT,
					score INTEGER,
					requests INTEGER)"
				);

				//facts table
				$this->db->exec(
				"CREATE TABLE IF NOT EXISTS facts (
					id INTEGER PRIMARY KEY AUTOINCREMENT, 
					client_id INTEGER,
					fact TEXT)"
				);

				//cards table
				$this->db->exec(
				"CREATE TABLE IF NOT EXISTS cards (
					id INTEGER PRIMARY KEY AUTOINCREMENT, 
					owner_id INTEGER,
					fact_id INTEGER,
					fact TEXT,
					status INTEGER)"
				);

				//logging
				$this->db->exec(
				"CREATE TABLE IF NOT EXISTS logging (
					id INTEGER PRIMARY KEY AUTOINCREMENT, 
					client_id INTEGER,
					target_id INTEGER DEFAULT 0,
					event TEXT,
					time TEXT)"
				);	

			} else{

				//select db
				$this->db = new PDO("sqlite:{$this->db_file}");

			}

			//set error handling
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch(PDOException $e) {
			echo $e->getMessage();
		}

	}

	/**
	 * writes facts associated with a client id
	 * the old facts are deleted (replaced) each time
	 *
	 * @param $id		id used to set facts of
	 * @param $facts	array of facts
	 * @param $replace	states if replacing placeholders or not
	 * @param $answers	contains answers used to replace placeholders
	 */
	public function setFacts($id, $facts, $replace = false, $answers = NULL){

		if($replace){
			
			//initialize new array (overwrite)
			$facts = [];

			//read old facts (i.e. with placeholders)
			$old = $this->getFacts($id);

			//define placeholder
			$placeholder = ":::answer:::";

			//replace placeholder with answers
			for($i = 0; $i < sizeof($old); $i++){

				//find placeholder position
				$pos = strpos($old[$i], $placeholder);

				//replace it
				$facts[$i] = substr_replace($old[$i], $answers[$i], $pos, strlen($placeholder));

			}
		}

		try{

			//commit all changes in a collected transaction
			$this->db->beginTransaction();

			//delete old entries
			//prepare, bind, execute
			$sql = "DELETE FROM facts WHERE client_id = :id";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			//insert new entries
			$sql = "INSERT INTO facts (client_id, fact) VALUES (:id, :fact)";
			$stmt = $this->db->prepare($sql);

			//bind param
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);

			//insert each fact
			foreach($facts as $f){
				$stmt->bindParam(":fact", $f);

				$stmt->execute();
			}

			//commit transaction
			$this->db->commit();

		} catch(PDOException $e) {
			echo $e->getMessage();
		}

	}

	/**
	 * returns facts associated with given id
	 *
	 * @param $id	id used to fetch data from
	 *
	 * @return array 	[0]["fact"] = "blabla"
	 */
	public function getFacts($id){

		try{
			$sql = "SELECT fact FROM facts WHERE client_id = :id";			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_COLUMN);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * returns x amount of random facts associated with given id
	 *
	 * @param $id		id used to fetch data from
	 * @param $amount	how many facts to fetch
	 *
	 * @return array 	facts
	 */
	public function getRandomFacts($id, $amount){

		try{
			$sql = "SELECT fact FROM facts WHERE client_id = :id";			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
			$all_facts = $stmt->fetchAll(PDO::FETCH_COLUMN);

			$facts = [];

			//TODO: error handling when not enough facts: all_facts count < amount
			for($i = 0; $i < $amount; $i++){
				$rand = rand(0, (count($all_facts)-1) );
				$facts[$i] = $all_facts[$rand];

				//remove used fact from pool
				unset($all_facts[$rand]);

				//reindex
				$all_facts = array_values($all_facts);
			}

			return $facts;

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * check if client with given id has submitted answers yet
	 * i.e. placeholders were replaced with actual answers
	 *
	 * @param $id			id of client to check
	 *
	 * @return boolean      true: has answered, false: has NOT answered
	 */
	function hasAnswered($id){
		
		//fetch facts
		$facts = $this->getFacts($id);

		//if no facts found = first time request = has not yet answered
		if(count($facts) == 0){

			return false;

		} else{

			//if facts have been made, check if they contain placeholders
			if( strpos($facts[0], ":::answer:::") === false ){

				//if none found = has answered
				return true;

			} else{

				//if placeholder found = has not yet answered
				return false;

			}
		}
	}

	/**
	 * inserts id, associated nick and initialize score
	 *
	 * @param $id	id of client
	 * @param $nick	nick of client
	 */
	public function setNick($id, $nick){

		try{

			//insert new entry
			$sql = "INSERT INTO clients (client_id, nick, score, requests) VALUES (:id, :nick, 0, 0)";
			$stmt = $this->db->prepare($sql);

			//bind param
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->bindParam(":nick", $nick);

			//execute
			$stmt->execute();

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * returns nick of given id
	 *
	 * @param $id	id of nick to look up
	 *
	 * @return string 	nick
	 */
	public function getNick($id){

		try{
			$sql = "SELECT nick FROM clients WHERE client_id = :id";			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_COLUMN);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * returns number of players
	 *
	 * @return int		number
	 */
	public function countPlayers(){

		try{
			$sql = "SELECT COUNT(*) FROM clients"; 
			//$stmt = $this->db->prepare($sql);
			$stmt = $this->db->query($sql);

			return $stmt->fetchColumn();

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * checks if client with given id owns any cards
	 *
	 * @param $id	id to check
	 *
	 * @return boolean 	true if owns, false if none
	 */
	public function hasCards($id){

		try{
			$sql = "SELECT * FROM cards WHERE owner_id = :id";			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			if(count( $stmt->fetchAll(PDO::FETCH_ASSOC) ) > 0){
				//change to fetch columN?
				return true;
			} else{
				return false;
			}

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * generates x amount of cards
	 * if not enough (unique) data, it will return empty
	 *
	 * @param $id		id of requestor i.e. making sure not to pick own fact
	 * @param $amount	how many cards
	 * @param $f_amount	how many facts per card
	 * @param $more		if true, must consider already existing cards
	 *
	 * @return Cards array (prepared for json data format)
	 */
	public function makeCards($id, $amount, $f_amount, $more = false){

		try{
			
			$sql = "";

			if($more){

				//fetch ids of every other player except own
				//and id must not be in cards (fact_id) where client is the owner
				//(=received the card already)
				$sql = "SELECT client_id FROM clients 
							WHERE client_id IS NOT :id
							AND client_id NOT IN 
								(SELECT DISTINCT fact_id FROM cards 
									WHERE owner_id = :id)";

			} else{

				//fetch ids of every other player except own
				$sql = "SELECT client_id FROM clients WHERE client_id != :id";				
			}
			

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			//pool of available players (ids)
			$players = $stmt->fetchAll(PDO::FETCH_COLUMN);

			/* generate cards */

			//to be send to client
			$cards = [];

			for($i = 0; $i < $amount; $i++){

				//pick random id
				$rand = rand(0, (count($players)-1) );

				/* generate a card */

				//preparing json structure
				$card["id"] = $players[$rand];

				$card["nick"] = $this->getNick($players[$rand]);

				//preparing answers for json
				$facts = $this->getRandomFacts($players[$rand], $f_amount);
				$answers = [];

				for($z = 0; $z < count($facts); $z++){
					$answers["a".$z] = $facts[$z];
				}

				$card["answers"] = $answers;

				$card["status"] = "unsolved"; //default false

				//add card to cards
				$cards[] = $card;

				//remove from pool and reindex
				unset($players[$rand]);
				$players = array_values($players);
			}

			//save cards to db
			$this->setCards($id, $cards);

			//return cards
			return $this->getCards($id);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * saves generated cards associated with owner_id into db
	 * updates request counter, i.e. when new cards have been made +1
	 *
	 * @param $owner_id		id of client who owns the card
	 * @param $cards		contains the cards owner_id client has to look for
	 */
	public function setCards($owner_id, $cards){
		
		try{

			//commit all changes in a collected transaction
			$this->db->beginTransaction();

			$sql = "INSERT INTO cards (owner_id, fact_id, fact, status)
						VALUES (:owner_id, :fact_id, :fact, :status)";
			$stmt = $this->db->prepare($sql);

			for($i = 0; $i < count($cards); $i++){

				//bind params
				$fact_id = $cards[$i]["id"];
				$facts = $cards[$i]["answers"];
				// strcmp($cards[$i]["status"], "unsolved") === false ? 1 : 0;
				//setCards used for adding "fresh" cards, thus default should be 0 anyhow
				$status = 0; //false

				$stmt->bindParam(":owner_id", $owner_id, PDO::PARAM_INT);
				$stmt->bindParam(":fact_id", $fact_id, PDO::PARAM_INT);
				$stmt->bindParam(":status", $status, PDO::PARAM_INT);

				foreach($facts as $f){
					$stmt->bindParam(":fact", $f);
					$stmt->execute();
				}

			}

			//update requests counter
			$sql = "UPDATE clients SET requests = requests+1 WHERE client_id = :id";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $owner_id, PDO::PARAM_INT);
			$stmt->execute();

			//commit transaction
			$this->db->commit();

		} catch(PDOException $e) {
			echo $e->getMessage();

		}

	}

	/**
	 * fetches existing cards owned by the given id
	 *
	 *	@param $id		fetches cards of given id
	 *
	 *	@return cards	in a client-readable format
	 */
	public function getCards($id){
		try{

			//will contain all the cards related to id
			$cards = [];

			//first get all fact_ids which are related to owner_id
			$sql = "SELECT DISTINCT fact_id FROM cards WHERE owner_id = :id";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			//all fact_ids; 1 fact_id basically represents 1 card
			$fact_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

			//for each fact_id, get the corresponding facts
			foreach($fact_ids as $f_id){

				//get all facts (answers) from given fact_id
				$sql = "SELECT fact, status FROM cards WHERE owner_id = :id AND fact_id = :f_id";
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(":id", $id, PDO::PARAM_INT);
				$stmt->bindParam(":f_id", $f_id, PDO::PARAM_INT);
				$stmt->execute();

				//prepare answers for json
				$facts = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
				$answers = [];

				for($i = 0; $i < count($facts); $i++){
					$answers["a".$i] = $facts[$i];
				}

				//prepare status for json
				$stmt->execute();
				$status = $stmt->fetch(PDO::FETCH_ASSOC)["status"];

				//structuring json
				$card["id"] = $f_id;
				$card["nick"] = $this->getNick($f_id);
				$card["answers"] = $answers;
				$card["status"] = intval($status) == 0 ? "unsolved" : "solved";

				//add card to cards
				$cards[] = $card;
			}

			//return json ready cards
			return $cards;

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}

	}

	/**
	 *	returns the number of requests which have been made
	 */
	public function getRequests($id){
		$sql = "SELECT requests FROM clients WHERE client_id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);		
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_COLUMN);
	}

	//for debugging
	public function getAllCards(){
		try{
			$sql = "SELECT * FROM cards";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}

	}

	/**
	 *	checks if the client with the given id has unsolved cards
	 *
	 * 	@param $id		id of client to check
	 *
	 *	@return boolean	true on unsolved, false on solved all
	 */
	public function hasUnsolved($id){

		try{
			$sql = "SELECT status FROM cards WHERE owner_id = :id AND status = 0";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			if( count($stmt->fetchAll(PDO::FETCH_COLUMN))  > 0){
				return true;
			} else{
				return false;
			}

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * updates the score of given id and updates solved status
	 *
	 * @param id			id of client who scored a point
	 * @param target_id		id of client who was identified
	 * @param points		how many points to add, default 1
	 *
	 * @return data			returns request count + if client has unsolved cards
	 */
	public function setScore($id, $target_id, $points = 1){

		try{

			//update score
			$sql = "UPDATE clients SET score = score+:points WHERE client_id = :id";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":points", $points, PDO::PARAM_INT);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			//update solved
			$sql = "UPDATE cards SET status = 1 WHERE owner_id = :id AND fact_id = :t_id";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->bindParam(":t_id", $target_id, PDO::PARAM_INT);
			$stmt->execute();

			//count how many requests were made ()
			//$data["count"] = intval( $this->getRequests($id) );

			//has unsolved? 1 = yes, 0 = false
			$data["unsolved"] = $this->hasUnsolved($id) ? 1 : 0;

			return $data;

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	/**
	 * get scores + nick, order descending
	 * 
	 * @return json		nick and its score
	 */
	public function getScores(){
		
		try{

			//read scores
			$sql = "SELECT nick, score FROM clients ORDER BY score DESC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}

	}

	/**
	 * deletes all data
	 */
	public function reset(){
		$this->db->prepare("DELETE FROM clients")->execute();
		$this->db->prepare("DELETE FROM cards")->execute();
		$this->db->prepare("DELETE FROM facts")->execute();
	}

	/**
	 * logs events
	 *
	 * @param id		id of client triggering event
	 * @param event		description of event (e.g. success, fail)
	 * @param t_id		optional: target id if success
	 */
	public function log($id, $event, $t_id = -1){
		
		date_default_timezone_set("Europe/Stockholm"); 

		//add time of server start
		$time = date("H:i:s");

		try{

			//insert new entry
			$sql = "INSERT INTO logging (client_id, target_id, event, time) VALUES (:id, :t_id, :event, :time)";
			$stmt = $this->db->prepare($sql);

			//bind param
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->bindParam(":t_id", $t_id, PDO::PARAM_INT);
			$stmt->bindParam(":event", $event);
			$stmt->bindParam(":time", $time);

			//execute
			$stmt->execute();

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}

	}

	/**
	 * returns the log
	 * 
	 * @param toFile	saves log to file if true
	 * @return html table
	 */
	public function getLog($toFile = false){
		
		try{

			//read scores
			$sql = "SELECT client_id, target_id, event, time FROM logging ORDER BY client_id ASC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$table = "<table>
						<tr>
							<th>Player</th>
							<th>Target</th>
							<th>Type</th>
							<th>Time</th>
						</tr>
					";

			foreach($data as $d){
				$table .= "<tr>";
					$table .= "<td>".$d["client_id"]."</td>";
					$table .= "<td>".$d["target_id"]."</td>";
					$table .= "<td>".$d["event"]."</td>";
					$table .= "<td>".$d["time"]."</td>";
				$table .= "</tr>";
			}

			$table .= "</table>";

			return $table;

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}

	}

//////////////////////////////////
//FUNCTIONS FOR TESTING PURPOSES//
//////////////////////////////////

/*
	public function delete(){

		try{
			$sql = "DELETE FROM cards";			
			$stmt = $this->db->prepare($sql);
			$stmt->execute();

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	// returns all nick
	public function getNicks(){

		try{
			$sql = "SELECT client_id, nick FROM clients";			
			$stmt = $this->db->prepare($sql);
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}
	}

	public function test($id){
		try{
			$sql = "SELECT client_id FROM clients 
							WHERE client_id != :id
							AND client_id NOT IN 
								(SELECT DISTINCT fact_id FROM cards WHERE owner_id = :id)";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_COLUMN);

		} catch(PDOException $e) {
			
			echo $e->getMessage();

		}

	}
*/

}

?>