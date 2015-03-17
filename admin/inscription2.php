<?php
require_once 'header.php';

$mail = htmlentities($_POST['mail']);
$pseudo = $_POST['admin']; //On recupère les infos
    if (preg_match("!^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$!", $mail) && !strpos($pseudo,' ') ) //Verifie que l'email entrée n'est pas une fausse.
    {
		$chaine = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
		$confirm = str_shuffle($chaine); //On créé le code de confirmation
		
		
		$mdp = genere_passwd();
		$smtp=new Rmail();
		
		/*On Fait la variable contenant le mail de confirmation*/
	
		$message1 = '<html><body>Bonjour ' . $pseudo . ',</br>'; 
		$message1 .= '</br>';
		$message1 .= 'Vous venez de vous venez d\'être inscrit en temps qu\'administrateur du site ' .$nom_site. '.<br>'; 
		$message1 .= 'Voici un rappel de vos identifiants, notez les précieusement : <br>';
		$message1 .= 'Votre Pseudo : ' . $pseudo .'.<br>';
		$message1 .= 'Votre Mot de passe: ' . $mdp .'.<br><br>';
		$message1 .='Pour activer votre compte <a href="' .$url_site. 'activation.php?code=' .$confirm.'&amp;pseudo=' .$pseudo.'">Cliquer ici</a><br><br>';
		$message1 .= 'Si vous souhaitez changer vos informations, rendez-vous dans votre compte pour les mettre à jour.<br>';
		$message1 .= 'A très bientôt sur le site!<br>L\'équipe de ' .$nom_site. '.<br><a href="' .$url_site. '">' .$nom_site. '</a>';

		
		$verification = "SELECT COUNT(*) FROM admin_information WHERE admin_username='$pseudo' OR mail='$mail'";
		$donnees = $db->getVar($verification);

		if($donnees >= 1) //On verifie que le pseudo n'existe pas déjà
		{ 
			$reponse = 'Le pseudo ou l\'email est déjà utilisé, merci d\'en choisir un(e) autre.';
		}
		else
		{	
			if( empty($pseudo) || empty($mail) ) //On verifie que les variables précédentes ne soient pas vide
			{
				$reponse = 'Un ou plusieurs champs ne sont pas remplis';
			}
			else //Si tout est bon on entre les données dans la BDD et on envoye le mail
			{
				$mdp=md5($mdp);
				$reponse = 'Vous vener de créer un nouvel Admin. <br>Cette personne va recevoir un e-mail lui rappelant ces identifiants. <br>';
				/*$entete  = "MIME-Version: 1.0\r\n";
				$entete .= "Content-type: text/html; charset=iso-8859-1\r\n";
				$entete .= "From: <$email_admin>\r\n";
				$entete .= "Reply-To: $email_admin\r\n";
				$entete	.= "To: $mail\r\n";
				$entete .= "Subject: Bienvenue sur $nom_site $pseudo .\r\n";
				$entete .= "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")."\r\n";*/
				
				
				$smtp->setFrom("Webmaster (Balor) <$email_admin>");
				$smtp->setSubject(" Bienvenue sur $nom_site $pseudo");
				$smtp->setHTML($message1);
				$smtp->setHTMLCharset('UTF-8');
				
				if($smtp->send(array($mail)))
				{
					$reponse.= "Message sent to $mail OK.\n";
					$db->query("INSERT INTO admin_information VALUES ('','$pseudo', '$mdp',4,'$mail', '$confirm')");
				}
				else
					$reponse= "Cound not send the message to $mail.\nError: ".$smtp->error."\n";
			}
		}
		
		unset($db); //On se deconnecte
	}
	else //Reponse si l'adresse e-mail est une fausse
	{
		$reponse = 'Votre adresse e-mail "' . $mail . '" n\'est pas correcte<br /> ou votre pseudo ('.$pseudo.') contient un espace, ce qui est interdit.';
	} 	




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
<?php echo $reponse; ?> <!-- On affiche la reponse de tout le code du dessus -->
<br>
<a href="javascript:history.back(1)">Retour Formulaire</a><br>
<a href="espace_admin.php">Retour</a>
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>>

