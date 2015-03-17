<?php
if (isset($_POST['supprimer'])) {
    require_once 'conf.php';
    $db->use_disk_cache = false;
    $db->cache_queries = false;
    include("login.php");
    login();
    $db->query('DELETE FROM downloads WHERE id=' . $_POST['supprimer']);
    $db->query("DELETE FROM rss WHERE dl_id=" . $_POST['supprimer']);

    unset($db);
    die();
}
require_once 'header.php';
require('../includes/bbcode.php');
include ('./templates/links.html');
?>
<script type="text/javascript">
    function dataHtml(data){
        // Si l'ajout est réussi, afficher un message de réussite
        $('#cellule_'+ep_num).html(data);
        $('#cellule_'+ep_num).show('slow');
        setTimeout(function() {
            $('#cellule_'+ep_num).fadeOut('slow');
            $('#cellule_'+ep_num).html($('#cache_'+ep_num).html());
            $('#cache_'+ep_num).remove();
            setTimeout($('#cellule_'+ep_num).fadeIn(),750);

        },8000);
    }</script>

<div id="content">
    <h2>Modifier/Supprimer des Episodes</h2>
    <div id="resultsContainer" style="display: block;">
        <table width="720" height="21" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50">Série</td>
                <td width="77">Nom</td>
                <td width="300">Description</td>
                <td width="44">Modifier</td>
                <td width="57">Supprimer</td>
            </tr>
            <?php
// on crée la requête SQL
            $sql = 'SELECT c.nom cat_nom,downloads.nom,downloads.id,downloads.screen,downloads.description
 FROM downloads 
 INNER JOIN categorie c
 ON c.id=downloads.categorie
 ORDER BY c.nom ASC, downloads.id ASC';

            $datas = $db->get_results($sql);
            if ($datas) {
// on fait une boucle qui va faire un tour pour chaque enregistrement
                foreach ($datas as $data) {
                    $cat = $data['cat_nom'];
                    $desc = replacement($data['description']);
//affichage de l'entête du tableau html avec les noms des champs
                    echo'
<tr width="720" id="cellule_' . $data['id'] . '">
	<td>' . $cat . '</td>
   <td>' . $data['nom'];
                    if ($data['screen'] != '')
                        echo' <br><img src="' . $data['screen'] . '" alt="" width="150" height="auto" />';
                    echo' </td><td>' . $desc . '</td>
   <td><a class="modifier" href="javascript:void(0);"  id="modif_' . $data['id'] . '"><img src="./images/mod.gif" width="23" height="23" border="0" alt=""></a></td>
   <td><a class="supprimer" href="javascript:return false;" id="supp_' . $data['id'] . '"><img src="supprimer.png" width="23" height="23" border="0" alt=""></a></td>
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
<div id="cache" style="display:none;"></div>
</div>
</body>
</html>