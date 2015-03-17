<?php
require_once 'header.php';
include ('./templates/linksUp.html');
include('../includes/addSerieXml.php');
if(isset($_POST['OK']) && $_POST['OK']=='Envoyer')
{
    $url=$_POST['url'];
    $txt=false;
    if(isset($_POST['txt'])&& !empty($_POST['txt']))
        $txt=str_replace('\r\n', PHP_EOL, $_POST['txt']);
    $result=autoXml($url,$txt);
    $msg='Voici vos liens par fichier:<br />
        <h3>Cliquer sur le champ pour sélectionner le lien mirror.</h3><br />';
    foreach($result as $file => $link)
    {
        $info=explode(',', $link);
        $msg.='<label class="file">'.$file.' :</label><input name="'.$file.'" onclick="javacript:select(\''.$file.'\');" type="text" size=65 readonly="readonly" value="http://www.gestdown.info/'.$info[0].'"/><span style="color:orange">'.$info[1].'</span><br />';
    }
}
?>
<style type="text/css">
    label
    {
        color: red;
        font-weight:bold;
        display: inline;
        float: left;
        width: 150px;
    }
    label.file
    {
        width: 290px;
    }
</style>
<script type="text/javascript">

function select(element1) {
// first set focus
document.frm1.elements[element1].focus();
// select all contents
document.frm1.elements[element1].select();
}
</script>
<div id="content">
    <h2>Automatisation du lien Mirror</h2>
    <p style="width:600px;">
        Le lien mirror est créé automatiquement à partir du fichier texte dont l'url est ci-dessous.<br />
        Ne changez cet url que si vous savez ce que vous faites.<br /><br />
        Vous pouvez aussi mettre manuellement les info du fichier txt dans l'emplacement "Texte" et ce sera ce champ qui sera pris en compte pour la création et/ou la mise à jour du mirror.<br />
    <form name="mirror" id="mirror" action="autoMirror.php" method="POST">
        <label>Url :</label><input type="text" size=75 name="url" value="http://imagidream.eu/upload/files/myuploads.txt"/><br /><br />
        <label>Texte :</label><textarea name="txt" cols="65" rows="10"></textarea>
        <div style="text-align: left;"><input name="OK" type="submit" value="Envoyer" />  </div>
    </form>
    <?php echo isset($msg)?$msg:'';?>

</div>
<div id="footer">
    <?php echo $close; ?>
</div>

</div>
</body>
</html></p>
