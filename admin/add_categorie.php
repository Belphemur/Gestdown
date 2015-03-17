<?php
require_once 'header.php';
include ('./templates/links.html'); ?>
<div id="content">
			<h2>Ajouter une Série</h2>
			<p>
    <form name="Formdescription" id="Formdescription" method="post" action="add_categorie2.php" onSubmit="return false;">
            <table width="200" border="0" style="border: #999 solid 1px ">
  <tr>
    <th scope="row">Nom:</th>
    <td>*
      <input type="text" name="nom" /></td>
  </tr>
  <tr>
    <th scope="row">Description:</th>
    <td><script language="javascript">initBBcode('description','Prévisualiser',450,150,'',0); </script></td>
  </tr>
  <tr>
    <th scope="row">Url de l'image:</th>
    <td>*
      <input type="text" name="image" size="50"/></td>
  </tr>
</table>


 <br/>
  <p>
    <input type="submit" name="envoi" value="Ajouter Catégorie" onclick="if(valideForm()){document.Formdescription.submit()}">
  </p>
    </form><br><br>
<a href="javascript:history.go(-1)">Retour</a>
</p>
		</div>
<div id="footer">
		<?php echo $close; ?> 
	</div>
	
</div>
</body>
</html>