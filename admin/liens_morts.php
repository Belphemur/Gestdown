<?php
if (isset($_POST['valider']))
{
    require_once 'conf.php';
    include("login.php");
    login();
    $db->query('UPDATE `downloads` SET `mort`=0 WHERE `id`=' . $_POST['valider']);
    $db->query('DELETE FROM `descriptions` WHERE `mort`!=0 AND download=' . $_POST['valider']);
    unset($db);
    die('Supprimé des liens morts');
}
require_once 'header.php';
include ('./templates/links.html'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("a.supprimer").live('click',function(){
            var num = $(this).attr("id").substr(5);

            $.ajax({
                type: "POST",
                url: "liens_morts.php",
                data: "valider="+num,
                success: function html(data){
                    // Si l'ajout est réussi, afficher un message de réussite
                    $('#cellule_'+num).fadeOut("slow");
                }
            });
            return false;
        });
    });</script>
<div id="content">
    <h2>Liens morts signalés</h2>
    <p>
    <table width="719" height="27" border="1">
        <tr>
            <td width="64">Série</td>
            <td width="75">Nom</a></td>
            <td width="123">liens</td>
            <td width="266">Description</td>
            <td width="65">Modifier</td>
            <td width="43">en VIE</td>
        </tr>
        <?php
        $sql = 'SELECT categorie.nom cat_nom,downloads.nom,downloads.categorie,downloads.id,downloads.lien,downloads.lien2,downloads.lien3,downloads.screen,descriptions.resume
FROM descriptions,downloads
INNER JOIN categorie
ON categorie.id=downloads.categorie
WHERE downloads.id=descriptions.download AND descriptions.mort =1
ORDER BY categorie.id,downloads.id ASC';

        $datas= $db->get_results($sql);
        if($datas)
        {
// on fait une boucle qui va faire un tour pour chaque enregistrement
            foreach($datas as $data)
            {
                $cat=$data['cat_nom'];

//affichage de l'entête du tableau html avec les noms des champs
                echo'
<tr id="cellule_'.$data['id'].'"><td>'.$cat.'</td>
   <td>'.$data['nom'];
                if($data['screen']!='')
                    echo' <br><img src="'.$data['screen'].'" alt="" width="200" height="auto" />';
                echo '</td>
   <td> <a href="'.$data['lien'].'" title="MQ" target="_blank">MQ</a>&nbsp;&nbsp;&nbsp;';
                if($data['lien2']!='')
                    echo '<a href="'.$data['lien2'].'" title="HD" target="_blank">HD</a>&nbsp;&nbsp;&nbsp;';
                if($data['lien3']!='')
                    echo '<a href="'.$data['lien3'].'" title="FHD" target="_blank">FHD</a>';
                echo '</td><td>'.$data['resume'].'</td>
   <td><a href="modifier_dl.php?modifier='.$data['id'].'"><img src="./images/mod.gif" width="23" height="23" border="0" alt=""></a></td>
   <td><a class="supprimer" href="javascript:return false;" id="supp_'.$data['id'].'"><img src="ok.jpg" width="9" height="9" border="0" alt=""></a></td>
</tr>
';
            }
        }
// on ferme la connexion à mysql
        unset($db);
        ?></table>
</p>
</div>
<div id="footer">
    <?php echo $close; ?>
</div>

</div>
</body>
</html>