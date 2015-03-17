<?php
require_once 'header.php';
//require_once 'rss.php';
require_once ('../includes/host.php');
include ('./templates/links.html'); 
?>
<div id="content">
    <h2>Ajout d'un Episode</h2>
    <p>
        <?php
        if(isset($_POST['id']))
        {
            $id=$_POST['id'];
            $categorie = $_POST['categorie'];
            $nom="Episode ".$_POST['num'];
            $sql = "UPDATE downloads SET categorie='$categorie', nom='$nom' WHERE id='$id'";
            $cat_nom=$db->get_var("SELECT nom FROM categorie WHERE id=$categorie");
            $db->query($sql);
            echo "<br />L'",$nom," de ",$cat_nom," a &eacute;t&eacute; ajout&eacute; avec succès";
            $num = $db->get_var("SELECT COUNT(news_id) FROM rss");
            if($num > 7)
            {
                $id_n=$db->get_var("SELECT MIN(news_id) FROM rss");
                $db->query("DELETE FROM rss WHERE news_id=$id_n");
            }
            $db->query("INSERT INTO rss VALUES('','EPISODE','','','$id')");
            include 'gen_rss.php';
        }
        else
        {
            $msg_erreur = "Erreur. Les champs suivants doivent être obligatoirement remplis :<br/><br/>";
            $msg_ok = "Download Ajouté.<br />";
            $message = $msg_erreur;

            // vérification des champs
            if (empty($_POST['auteur']))
                $message .= "Auteur<br/>";
            if (empty($_POST['lienMQ']) && empty($_POST['lienHD']) && empty($_POST['lienFHD']))
                $message .= "Vous devez au moins mettre un de lien ddl <br/>";
            if(!empty($_POST['lienMQ']))
                if(!filter_var($_POST['lienMQ'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
                    $message .= "LienMQ invalide<br/>";
            if(!empty($_POST['lienHD']))
                if(!filter_var($_POST['lienHD'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
                    $message .= "LienHD invalide<br/>";
            if(!empty($_POST['lienFHD']))
                if(!filter_var($_POST['lienFHD'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
                    $message .= "LienFHD invalide<br/>";
            if(!empty($_POST['screen']))
                if(!filter_var($_POST['screen'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
                    $message .= "Lien du screen invalide<br/>";

            // si un champ est vie, on affiche le message d'erreur
            if (strlen($message) > strlen($msg_erreur))
            {

                echo $message;

                // sinon c'est ok
            }
            else
            {
                $date = time();
                foreach($_POST as $index => $valeur)
                {
                    $$index = trim($valeur);
                }
                if(!empty($lienMQ))
                    $host=host($lienMQ);
                else if(!empty($lienHD))
                    $host=host($lienHD);
                else
                    $host=host($lienFHD);
                if($host=='')
                {
                    $num=0;
                    $cat="Prob de lien";
                }
                else
                {
                    list($num,$cat)=linkInformations($host);
                }

                echo 'Série : ' .$cat.'<br>';
                $query= "SELECT id FROM categorie WHERE nom='$cat'";
                $resultat=$db->query($query);

                //Si la catégorie existe
                if($cat!="Prob de lien" && $resultat)
                {
                    $resultat=$db->get_var();
                    $categorie=$resultat;
                    $nom='Episode '.$num;
                    $sql = "INSERT INTO downloads VALUES ('', '$categorie', '$nom', '$date', '$description', '$auteur', '$lienMQ', '$lienHD','$lienFHD','$torrentMQ','$torrentHD','$torrentFHD','$screen', '1', '0','0')";
                    $res = $db->query($sql);
                    $query= "SELECT LAST_INSERT_ID() FROM downloads";
                    $dl=$db->get_var($query);
                    $nbre = $db->get_var("SELECT COUNT(news_id) FROM rss");
                    if($nbre > 7)
                    {
                        $id=$db->get_var("SELECT MIN(news_id) FROM rss");
                        $db->query("DELETE FROM rss WHERE news_id=$id");
                    }
                    $db->query("INSERT INTO rss VALUES('','EPISODE','','','$dl')");
                    include 'gen_rss.php';

                    $bbcode_lien_template='<textarea name="" cols="86" rows="10" readonly="readonly">[color=orange]'.stripcslashes($description).'[/color]'."\n\n".'[ALIGN=center][img]'.$screen.'[/img][/ALIGN]'."\n\n".'%s</textarea>';
                    $bbcode_lien="[u][color=red]".$cat." ".$num."[/color][/u]"."\n";

                    if(!empty($lienMQ))
                        $bbcode_lien.= 'MQ : [url='.$url_site.'dl-'.$dl.'-mq.html]GestDown Mirror MQ[/url]'."\n";

                    if(!empty($lienHD))
                        $bbcode_lien.= 'HD : [url='.$url_site.'dl-'.$dl.'-hd.html]GestDown Mirror HD[/url]'."\n";

                    if(!empty($lienFHD))
                        $bbcode_lien.= 'FHD : [url='.$url_site.'dl-'.$dl.'-fhd.html]GestDown Mirror FHD [/url]'."\n";

                    if(!empty($torrentMQ))
                        $bbcode_lien.=  "[b][u] Liens Torrent[/u][/b] \n".'Torrent MQ : [url='.$url_site.'tor-'.$dl.'-mq.html]Torrent MQ[/url]'."\n";
                    if(!empty($torrentHD))
                        $bbcode_lien.= 'Torrent HD : [url='.$url_site.'tor-'.$dl.'-hd.html]Torrent HD[/url]'."\n";
                    if(!empty($torrentFHD))
                        $bbcode_lien.= 'Torrent FHD : [url='.$url_site.'tor-'.$dl.'-fhd.html]Torrent FHD[/url]'."\n";

                    echo $msg_ok,'Voici les liens BBCODE à mettre dans la news : <br />';
                    printf($bbcode_lien_template,$bbcode_lien);
                }
                else
                {
                    /*$sql = "INSERT INTO categorie VALUES ('', '$cat', 'En Construction', '".$url_site."/images/vide.png',0,0)";
		$res = $db->query($sql);
		$query= "SELECT LAST_INSERT_ID() FROM categorie";
		$categorie=$db->get_var($query);*/
                    $query= "SELECT id FROM categorie WHERE nom='Prob de lien'";
                    $categorie=$db->get_var($query);
                    $sql = "INSERT INTO downloads VALUES ('', '$categorie', 'EPISODE A MODIFIER', '$date', '$description', '$auteur', '$lienMQ', '$lienHD','$lienFHD','$torrentMQ','$torrentHD','$torrentFHD','$screen', '1', '0','0')";
                    $res = $db->query($sql);
                    $query= "SELECT LAST_INSERT_ID() FROM downloads";
                    $dl=$db->get_var($query);
                    ?>
        Introduisez ci-dessous les informations manquantes:
    <p>Si la s&eacute;rie ne se trouve pas dans la liste cliquer sur Annuler, vous retrouvez alors votre &eacute;pisode dans la <br />
			cat&eacute;gorie &quot;Prob de lien&quot; o&ugrave; vous pourrez le modifier, une fois la s&eacute;rie cr&eacute;&eacute;e.<form action="add_download2.php" method="post" enctype="application/x-www-form-urlencoded" name="correction" target="_self">
        <table width="309" height="57" border="0">
            <tr>
                <th width="174" align="left" valign="middle" scope="row">      S&eacute;rie :</th>
                <td width="125"><select name="categorie">
                                    <?php
                                    $recherche="SELECT nom,id FROM categorie ORDER BY nom";
                                    $vars = $db->get_results($recherche);
                                    foreach($vars as $var)
                                    {
                                        $id = $var['id'];
                                        $nom = $var['nom'];

                                        echo'	<option value="'.$id.'">'.$nom.'</option>';

                                    }
                                    ?>
                    </select></td>
            </tr>
            <tr>
                <th align="left" valign="middle" scope="row">Numéro de l'épisode : </th>
                <td><input name="num" type="text" size="3" maxlength="2" /></td>
            </tr>
        </table>
        <input name="id" type="hidden" value="<?php echo $dl; ?>" />
        <br />
        <input name="Annuler" type="button" value="Annuler" onclick="javascript:history.go(-1)" />
        <input type="submit" name="button" id="button" value="Envoyer" />

    </form>

                <?php
            }



        }
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