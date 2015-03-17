<?php
require_once 'header.php';
require('../includes/bbcode.php');
include ('./templates/linksUp.html');
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->query("SET NAMES 'utf8'");?>
<script type="text/javascript">
    sortir=true;
    function dataHtml(data){
                    // Si l'ajout est réussi, afficher un message de réussite
                    $('#cellule_'+ep_num).html(data);
                    $('#cellule_'+ep_num).show('slow');
                }
   </script>

<div id="content">
    <h2>Episodes à sortir</h2>
    <p>Pour sortir un épisode c'est très simple :<br />
        Il vous suffit de cliquer sur l'icone dans la liste et de remplir les champs <span style="color: red;">Nom de l'épisode ET Lien du screen</span>.<br />
        Ensuite cliquer sur le bouton sortir l'épisode et les balises bbcode à mettre dans la news vous seront donné.
    </p>
    <div  id="resultsContainer" style="display: block;">
        <table width="720" height="21" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50">Série</td>
                <td width="77">Nom</td>
                <td width="300">Description</td>
                <td width="44">Sortir</td>
                <td width="57">Supprimer</td>
            </tr>
            <?php
// on crée la requête SQL
            $sql = 'SELECT c.nom cat_nom,downloads.nom,downloads.id,downloads.screen,downloads.description
 FROM downloads
 INNER JOIN categorie c
 ON c.id=downloads.categorie
 WHERE downloads.actif=0
 ORDER BY c.nom ASC, downloads.id ASC';

            $datas = $db->getResults($sql,false);
            if($datas)
            {
// on fait une boucle qui va faire un tour pour chaque enregistrement
                foreach($datas as $data)
                {
                    $cat=$data['cat_nom'];
                    $desc=replacement($data['description']);
//affichage de l'entête du tableau html avec les noms des champs
                    echo'
<tr id="cellule_'.$data['id'].'">
	<td>'.$cat.'</td>
   <td>'.$data['nom'];
                    if($data['screen']!='')
                        echo' <br><img src="'.$data['screen'].'" alt="" width="200" height="auto" />';
                    echo' </td><td>'.$desc.'</td>
   <td><a class="modifier" href="javascript:void(0);"  id="modif_'.$data['id'].'"><img src="./images/mod.gif" width="23" height="23" border="0" alt=""></a></td>
   <td><a class="supprimer" href="javascript:return false;" id="supp_'.$data['id'].'"><img src="supprimer.png" width="23" height="23" border="0" alt=""></a></td>
</tr>
';
                }
            }
// on ferme la connexion à mysql
            unset($db);
            ?></table>
    </div>
</div>
<div id="footer">
    <?php echo $close; ?>
</div>

</div>
</body>
</html>