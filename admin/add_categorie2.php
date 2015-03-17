<?php
require_once 'header.php';
include ('./templates/links.html'); 
include ('../includes/animeka.php');
include ('../includes/addSerieXml.php');?>
<div id="content">
    <h2>Ajouter une Série</h2>
    <p>
        <?php
        if(isset($_POST['url']) && isset($_POST['id']))
        {
            $id=$_POST['id'];
            $url=$_POST['url'];
            $result = new Result();
            $result = animeka($url);

            $db->query("INSERT INTO informations VALUES('$id','{$result->annee}','{$result->studio}','{$result->genre}','{$result->auteurs}','{$result->type}')");
            echo "<br />Série Ajoutée avec succes";
        }
        else
        {
            $msg_erreur = "Erreur. Les champs suivants doivent etre obligatoirement remplis :<br/><br/>";
            $msg_ok = "Catégorie Ajouté.<br />";
            $message = $msg_erreur;

            // vérification des champs
            if (empty($_POST['nom']))
                $message .= "Nom<br/>";
            if (empty($_POST['description']))
                $message .= "Description Obligatoire<br/>";

            // si un champ est vie, on affiche le message d'erreur
            if (strlen($message) > strlen($msg_erreur))
            {

                echo $message;

                // sinon c'est ok
            } else
            {

                foreach($_POST as $index => $valeur)
                {
                    $$index = trim($valeur);
                }

                $sql = "INSERT INTO categorie VALUES (NULL, ? , ?, ?,0,0,0)";
                $res = $db->preparedQuery($sql, array("sss",$nom,$description,$image));          
                $id=$db->getLastID();
                $serie=$nom;
                $nom=str_replace(" - ","-",$nom);
                $nom=str_replace(" ","-",$nom);
                echo '<br /><span style="color:#F00; text-align:center; font-size:24px">',$serie,'</span><br /><br />';

                $result = animeka("http://www.animeka.com/animes/detail/$nom.html");
                if(!$result->echec)
                {
                    $db->query("INSERT INTO informations VALUES('$id','{$result->annee}','{$result->studio}','{$result->genre}','{$result->auteurs}','{$result->type}')");
                    echo "<br />Série Ajoutée avec succès";
                }
                else
        {
            ?>
    <form action="add_categorie2.php" method="post" enctype="application/x-www-form-urlencoded" name="animeka" target="_self" lang="fr">
        <p>La série n'a pas été trouvée sur animeka,<br />veuillez introduire dans la case si dessous l'URL de la page de la série sur animeka.<br />
		Le script fera le reste. <br /><br />
            <img src="http://imagidream.eu/image-C65A_4A89622C.gif" alt="" width="433" height="200" /><br /><br />
		URL :
            <input name="url" type="text" size="75" maxlength="255" />
        </p>
        <input name="id" type="hidden" value="<?php echo $id;?>" />
        <input name="" type="submit" />
    </form>
                <?php
                die("	</p>
			</div>
	<div id=\"footer\">
			<?php echo $close; ?> 
		</div>
		
	</div>
	</body>
	</html>");
            }


    }
    ?>
    <a href="javascript:history.go(-1)">Retour</a>
</p>
</div>
<div id="footer">
    <?php echo $close; ?>
</div>

</div>
</body>
</html>
    <?php
}
?>