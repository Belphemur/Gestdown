<?php
require_once 'header.php';
$pseudo= $_SESSION['pseudo'];
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
	$sql = 'SELECT count(descriptions.id) FROM descriptions';
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