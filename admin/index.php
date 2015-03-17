<?php
require_once 'header.php';
$pseudo= $_SESSION['pseudo'];
require_once '../vendor/autoload.php';


$client = new \Imgur\Client();
$client->setOption('client_id', $imgur['CLIENT_ID']);
$client->setOption('client_secret', $imgur['CLIENT_SECRET']);
?>

Bienvenue dans la console d'administration de GestDown <?php echo $version; ?>.
		<br />
		Pseudo: <?php echo $pseudo; ?><br>
		<br />
		</div>
<div id="content">
			<h2>Bienvenue <?php echo $pseudo; ?></h2>
			<p>
Ceci est votre espace d'administration!<br>
Vous pourrez y g&eacute;rer gr&acirc;ce au menu de votre gauche, l'ensemble du Gestionnaire de Download!<br>
<?php
$etat=$db->get_var("SELECT admin_active_id active FROM admin_information WHERE admin_username='$pseudo'");
if($etat==4)
{
	echo '<br /><span style="color:red;">Veuillez changer votre mot de passe pour des raisons de sécurité</span>'."\n<br />";
?>
<a href="espace_admin_changeinfo.php" title="Changer son mot de passe" target="_self">Cliquer ici pour le changer</a>
<?php
}
if (isset($_SESSION['imgurToken'])) {
    $client->setAccessToken($_SESSION['imgurToken']);
    if($client->checkAccessTokenExpired()) {
        $client->refreshToken();
    }
    echo 'Imgur activé';
} else {
    echo '<a href="'.$client->getAuthenticationUrl().'">Connecter à Imgur</a>';
}
?>
  </p>
  <table width="299" border="0">
  <tr>
    <th width="215" scope="row">Nombre de liens morts :</th>
    <td width="74"><?php 
	$sql = 'SELECT count(downloads.id)
	FROM downloads 
	WHERE `mort`!=0';
	
 	$num=$db->get_var($sql);
	if($num !=0)
		echo $num;
	else
		echo 0;
	?></td>
  </tr>
  <tr>
    <th scope="row">Nombre de Synopsis Proposés :</th>
    <td><?php
	$sql = 'SELECT count(descriptions.id) FROM descriptions WHERE mort=0';
	$num=$db->get_var($sql);
	if($num !=0)
		echo $num;
	else
		echo 0;?></td>
  </tr>
</table>

</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>