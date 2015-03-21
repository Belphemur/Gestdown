<?php
if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
    include_once('conf.php');
    require_once 'login.php';
    $db->disableDiskCache();
    login();
} else {
    require_once('header.php'); //Commme d'ab
    include('./templates/links.html');
}

function sendToImgur($image, $name)
{
    global $imgur, $nom_site;
    require_once '../vendor/autoload.php';

    $client = new \Imgur\Client();
    $client->setOption('client_id', $imgur['CLIENT_ID']);
    $client->setOption('client_secret', $imgur['CLIENT_SECRET']);
    
    if (isset($_SESSION['imgurToken'])) {
        $client->setAccessToken($_SESSION['imgurToken']);
        if ($client->checkAccessTokenExpired()) {
            $client->refreshToken();
        }
    }

    $imageData = array(
        'image' => $image,
        'type' => 'file',
        'name' => $name,
        'title' => $nom_site . ' : ' . $name,
    );
    $basic = $client->api('image')->upload($imageData);
    unlink($image);
    $link = explode('//',$basic->getData()['link']);
    return '//'.$link[1];
}

function resizeAndImgur($uploadfile, $screenPath, $name)
{
    if (!move_uploaded_file($_FILES['screen_image']['tmp_name'], $uploadfile))
        die("Le screenshot envoyé est invalide");
    try {
        $resize = new SimpleImage($uploadfile);
        $resize->resizeToWidth(460);
        $resized = $resize->save($screenPath);
        unlink($uploadfile);
        return sendToImgur($resized, $name);
    } catch (Exception $exc) {
        unlink($uploadfile);
        die("Le screenshot envoyé est invalide");
    }
}

if (isset($_POST['id'])) {
    ?>
    <div id="content">
        <h2>Modifier l'Episode</h2>

        <p>
            <?php
            $msg_erreur = "Erreur. Les champs suivants doivent être obligatoirement remplis :<br/><br/>";
            $msg_ok = "Download Modifié.<br />";
            $message = $msg_erreur;

            // vérification des champs
            if (empty($_POST['id']))
                $message .= "ID<br/>";
            if (empty($_POST['nom']))
                $message .= "Nom<br/>";
            if (empty($_POST['actif']))
                $message .= "Actif<br/>";
            if (isset($_POST['ajax'])) {
                if (!isset($_FILES['screen_image'])) {
                    if (empty($_POST['screen']))
                        $message .= "Screen<br/>";
                } else if ($_FILES['screen_image']['error'] != UPLOAD_ERR_OK)
                    $message .= "Problème lors de l'upload de votre screenshot<br/>";
                if (empty($_POST['description']))
                    $message .= "Nom de l'épisode<br/>";
            }

            // si un champ est vie, on affiche le message d'erreur
            if (strlen($message) > strlen($msg_erreur)) {

                echo $message;

// sinon c'est ok
            } else {
                foreach ($_POST as $index => $valeur) {
                    $$index = trim($valeur);
                }
                $db->pQuery("SELECT c.nom FROM categorie c WHERE c.id=?", array('i', $categorie));
                $cat = $db->getVar();
                if (file_exists($_FILES['screen_image']['tmp_name']) && is_uploaded_file($_FILES['screen_image']['tmp_name'])) {
                    $name = $cat . " " . $nom;
                    $screenPath = $tmpDir . $name;
                    $uploadfile = $tmpDir . basename($_FILES['screen_image']['name']);
                    $screenShot = resizeAndImgur($uploadfile, $screenPath, $name);
                    if (!empty($screenShot)) {
                        $screen = $screenShot;
                    }
                } else if (empty($screen))
                    die("Le screenshot envoyé est invalide");

                if (isset(SessionManager::getInstance()->sortir) && SessionManager::getInstance()->sortir == 1) {
                    $sql = "UPDATE downloads SET auteur=?, categorie=?, nom=?, description=?, lien=?, lien2=?,lien3=?,torrentMQ=?,torrentHD=?,torrentFHD=?, screen=?, actif=?, mort=?,date=? WHERE id=?";
                    $db->pQuery($sql, array('sssssssssssiiii', $auteur, $categorie, $nom, $description, $lienMQ, $lienHD, $lienFHD, $torrentMQ, $torrentHD, $torrentFHD, $screen, $actif, $mort, time(), $id));
                    SessionManager::getInstance()->sortir = 0;
                } else {
                    $sql = "UPDATE downloads SET auteur=?, categorie=?, nom=?, description=?, lien=?, lien2=?,lien3=?,torrentMQ=?,torrentHD=?,torrentFHD=?, screen=?, actif=?, mort=? WHERE id=?";
                    $db->pQuery($sql, array('sssssssssssiii', $auteur, $categorie, $nom, $description, $lienMQ, $lienHD, $lienFHD, $torrentMQ, $torrentHD, $torrentFHD, $screen, $actif, $mort, $id));
                    $db->pQuery('DELETE FROM `descriptions` WHERE `mort`!=0 AND download=?', array('i', $id));
                }
                include('gen_rss.php');

                $dl = $id;
                $bbcode_lien_template = '<textarea name="" cols="65" rows="10" readonly="readonly">[color=orange]' . stripcslashes($description) . '[/color]' . "\n\n" . '[ALIGN=center][img]' . $screen . '[/img][/ALIGN]' . "\n\n";
                $bbcode_lien = "[u][color=red]" . $cat . " " . $nom . "[/color][/u] - " . "\n";
                $bbcode_lien.= 'Episode : [url=' . $url_site . 'ep-' . $dl . '.html]Gestdown[/url]' . "\n";

                /*if (!empty($lienMQ))
                    $bbcode_lien .= 'MQ : [url=' . $url_site . 'dl-' . $dl . '-mq.html]Jheberg MQ[/url]' . "\n";

                if (!empty($lienHD))
                    $bbcode_lien .= 'HD : [url=' . $url_site . 'dl-' . $dl . '-hd.html]Jheberg HD[/url]' . "\n";

                if (!empty($lienFHD))
                    $bbcode_lien .= 'FHD : [url=' . $url_site . 'dl-' . $dl . '-fhd.html]Jheberg FHD [/url]' . "\n";

                if (!empty($torrentMQ))
                    $bbcode_lien .= "[b][u] Liens Torrent[/u][/b] \n" . 'Torrent MQ : [url=' . $url_site . 'tor-' . $dl . '-mq.html]Torrent MQ[/url]' . "\n";
                if (!empty($torrentHD))
                    $bbcode_lien .= 'Torrent HD : [url=' . $url_site . 'tor-' . $dl . '-hd.html]Torrent HD[/url]' . "\n";
                if (!empty($torrentFHD))
                    $bbcode_lien .= 'Torrent FHD : [url=' . $url_site . 'tor-' . $dl . '-fhd.html]Torrent FHD[/url]' . "\n";*/

                echo $msg_ok, 'Voici les liens BBCODE à mettre de la news : <br />';
                echo $bbcode_lien_template, $bbcode_lien, '</textarea>';
            }
            if (isset($_POST['ajax']))
                die('</div>');
            ?>
            <a href="./gestion_dl.php">Retour</a>
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
    $modifier = $_GET['modifier'];
    ?>
    <div id="content">
        <h2>Modifier l'épisode</h2>

        <p>

            <?php
            // on crée la requête SQL
            $sql = ('SELECT c.nom cat_nom,downloads.nom,downloads.auteur,downloads.id,downloads.categorie,downloads.lien,downloads.lien2,downloads.lien3,downloads.torrentMQ,downloads.torrentHD,downloads.torrentFHD,downloads.screen,downloads.description
 FROM downloads 
 LEFT JOIN categorie c
 ON c.id=downloads.categorie
WHERE downloads.id=?');
            $db->pQuery($sql, array('i', $modifier));
            $data = $db->getRow(false, false);

            echo '<form name="Formdescription" enctype="multipart/form-data" id="Formdescription" method="post" action="modifier_dl.php" onSubmit="return false;">
<table width="350" border="0" style="border: #999 solid 1px ">
<input type="hidden" name="actif" value="1" />
  <tr>
    <th width="213" scope="row">Catégorie : </th>
    <td width="350">&nbsp;&nbsp;&nbsp;<select name="categorie">';

            $ids = $data['categorie'];
            $noms = $data['cat_nom'];
            echo '<option value="' . $ids . '">' . $noms . '</option>';


            $recherche = "SELECT nom,id FROM categorie WHERE id !=? ORDER BY nom";
            $db->pQuery($recherche, array('i', $ids));
            $vars = $db->getResults();
            foreach ($vars as $var) {
                $id = $var->id;
                $nom = $var->nom;

                echo '  <option value="' . $id . '">' . $nom . '</option>';
            }
            echo '</select></td>
  </tr>
  <tr>
    <th scope="row">Nom :</th>
    <td>*
      <input type="text" name="nom" value="' . $data['nom'] . '"/></td>
  </tr>
  <tr>
    <th scope="row">Nom de l\'épisode :</th>
    <td><textarea name="description" cols="30" rows="10">' . $data['description'] . '</textarea></td>
  </tr>
  <tr>
    <th scope="row">Auteur :</th>
    <td>*
      <input type="text" name="auteur" value="' . $data['auteur'] . '" /></td>
  </tr>
  <tr>
    <th scope="row">Lien MQ :</th>
    <td>
      <input type="text" name="lienMQ" value="' . $data['lien'] . '" size="50"/></td>
  </tr>
  <tr>
    <th scope="row">Lien HD :</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="lienHD" value="' . $data['lien2'] . '" size="50"/></td>
  </tr>
  <tr>
    <th scope="row">Lien FHD :</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="lienFHD" value="' . $data['lien3'] . '" size="50"/></td>
  </tr>
    </tr>
  <tr>
    <th colspan="2" scope="row">----------------------------------------Torrents----------------------------------------</th>
    </tr>
  <tr>
    <th width="400" scope="row">Torrent MQ:</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="torrentMQ" value="' . $data['torrentMQ'] . '" size="50"/></td>
  </tr>
    <tr>
    <th width="400" scope="row"> Torrent HD:</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="torrentHD" value="' . $data['torrentHD'] . '" size="50"/></td>
  </tr>
    <tr>
    <th width="400" scope="row"> Torrent FHD:</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="torrentFHD" value="' . $data['torrentFHD'] . '" size="50"/></td>
  </tr>
    <tr>
    <th scope="row">Lien du screen :</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="screen" value="' . $data['screen'] . '" size="50"/></td>
  </tr>
    <tr>
    <th scope="row">Uploader le screenshot :</th>
    <td>&nbsp;&nbsp;&nbsp; <input name="screen_image" type="file"/></td>
  </tr>
</table>
<input type="hidden" name="actif" value="1" />
<input type="hidden" name="mort" value="0" />
<input id="modifID" type="hidden" name="id" value="' . $modifier . '" />
<br>';
            if (isset($_GET['ajax'])) {
                echo '<input type="hidden" name="ajax" value="1" />';
            }
            if (isset($_GET['ajax'], $_GET['sortir'])) {
                echo '<input id="modifDl" type="button" name="envoi" value="Sortir l\'épisode" /></form><br><br>';
                SessionManager::getInstance()->sortir = 1;
            } else if (isset($_GET['ajax']))
                echo '<input id="modifDl" type="button" name="envoi" value="Modifier Download" /></form><br><br>';
            else
                echo '
<input type="submit" name="envoi" value="Modifier Download" onclick="if(valideForm()){document.Formdescription.submit()}"></form><br><br>
<a href="javascript:history.go(-1)">Retour</a>';

            echo '</form>';
            echo ' <div id="progress" style="display:none;">
        <div id="bar"></div >
        <div id="percent">0%</div >
    </div>';
            if (isset($_GET['ajax'])) {
                die('</div>');
            }
            ?>
        </p>
    </div>
    <div id="footer">
        <?php echo $close;
        unset($db); ?>
    </div>

    </div>
    </body>
    </html>
<?php
}
?>