<?php 
//sEcure Login ---- library by abdohoo.com
//file ---- login form
function dojoForm($error_text='') { ?>

<?php
//include"head.php"; head section can contain your head file of ur template ?>
<style type="text/css">
.stformone {
	font-family: Verdana;
	font-size: 12px;
}
.stformtwo {
	font-family: Verdana;
	font-size: 12px;
	text-align: center;
}
.stformonem {
	background-color: #F7F7DE;
}
.stformtwom {
	font-family: Verdana;
	font-size: 12px;
	text-align: center;
	color: #FF0000;
}
</style>
<center>
<div id='center'>
<form method="post">
<h3>   <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
	<title>CyPlanning :: Accès.</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="images/style.css" />
</head>

<body><br /><br /><br /><br /><br /><br /><br /><br /><br />
<div align="center"><font size="3">CyPlanning: Cyber Planning. </font></div><br />
<div align="center"><table width="580" height="247" border="0" background="images/milieu.GIF"/>
<tr><td><div align="center">
Connexion au calendrier/planning.<br /><br />
<form method="post">
<table border="0">
<tr>
   <td>Pseudo:</td>
   <td><input name="userName" type="text" size="14"/></td>
</tr>
<tr>
   <td>Pass:</td>
   <td><input name="password" type="password" class="stformone" /></td>
</tr>
</table>
Mot de passe perdu ? <a href="recuppass.php" title="Récupérer son mot de passe" target="_blank">cliquer ici</a><br />
<input type="submit" name="Submit" value="Connexion" />
<input name="do_login" type="hidden" value="1" />
</form>
</div></td></tr><td class="stformtwom" colspan="2"><strong><?php echo $error_text; ?></strong></td>
</table>
<br /><br /></div>
</body>
</html>
<?php } ?>
