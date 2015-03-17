<?php
require_once 'header.php'; 
require_once '../includes/bbcode.php';

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
			<h2>Chat des Admins</h2>
			<p>        
<?php
if (isset($_POST['description'])) // Si la variable existe et quelle n'est pas vide
{ 
        // On utilise la fonction PHP htmlentities pour éviter d'enregistrer du code HTML dans la table    
        $message = htmlentities($_POST['description'],ENT_QUOTES);
        $pseudo = $_SESSION['pseudo'];
    
        // Ensuite on enregistre le message
        $db->query("INSERT INTO pdf_minichat VALUES('', '$pseudo', '$message')");
    
        // On se déconnecte de MySQL
        mysql_close();
		header('Location:minichat.php');

}
else if(isset($_GET['vider']))
{
	
        // Ensuite on enregistre le message
        $db->query("TRUNCATE TABLE `pdf_minichat`");
    
        // On se déconnecte de MySQL
        mysql_close();
		header('Location:minichat.php');
}
// Que l'on ait enregistré des données ou pas...
// On affiche le formulaire puis les 10 derniers messages
// Tout d'abord le formulaire :
?>

<form name="Formdescription" id="Formdescription" method="post" action="minichat.php" onSubmit="return false;">

<p align="center">Pseudo : <?php echo $_SESSION['pseudo']; ?></p>
<p align="center"> Message : <br />
<script language="javascript">initBBcode('description','Prévisualiser',450,150,''); </script>
</p>
<p align="center"><input type="submit" value="Envoyer" onclick="if(valideForm()){document.Formdescription.submit()}"/></p>
</form>
<form action="minichat.php" method="get">
<input name="vider" type="submit" value="Vider"/></form>


<?php

// Maintenant on doit récupérer les 10 dernières entrées de la table
// On se connecte d'abord à MySQL :


// On utilise la requête suivante pour récupérer les 10 derniers messages :
$reponse="SELECT * FROM pdf_minichat ORDER BY ID DESC LIMIT 0,12";
$don=$db->get_results($reponse);

// Puis on fait une boucle pour afficher tous les résultats :
if($don)
{
	foreach ($don as $donnees)
	{
		$affiche_message ='<div id="chat">'.html_entity_decode(replacement($donnees['message']),ENT_QUOTES).'</div>';
		echo '<p><strong><font face="Arial" color="#339966">' .$donnees['pseudo']. '</font></strong> : ' .$affiche_message. '</p>';
	}
// Fin de la boucle
}
?>        
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>

