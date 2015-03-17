<?php
require_once 'header.php';
include_once('../includes/addSerieXml.php');
include ('./templates/linksUp.html');
if (isset($_POST['del']))
{
        unset($db);
    $db = ezDB::getInstance();
    $db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
    $db->pQuery('DELETE FROM mirror_files WHERE `fileID`=?',array('s',$_POST['del']));
} else
{
    unset($db);
    $db = ezDB::getInstance();
    $db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
    $mirrors = $db->getResults('SELECT `fileID`,`name`,`downloads` FROM mirror_files ORDER BY name ASC');
    $display = '';
    $total = count($mirrors);
    $dls=0;
    foreach ($mirrors as $mir)
    {
        $dls+= $mir->downloads;
        $fichiers='<div id="miro_' .$mir->fileID . '"><a class="info" href="javascript:return false;" id="info_' .$mir->fileID . '">' . $mir->name . '</a></div><br />';

        $display.='<tr id="cellule_' . $mir->fileID . '"><td>' . $fichiers . '</td>
            <td>' . $mir->downloads . '</td>
<td><img src="./images/mod.gif" width="23" height="23" border="0" alt=""></a></td>
<td><a class="del" href="javascript:return false;" id="supp_' . $mir->fileID . '"><img src="supprimer.png" width="23" height="23" border="0" alt=""></a></td>
</tr>';
    }

?>
<script type="text/javascript">
    function select(element1) {
        // first set focus
        document.frm1.elements[element1].focus();
        // select all contents
        document.frm1.elements[element1].select();
    }
    function strtolower (str) {
        // http://kevin.vanzonneveld.net
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Onno Marsman
        // *     example 1: strtolower('Kevin van Zonneveld');
        // *     returns 1: 'kevin van zonneveld'

        return (str+'').toLowerCase();
    }
    $(document).ready(function () {

        $("a.del").live('click',function(){
            if(confirm("Êtes-vous sûr de vouloir supprimer ce mirror ?"))
            {
                var info = $(this).attr("id").substr(5);
                $.ajax({
                    type: "POST",
                    url: "SuMoMirror.php",
                    data: "del="+info,
                    success: function html(data){
                        $('#cellule_'+info).fadeOut("slow");
                    }
                });
            }
        });
        $("a.info").live('click',function(){

            var info = $(this).attr("id").substr(5);
            var file=$(this).text();
            $("#miro_"+info).html('<input id="lien" onclick="javacript:select(this);" type="text" size=85 readonly="readonly" value="http://www.gestdown.info/file/'+info+"/"+file+'"/>');

        });
    });</script>
<div id="content">
    <h2>Modifier/Supprimer des Mirror</h2>
    <p>
        Pour afficher le lien mirror, cliquer sur le nom du fichier, le lien apparaîtra.<br />
    <div style="display: none;" id="invisible"><br /><br /></div>
    <table width="720" height="21" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="450">Fichiers <br />(<?php echo $total ?>)</td>
             <td width="auto">Téléchargements  <br />(<?php echo $dls ?>)</td>
            <td width="44">Modifier</td>
            <td width="57">Supprimer</td>
        </tr>
        <?php echo $display; ?>
    </table>
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