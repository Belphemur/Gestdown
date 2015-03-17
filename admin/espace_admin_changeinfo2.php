<?php
require_once 'header.php';

	$id=$_POST['id'];
	$mail = $_POST['mail'];
	$mdp = $_POST['mdp'];
	$new_mdp = $_POST['new_mdp'];
	$confirm_mdp = $_POST['confirm_mdp'];
		
	$recherche ="SELECT admin_password,mail FROM admin_information WHERE `admin_id`='$id'";
	$var = $db->get_row($recherche); 
	$pass_sql = $var['admin_password'];
	$mail_sql = $var['mail'];
	
	if($pass_sql != md5($mdp))
	{
		$reponse = 'Le mot de passe n\'est pas bon.' ;

	}	 
	else	  
	{
		if ( $mail != $mail_sql && empty($new_mdp))
		{
			$verification2 ="SELECT COUNT(*) FROM admin_information WHERE mail='$mail'";
			$donnees2 = $db->get_var($verification2);
	
			if($donnees2>= 1) 
			{
				$reponse = 'Cette adresse e-mail est déjà utilisé, veuillez en choisir une autre. <a href="javascript:history.back(1)">Retour au formulaire</a>';
			}
			else
			$db->query("UPDATE admin_information SET `mail`='$mail' WHERE admin_id='$id'");
			$reponse = 'Vos données on été actualisées.<br> <a href="index.php">Retour à l\'espace membre</a>' ;
		}	
		elseif(empty($mail))
		{
			$reponse = 'Vous n\'avez pas rempli le champ de l\'email.' ;
		}
		elseif(!empty($new_mdp) & ($new_mdp == $confirm_mdp))
		{
			$nouv_mdp=md5($new_mdp);
			$db->query("UPDATE admin_information SET admin_active_id=1, `mail`='$mail', `admin_password`='$nouv_mdp' WHERE admin_id='$id'");
			$reponse = 'Vos données on été actualisées.<br> <a href="index.php">Retour à l\'espace membre</a>';
		}
		elseif(!empty($new_mdp) & ($new_mdp != $confirm_mdp))
		{
			$reponse='Nous somme désolé mais votre nouveau mot de passe ne correspond pas à la confirmation';
			$reponse.='<br><a href="javascript:history.go(-1)">Retour au formulaire</a>';
		}
	}
	unset($db); //Deconnection	
?>

Bienvenue dans la console d'administration de GestDown <?php echo $version; ?>.
		<br />
		<br />
		</div>
<div id="content">
			<h2>Vos informations</h2>
			<p>
<?php echo $reponse; ?>
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>

