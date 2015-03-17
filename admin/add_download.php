<?php
require_once 'header.php';
$pseudo=$_SESSION['pseudo'];
include ('./templates/links.html'); ?>
<div id="content">
			<h2>Ajouter un Episode</h2>
			<p>
<form name="Formdescription" id="Formdescription" method="post" action="add_download2.php" onSubmit="return false;">
<table width="550" border="0" style="border: #999 solid 1px ">
  <tr>
    <th width="400" scope="row">Description :</th>
    <td width="322">&nbsp;&nbsp;&nbsp;<script language="javascript">initBBcode('description','Prévisualiser',450,150,''); </script></td>
  </tr>
  <tr>
    <th width="400" scope="row">Auteur :</th>
    <td>*
      <input type="text" name="auteur" value="<?php echo $pseudo; ?>" /></td>
  </tr>
  <tr>
    <th width="400" scope="row">Lien MQ :</th>
    <td>
      &nbsp;&nbsp;&nbsp;<input type="text" name="lienMQ" size="50" /></td>
  </tr>
  <tr>
    <th width="400" scope="row">Lien HD :</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="lienHD" size="50"/></td>
  </tr>
  <tr>
    <th width="400" scope="row">Lien FHD :</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="lienFHD" size="50"/></td>
  </tr>
  <tr>
    <th colspan="2" scope="row">----------------------------------------Torrents----------------------------------------</th>
    </tr>
  <tr>
    <th width="400" scope="row">Torrent MQ:</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="torrentMQ" size="50"/></td>
  </tr>
    <tr>
    <th width="400" scope="row"> Torrent HD:</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="torrentHD" size="50"/></td>
  </tr>
    <tr>
    <th width="400" scope="row"> Torrent FHD:</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="torrentFHD" size="50"/></td>
  </tr>
    <tr>
    <th width="400" scope="row">Lien du screen :</th>
    <td>&nbsp;&nbsp;&nbsp;<input type="text" name="screen" size="50"/></td>
  </tr>
</table>
<br>
<input type="submit" name="envoi" value="Ajouter Download" onclick="if(valideForm()){document.Formdescription.submit()}"></form><br><br>
<a href="javascript:history.go(-1)">Retour</a>
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>