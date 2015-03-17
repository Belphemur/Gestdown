<?php
require_once 'header.php';
require('../includes/bbcode.php');
include ('./templates/links.html');
?>
<script type="text/javascript">
    function send(type,id,name)
    {
        var txt='abandonner';
        if(type=='lic')
            txt='licencier';
        else if (type=='suppr')
            txt='supprimer';

        if(confirm("Voulez-vous "+txt+" "+name+" ?"))
        {
            $.ajax({
                type: "POST",
                url: "suppr_mod_Categorie.php",
                data: "ok="+id+"&type="+type,
                success: function html(data){
                    // Si l'ajout est réussi, afficher un message de réussite
                    $("body").html(data);
                }
            });
        }
    }
</script>
<?php
 if (isset($_POST['ok']) && isset($_POST['type']))
{
    if($_POST['type']=="suppr")
    {
        $db->query('DELETE FROM downloads WHERE categorie=' . $_POST['ok']);
        $db->query('DELETE FROM categorie WHERE id=' . $_POST['ok']);
    }
    else if($_POST['type']=="lic")
    {
        $db->query('DELETE FROM downloads WHERE categorie=' . $_POST['ok']);
        $db->query('UPDATE `categorie` SET `licencie` = 1 WHERE `categorie`.`id` ='.$_POST['ok'].' LIMIT 1');
    }
    else if($_POST['type']=="stop")
        $db->query('UPDATE `categorie` SET `stopped` = 1 WHERE `categorie`.`id` ='.$_POST['ok'].' LIMIT 1');

}
?>
<div id="content">
    <h2>Modifier/Supprimer des Séries</h2>
    <p>
    <table width="720" height="21" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="107" height="19">Nom</td>
            <td width="371">Description</td>
            <td width="64">Modifier</td>
            <td width="48">Suppr</td>
            <td width="48">Licencier</td>
            <td width="48">Abandonnée</td>
        </tr>
        <?php

// on crée la requête SQL
        $sql = 'SELECT * FROM categorie ORDER BY stopped ASC ,licencie ASC, finie ASC ,nom ASC';
        $datas = $db->get_results($sql);
        if($datas)
        {
// on fait une boucle qui va faire un tour pour chaque enregistrement
            foreach($datas as $data)
            {
                $desc=replacement($data['description']);
//affichage de l'entête du tableau html avec les noms des champs
                echo'
<tr id="'.$data['id'].'">
   <td>'.$data['nom'];
                if($data['finie'])
                    echo'<br><font color=red>(Série Terminée)</font>';
                else if($data['licencie'])
                    echo'<br><font color=royalblue>(Série Licenciée)</font>';
                else if($data['stopped'])
                    echo'<br><font color=orangered>(Série Abandonnée)</font>';
                echo '<br>
   	<img src="'.$data['image'].'" alt="" width="200" height="auto" /></td>
   <td>'.$desc.'</td>';
                if($data['nom']=="Prob de lien")
                    echo '<td></td>';
                else
                    echo '<td><a href="modifier_cat.php?modifier='.$data['id'].'"><img src="./images/mod.gif" width="23" height="23" border="0" alt=""></a></td>';
                if($data['nom']=="Prob de lien")
                    echo '<td></td>';
                else
                    echo '<td><a href="javascript:send(\'suppr\','.$data['id'].',\''.$data['nom'].'\');"><img src="supprimer.png" width="23" height="23" border="0" alt=""></a></td>';
                if($data['licencie'])
                    echo'<td></td>';
                else
                    echo'<td><a href="javascript:send(\'lic\','.$data['id'].',\''.$data['nom'].'\');"><img src="images/licence.jpg" width="23" height="23" border="0" alt=""></a></td>';
                if($data['stopped'])
                    echo'<td></td>';
                else
                    echo'<td><a href="javascript:send(\'stop\','.$data['id'].',\''.$data['nom'].'\');"><img src="images/stop.jpg" width="23" height="23" border="0" alt=""></a></td>';
                echo'
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