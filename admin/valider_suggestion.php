<?php

if (isset($_POST['valider']))
{
    require_once 'conf.php';
    $db->use_disk_cache = false;
    $db->cache_queries = false;
    include("login.php");
    login();
    $res=('SELECT download,resume,auteur,screen FROM descriptions WHERE id=' . $_POST['valider']);
    $result=$db->get_row($res);
    if($result['screen']!="")
        $db->query('UPDATE downloads SET description="'.$result['resume'].'", auteur="'.$result['auteur'].'", screen="'.$result['screen'].'" WHERE id='.$result['download']);
    else
        $db->query('UPDATE downloads SET description="'.$result['resume'].'", auteur="'.$result['auteur'].'" WHERE id='.$result['download']);
    $db->query('DELETE FROM descriptions WHERE mort=0 AND id=' . $_POST['valider']);
    unset($db);
    die('Suggestion acceptée avec succès');

}
if (isset($_POST['supprimer']))
{
    require_once 'conf.php';
    $db->use_disk_cache = false;
    $db->cache_queries = false;
    include("login.php");
    login();
    $db->query('DELETE FROM descriptions WHERE id=' . $_POST['supprimer']);
    unset($db);
    die('Suggestion supprimée avec succès');
}
require_once 'header.php';
require('../includes/bbcode.php');
include ('./templates/links.html');
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("a.supprimer").live('click',function(){
            var num = $(this).attr("id").substr(5);

            $.ajax({
                type: "POST",
                url: "valider_suggestion.php",
                data: "supprimer="+num,
                success: function html(data){
                    // Si l'ajout est réussi, afficher un message de réussite
                    $('#cellule_'+num).fadeOut("slow");
                }
            });
            return false;
        });

        $("a.valider").live('click',function(){
            var num = $(this).attr("id").substr(6);

            $.ajax({
                type: "POST",
                url: "valider_suggestion.php",
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
    <h2>Synopsis proposés :</h2>
    <p>
    <table width="719" height="27" border="1">
        <tr>
            <td width="64">Série</td>
            <td width="75">Nom</a></td>
            <td width="266">Description</td>
            <td width="65">Auteur</td>
            <td width="37">Suppr</td>
            <td width="43">OK</td>
        </tr>
        <?php
        $sql = 'SELECT descriptions.id,descriptions.resume,descriptions.auteur,categorie.nom cnom,downloads.nom,descriptions.screen
FROM descriptions
RIGHT JOIN downloads
ON downloads.id=descriptions.download
LEFT JOIN categorie
ON downloads.categorie=categorie.id
WHERE descriptions.mort =0';
        $datas = $db->get_results($sql);
        if($datas)
        {
// on fait une boucle qui va faire un tour pour chaque enregistrement
            foreach($datas as $data)
            {
                $resume=replacement($data['resume']);
                echo'
	<tr id="cellule_'.$data['id'].'"><td>'.$data['cnom'].'</td>
   <td>'.$data['nom'];
                if($data['screen']!='')
                    echo' <br><img src="'.$data['screen'].'" alt="" width="200" height="auto" />';
                echo '</td>
	<td>'.$resume.'</td>
   <td>'.$data['auteur'].'</td>
   <td><a class="supprimer" href="javascript:return false;" id="supp_'.$data['id'].'"><img src="supprimer.png" width="23" height="23" border="0" alt=""></a></td>
   <td><a class="valider" href="javascript:return false;" id="valid_'.$data['id'].'"><img src="ok.jpg" width="9" height="9" border="0" alt=""></a></td>
	</tr>
	';
            }
        }
        unset($db);
        ?></table>

</div>
<div id="footer">
    <?php echo $close; ?>
</div>

</div>
</body>
</html>