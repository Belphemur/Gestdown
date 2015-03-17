<?php
require_once 'header.php';


$pseudo = $_SESSION['pseudo'];
$requete = "SELECT admin_id,mail FROM admin_information WHERE `admin_username`='$pseudo'"; 
$var = $db->get_row($requete);

	$id = $var['admin_id'];
	$mail = $var['mail'];	
mysql_close();
?>

Bienvenue dans la console d'administration de GestDown <?php echo $version; ?>.
		<br />
		<br />
		</div>
<div id="content">
			<h2>Vos informations</h2>
			<p>&nbsp;</p>
  <form name="form2" method="post" action="espace_admin_changeinfo2.php" onSubmit="return valideForm()">
  <input name="id" type="hidden" value="<?php echo $id; ?> " />
  <table width="420" height="124" border="0">
  <tr>
    <th width="153" scope="row">Votre Pseudo:</th>
    <td width="251">*
    <input type="text" name="pseudo" readonly="readonly"  value="<?php echo $pseudo; ?>" /></td>
  </tr>
  <tr>
    <th scope="row">Votre MDP:</th>
    <td>*
    <input name="mdp"  id="mdp" type="password" size="12" maxlength="12"/></td>
  </tr>
  <tr>
    <th scope="row">Votre Nouveau MDP:</th>
    <td>&nbsp;&nbsp;&nbsp;<input name="new_mdp" id="new_mdp" type="password" size="12" maxlength="12"/ />       (laisser vide si vous ne voulez pas en changer)</td>
  </tr>
    <tr>
    <th scope="row">Confirmer votre nouveau MDP:</th>
    <td>&nbsp;&nbsp;&nbsp;<input name="confirm_mdp"  id="confirm_mdp"  type="password" size="12" maxlength="12"//>       (laisser vide si vous ne voulez pas en changer)</td>
  </tr>
  <tr>
    <th scope="row">Votre E-mail: </th>
    <td>*
    <input name="mail" type="text" id="mail" value="<?php echo $mail; ?>" /></td>
  </tr>
</table>

    <p><br />
      <input type="submit" name="Submit" value="Modifer">
    </p>
</form>
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>
