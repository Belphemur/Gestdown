<?php
include('../admin/conf.php');
include('../classes/RatingManager.inc.php'); 
function get_ip(){ 
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];} 
elseif(isset($_SERVER['HTTP_CLIENT_IP'])){ 
	$ip = $_SERVER['HTTP_CLIENT_IP'];} 
else{ $ip = $_SERVER['REMOTE_ADDR'];} 
return $ip;}


$ratingManager = RatingManager::getInstance();
 // $_REQUEST['j'] vote values 
 // $_REQUEST['q'] product id voted
 // $_REQUEST['t'] IP address of the personal
foreach($_REQUEST as $index => $valeur) 
{
	$$index = $db->real_escape(trim($valeur));
}
$vote_sent = preg_replace("/[^0-9]/","",$_REQUEST['j']);
$id_sent = preg_replace("/[^0-9]/","",$_REQUEST['q']);
$ip_num = get_ip();

if ($vote_sent > 5 or $vote_sent < 1) die("Sorry, vote appears to be invalid."); // kill the script because normal users will never see this.
$ratingManager->updateVote($vote_sent, $id_sent, $ip_num);
?>