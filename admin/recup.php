<?php

require("conf.php"); //On insert le fichier qui contient les informations

if(empty($_POST['mail'])) //On verifie que l'utilisateur est bien rentré une e-mail
{
	$reponse = 'Vous n\'avez pas saisi d\'e-mail.';
}
else
{

	$mail = htmlentities($_POST['mail']); //On recupère la variable et on déactive les balises html s'il y en avait encore       
	$smtp=new Rmail();
	$verif = "SELECT COUNT(*) FROM admin_information WHERE mail='$mail'";
	$donnees2 = $db->get_var($verif);
	
	if($donnees2 != 1) //On cherche si l'e-mail existe
	{
		$reponse = 'Cette adresse e-mail n\'existe pas.';
	}
	else
	{  
	  
		$sql1 = "SELECT admin_id id,admin_username pseudo,admin_active_id active 
		FROM admin_information 
		WHERE mail='$mail'";
		$donnees = $db->get_row($sql1);
		$active=$donnees['active'];
		if($active!=4)
		{
			$id = $donnees['id'];
			$pseudo=$donnees['pseudo'];
			$pass = genere_passwd();
			$sql_pass=md5($pass);
			
		
		
			$message = '<html><body>Bonjour,<br><br>'; //On fait le mail
			$message .= 'Comme vous l\'avez demandé,<br>';
			$message .= 'voici votre nouveau mot de passe, notez le précieusement :<br>';
			$message .= 'Votre Pseudo : ' . $pseudo .'<br>';
			$message .= 'Votre Code Secret : ' . $pass .'<br><br>';
			$message .= 'A très bientôt sur le site! <br>';
			$message .= 'L\'équipe de '.$nom_site. '<br>';
			$message .= '<a href="' .$url_site. '">Gestdown '.$version.'</a>';  
			
			/*$entete  = "MIME-Version: 1.0\r\n";
			$entete .= "Content-type: text/html; charset=utf-8\r\n";
			$entete .= "From: Webmaster Gestdown<$email_admin>\r\n";
			$entete .= "Reply-To: $email_admin\r\n";
			$entete	.= "To: $mail\r\n";
			$entete .= "Subject: Identifiants sur $nom_site : $pseudo .\r\n";
			$entete .= "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")."\r\n";*/
			
			$smtp->setFrom('Webmaster (Balor) <contact@imagidream.info>');
			$smtp->setSubject("Identifiants sur $nom_site : $pseudo");
			$smtp->setHTML($message);
			$smtp->setHTMLCharset('UTF-8');
			if($smtp->send(array($mail)))
			{
		
				$reponse = 'Votre adresse a bien été reconnue.<br>';
				$reponse .= 'Votre Pseudo et votre Code vous ont été envoyés par e-mail.<br>'; 
				$reponse .= 'Vous devriez les recevoir dans votre boîte aux lettres dans quelques instants.';
				$db->query("UPDATE admin_information SET admin_active_id=4 ,`admin_password`='$sql_pass' WHERE admin_id=$id");
			}
			else
			{
				$reponse="Votre mot de pass n'a pas été envoyé<br /> \n";
				$reponse.= "Cound not send the message to $mail.\nError: ".$smtp->error."\n";
			}
		}
		else
			$reponse="Vous avez déjà demandé un nouveau mot de passe.<br />\n Veuillez regarder votre boite mail : $mail";
	}
	
	unset($db); //Deconnection...

}



?>

<html>
<head> 
<script language="Javascript">
function Fermer()
{
opener=self;
self.close();
}
</script>
</head>
<body>
<?php echo $reponse; ?> <!-- On affiche la reponse du script -->
<br><a href="" onClick='Fermer()'>Fermer</a>
</body>
</html>
