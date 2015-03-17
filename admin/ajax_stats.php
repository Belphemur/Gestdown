<?php
require_once 'conf.php';
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->query("SET NAMES 'utf8'");
if(isset($_GET['date']) && isset($_GET['date_end']))
{
	$date_f=$_GET['date'];
	$date_e=$_GET['date_end'];
	$stat= new Stats($date_f,$db);
	$type=$_GET['type'];
	if($type=='d')
		echo $stat->compare($date_e,true);
	else if ($type=='t')
		echo $stat->compare($date_e);
	else
		echo 'Veuillez spécifier le type de stats que vous voulez';
}
else if(isset($_GET['date']))
{
	$date=$_GET['date'];
	$stat= new Stats($date,$db);
	if(isset($_GET['type']))
	{
		$type=$_GET['type'];
		if($type=='d')
			echo $stat->daily_display();
		else if ($type=='t')
			echo $stat->total_display();
		else
			echo 'Veuillez spécifier le type de stats que vous voulez';
	}
	else
		echo 'Aucun type donné';
	
}
else
	echo 'Erreur : aucune date donnée';
?>