<?php
error_reporting(E_ALL);

/**
 * mysql database wrapper class for start rating
 *@package AJAXRATING
 *@access public
 *@abstract Database
 *@author Sudhir Chauhan (chauhansudhir@gmail.com, sudhir_sonu@yahoo.com)
 *@return string
 *@version 1.0.0;
 */
global $sql_bdd,$sql_pass,$sql_login,$sql_serveur;
//database host name or IP
define('DATABASEHOST',$sql_serveur);
//database user name
define('DATABASEUSERNAME',$sql_login);
// database user password
define('DATABASEPASSWORD',$sql_pass);
//database name
define('DATABASENAME',$sql_bdd);
// single start width
define('STARWIDTH',20);
// total number of starts
// NOTE: This is not working complately. If you want to increase of decrease stars you have to modify css also
define('TOTALSTARS',5);

//mysql extention must be loaded
// Abstact class for rating
abstract class Database {
	public $databaseHost = DATABASEHOST;
	public $databaseUser = DATABASEUSERNAME;
	public $databasePassword = DATABASEPASSWORD;
	public $databaseName = DATABASENAME;
	public $connection = null; //  database connection
	protected $recordsSelected = 0;
	protected $recordsUpdated = 0;
	
	
	protected function connect() {
	
		$this->connection = mysql_connect($this->databaseHost, $this->databaseUser, $this->databasePassword);
		if (!$this->connection) {
			$this->connection = null;
   			trigger_error(mysql_error());
		}
		mysql_select_db($this->databaseName);
	}
	
	protected function querySelect($query) {
		
		if (strlen(trim($query)) < 0 ) {
			trigger_error("Database encountered empty query string in querySelect function", E_USER_ERROR);
			return false;
		}
		
		if ($this->connection === null ) {
			$this->connect();
		}

		$result = mysql_query($query, $this->connection) or die(mysql_error());
		if (!$result) {
			return array();
		}

		$this->recordsSelected = mysql_num_rows($result);
		return $this->getData($result); 
	}
	
	protected function queryExecute($query) {
		
		if (strlen(trim($query)) < 0 ) {
			trigger_error("Database encountered empty query string in queryExecute function", E_ERROR);
		}
		
		if ($this->connection === null ) {
			$this->connect();
		}
		
		$res = mysql_query($query, $this->connection);
		if($res) {
			$this->recordsUpdated = mysql_affected_rows($this->connection);
		}
	}
	
	protected function getData($result) {
		$data = array();
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			foreach ($row as $key => $value) {
				$data[$i][$key] = stripslashes($value);		
			}
			$i++;
		}
		return $data;
	}	
	
}

/**
 * Ajax start rating
 *@package AJAXRATING
 *@access public
 *@abstract Database
 *@author Sudhir Chauhan (chauhansudhir@gmail.com, sudhir_sonu@yahoo.com)
 *@return string
 *@version 1.0.0;
 * TODO:: 
 *	for optimisation we can get all values from rating table in the constructer
 * 	and avoid multiple select queries.
 * 	IP base check: restrict users how have already voted. (cookie or ip address0
 */
class RatingManager extends Database {
	public static $instance = 0;
	private $cipher;
	
	public function __construct() {

	}
	
	public static function getInstance() {
		
		if (self::$instance == 0 ) {
			self::$instance = new RatingManager();
		}
		return self::$instance;
	}
	
	/**
	 * Draw stars
	 * TODO: add IP restriction check to avoid person to vote again
	 * COOKIE can also be used for this
	 */
	public function drawStars($id) {
		$cookie=array();
		if (!is_numeric($id)) {
			trigger_error("RatingManager encountered problem in drawStars() parameter. Passed parameter must be numeric.");
			exit;
		}
		if(isset($_COOKIE['rating']))
			$cookie=unserialize(base64_decode($_COOKIE['rating']));
		$query = "SELECT total_votes, total_value, used_ips FROM ratings WHERE id='".$id."'";
		$result = $this->querySelect($query);
		
		if (is_array($result) && count($result) > 0 ) {
			$totalVotes = $result[0]['total_votes'];	//how many total votes
			$totalValues = $result[0]['total_value'];  //total number of rating added together and stored
			$oldIPs = unserialize($result[0]['used_ips']);
		}
		else {
			$totalVotes = 1;
			$totalValues = 0;
			$oldIPs = Array();		
		}
		
		$currentRating = @number_format($totalValues / $totalVotes, 2) * STARWIDTH ;
		
		// allow single submit for userid
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		
		//$ipAddress = $_SESSION['USERID'];
		
		if (in_array($ipAddress, $oldIPs) || in_array($id,$cookie)){
			return $this->drawPrintedStars($currentRating, $id,false,$totalVotes);
		}
		
		$ratingString = '<div id="unit_long'.$id.'">
							<ul class="unit-rating">
							<li class="current-rating" style="width:'.$currentRating.'px;">Currently '.$currentRating.'; ?>/ TOTALSTARS </li>';
		
			for ($ncount = 1; $ncount <= TOTALSTARS; $ncount++) { 
				$ratingString .= '<li><a href="javascript:void(0);" title="'.$ncount.' out of '.TOTALSTARS.'" class="r'.$ncount.'-unit" onclick="javascript:sndRequest(\''.$ncount.'\','.$id.',\''.$ipAddress.'\')">'.$ncount.'</a></li>';
			}

			$ncount = 0; // resets the count
			
			$ratingString .= '</ul>Côte : '.($currentRating/STARWIDTH).'/5<br />
							avec '.$totalVotes.' vote(s)</div>';
			
		return $ratingString;	
		
	}
	
	/**
	 * update votes for id
	 */
	public function updateVote($numberofVotesSent, $voteForWitchId, $userIPAddress) 
	{
		$numberOfVotes = $numberofVotesSent;
		$voteForID = $voteForWitchId; 
		$ipAddress = $userIPAddress;
		$cookie=array();	
	
		$query = "SELECT total_votes, total_value, used_ips FROM ratings WHERE id='".$voteForID."'";
		$result = $this->querySelect($query);
		
		$sum = 0;
		$oldIPAddress = Array();
		if (is_array($result) && count($result) > 0) {
			$totalVotes = $result[0]['total_votes'];			//how many votes total
			$totalValues = $result[0]['total_value'];  //total number of rating added together and stored
			$oldIPAddress = unserialize($result[0]['used_ips']);
			$sum = $numberOfVotes + $totalValues;		// add together current vote value and the total vote value
		}
		if(isset($_COOKIE['rating']))
			$cookie=unserialize(base64_decode($_COOKIE['rating']));
		

		if (in_array($ipAddress, $oldIPAddress) && in_array($cookie,$voteForID) ) {		
			$currentRating = @number_format($totalValues / $totalVotes, 2) * STARWIDTH ;
			echo $this->drawPrintedStars($currentRating,  $voteForID, true);
			return; 
		}
		else
		{
			array_push($cookie, $voteForID);
			setcookie('rating',base64_encode(serialize($cookie)),(time()+365*24*3600),'/');	
		}
		
		
		if ($sum == 0) {
			$addedVotes = 1; //checking to see if the first vote has been voted
			$sum = $numberOfVotes ;
		}
		else {
			$addedVotes = $totalVotes + 1;//increment the current number of votes
		}

		if (is_array($oldIPAddress)) {
			array_push($oldIPAddress, $ipAddress);//if it is an array i.e. already has entries the push in another value
		}
		else {
			$oldIPAddress = array($ipAddress);//for the first entry
		}
		
		$serializeIPList = serialize($oldIPAddress);
		
		//Check existing IDs
		$query = "SELECT count(*) as total FROM ratings WHERE id='".$voteForID."'";
		$result = $this->querySelect($query);
			
		if (is_array($result) && count($result) > 0 && $result[0]['total'] > 0 ) {
			$query = "UPDATE ratings SET total_votes = '".$addedVotes."', total_value='".$sum."', used_ips='".$serializeIPList."' WHERE id='".$voteForID."'";			
			$this->queryExecute($query);
		}
		else {
			$query = "INSERT INTO ratings (id, total_votes, total_value, used_ips) VALUES ($voteForID, $addedVotes, $sum, '".$serializeIPList."')";
			$this->queryExecute($query);
		}
		
		
		$currentRating = @number_format($sum / $addedVotes, 2) * STARWIDTH ;
		echo $this->drawPrintedStars($currentRating, $voteForID, true, $addedVotes);
	}
	
	public static function votedByUser($userID) {
		$query = "SELECT ";
	}
	
	public function drawPrintedStars($currentRating, $voteForID, $addDivID = false,$nbrevote=NULL) {
		ob_start();
		header("Cache-Control: no-cache");
		header("Pragma: nocache");		
		$ratingString = "<ul class=\"unit-rating\">\n" .
						"<li class=\"current-rating\" style=\"width:". $currentRating ."px;\">Currently $currentRating</li>\n";
			
			for ($ncount = 1; $ncount <= TOTALSTARS; $ncount++) { 
				$ratingString .= "<li class=\"r$ncount-unit\">$ncount</li>\n";
			}
			
		$ratingString .= '</ul>Côte : '.($currentRating/STARWIDTH).'/5<br />
				avec '.$nbrevote.' vote(s) dont le vôtre.';//show the updated value of the vote
		
		//name of the div id to be updated | the html that needs to be changed
		if ($addDivID === false) {
			$output = $ratingString;
		}
		else {
			$output = "unit_long$voteForID|$ratingString";
		}
		
		return $output;
	}
	
}

?>