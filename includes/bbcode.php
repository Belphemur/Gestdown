<?php

if(strpos(dirname($_SERVER['PHP_SELF']),'admin'))
	require_once('../includes/bbcode/nbbc_main.php');
else
	require_once('./includes/bbcode/nbbc_main.php');
$bbcode = new BBCode;
$bbcode->SetLocalImgDir("images/smileys");
$bbcode->SetSmileyURL("images/smileys");
function replacement($text)
{
	$text=stripslashes($text);
	global $bbcode;
	return $bbcode->Parse($text);
}

?>