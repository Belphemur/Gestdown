<?php
require_once 'header.php';
include ('./templates/links.html');
include ('../includes/animeka.php');
require_once ('../includes/imagesize.php');
if (isset($_POST['id'])) {
?>
    <div id="content">
        <h2>Modifier la Série</h2>
        <p>
        <?php
        $msg_erreur = "Erreur. Les champs suivants doivent être obligatoirement remplis :<br/><br/>";
        $msg_ok = "Série Modifiée avec succes.<br />";
        $message = $msg_erreur;

        // vérification des champs
        if (empty($_POST['id']))
            $message .= "ID<br/>";
        if (empty($_POST['nom']))
            $message .= "Nom<br/>";
        if (empty($_POST['description']))
            $message .= "Description Obligatoire<br/>";

        // si un champ est vie, on affiche le message d'erreur
        if (strlen($message) > strlen($msg_erreur)) {

            echo $message;

            // sinon c'est ok
        } else {

            foreach ($_POST as $index => $valeur) {
                $$index = trim($valeur);
            }
            try {

                $sql = "UPDATE categorie SET nom=?, description=?, image=?, finie=?, width=?, height=? WHERE id=?";
                $img = getImageDimension($image);
                $res = $db->pQuery($sql, array('sssiiii', $nom, $description, $image, $finie,$img->width,$img->height,$id));
                if (isset($_POST['animeka']) && $_POST['animeka'] != '') {
                    $url = $_POST['animeka'];
                    $result = new Result();
                    $result = animeka($url);
                    if (!$result->echec) {
                        $sql = 'INSERT INTO `informations` (`cat_id` ,`annee` ,`studio` ,`genre` ,`auteur` ,`episode`)
                        VALUES (?,  ?,  ?,  ?,  ?,  ?)
                        ON DUPLICATE KEY UPDATE annee=?,studio=?,genre=?,auteur=?,episode=?';
                        $db->pQuery($sql,
                                array('iissssissss', $id, $result->annee, $result->studio, $result->genre, $result->auteurs, $result->type,
                                    $result->annee, $result->studio, $result->genre, $result->auteurs, $result->type));
                    }
                    else
                        echo "Lien animeka invalide<br /> \n";
                }
            } catch (Exception $exc) {
                echo $exc;
            }


            echo $msg_ok;
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
    } else {
        $modifier = $_GET['modifier']; ?>
        <div id="content">
            <h2>Modifier/Supprimer des Catégories</h2>
            <p>

        <?php
        // on crée la requête SQL
        $sql = 'SELECT * FROM categorie WHERE id=?';
        $db->pQuery($sql, array('i', $modifier));
        // on envoie la requête
        $data = $db->getRow(false, false);

        echo '<form name="Formdescription" id="Formdescription" method="post" action="modifier_cat.php" onSubmit="return false;">
		<table width="200" style="border: #999 solid 1px " border="0">
		<tr>
		<th scope="row">Nom:</th>
		<td>*
		<input type="text" name="nom" value="' . $data['nom'] . '"/></td>
		</tr>
		<tr>
		<th scope="row">Description:</th>
		<td> <script language="javascript">initBBcode("description","Prévisualiser",450,150,' . json_encode($data['description']) . ',0); </script></td>
		</tr>
		<tr>
		<th scope="row">Url de l\'image:</th>
		<td>*
		<input type="text" name="image" value="' . $data['image'] . '" size="50"/></td>
		</tr>
		<th scope="row">Url de animeka <br />(NE PAS TOUCHER):</th>
		<td>
		<input type="text" name="animeka" value="" size="50"/></td>
		</tr>
		<tr>
		<th scope="row">Finie ?</th>
		<td>';
        if ($data['finie'] == 0)
            echo'<select  name="finie">
			<option value="0">Non</option>
			<option value="1">Oui</option>
			</select></td>';
        else
            echo'<select  name="finie">
			<option value="1">Oui</option>
			<option value="0">Non</option>
			</select></td>';
        echo'</tr>
		</table>
		<input name="id" type="hidden" value="' . $modifier . '" />
		<input type="submit" name="envoi" value="Modifier Catégorie" onclick="if(valideForm()){document.Formdescription.submit()}"></form><br><br>
		<a href="javascript:history.go(-1)">Retour</a>';
        // on ferme la connexion à mysql
        unset($db);
        ?>
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