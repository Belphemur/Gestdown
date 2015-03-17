<?php
require_once 'header.php';
?>
Bienvenue dans la console d'administration de GestDown <?php echo $version; ?>.
		<br />
		<a href="inscription.php">Ajout Admin</a><br>
<a href="suppr_admin.php">Supprimer Admin</a><br>
<a href="minichat.php">Chat Admins</a><br>
<a href="deconnexion.php">Déconnexion</a><br>
		<br />
		</div>
<div id="content">
			<h2>Ajouter un Admin</h2>
			<p>
<form name="form1" method="post" action="inscription2.php">
	Pseudo:<br>
    <input type="text" name="admin">*<br>
    Votre E-Mail:<br>
    <input name="mail" type="text" id="mail">*<br><br>	
    <input type="submit" name="Submit" value="Inscription">
  </form><br>
<a href="espace_admin.php">Retour</a>
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>