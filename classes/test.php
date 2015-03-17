<?php
require_once("dbForm.class.php");
require_once("../admin/conf.php");
$value=array('mort'=>'hidden','categorie'=>'password');
$names=array('mort'=>'iKillYou :D','lien'=>'Lien MQ');
$test= new dbForm($db,"downloads");
$test->ignore('id');
$test->changeType($value);
$test->changeDisplayName($names);
$test->addField('test','hidden','test','categorie',5);
$test->display();
?>