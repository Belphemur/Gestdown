<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Activation de votre compte</title>
</head>
<?php
require("./admin/conf.php"); //On insert le fichier qui contient les informations
if(isset($_GET['pseudo']) && isset ($_GET['code']))
{
	$pseudo=$db->real_escape($_GET['pseudo']);
	$code=$db->real_escape($_GET['code']);
	if(strpos($pseudo,' '))
	{
		die('Insertion SQL détectée');
	}

	$query="SELECT confirm FROM admin_information WHERE admin_username='$pseudo'";
	$donnee=$db->get_row($query);

	if($donnee['confirm'] == $code)
	{
		$db->query("UPDATE admin_information SET `confirm`='', `admin_active_id`=1  WHERE admin_username='$pseudo'");
		echo 'Votre compte est maintenant activé <br> <a href="' .$url_site. '">' .$nom_site. '</a>';
	}
	else
	{
		echo  'ce code est invalide ou ne correspond pas à votre pseudo<br><a href="' .$url_site. '">' .$nom_site. '</a>';
	}
}
else
	header("Location:index.php");
?>
<body>
</body>
</html>