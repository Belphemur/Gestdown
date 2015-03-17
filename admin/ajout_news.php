<?php
require_once 'header.php';
//require_once 'rss.php';
include ('./templates/links.html'); ?>
<div id="content">
			<h2>Ajouter une news</h2>
			<p>
<?php
if(isset($_POST['dl_id']) && isset($_POST['description']) && isset($_POST['title']))
{
	$num = $db->get_var("SELECT COUNT(news_id) FROM rss");
	if($num > 7)
	{
		$id=$db->get_var("SELECT MIN(news_id) FROM rss");
		$db->query("DELETE FROM rss WHERE news_id=$id");
	}
	foreach($_POST as $index => $valeur) 
	{
		$$index = trim($valeur);
	}
	$date=time();
        $description= str_replace('\r\n', PHP_EOL, $description);
	$db->query("INSERT INTO rss VALUES('','$title','$date','$description','$dl_id')");
	echo "News : $title <br /><br /> Ajoutée avec succès.";
	include 'gen_rss.php';
}
else
{
?>
    <form name="Formdescription" id="Formdescription" method="post" action="ajout_news.php" onSubmit="return false;">
            <table width="200" border="0" style="border: #999 solid 1px ">
  <tr>
    <th scope="row">Titre:</th>
    <td>*
      <input type="text"  size="60"name="title" /></td>
  </tr>
  <tr>
    <th scope="row">Description:</th>
    <td><script language="javascript">initBBcode('description','Prévisualiser',450,150,'',0); </script></td>
  </tr>
  <tr>
    <th scope="row">ID de l'épisode : (mettre 0 pour une news Générale(rapport avec la team, le site, etc ...))</th>
    <td>*
      <input type="text" name="dl_id" value="<?php if (isset($_GET['id'])) echo $_GET['id']; ?> " size="50"/></td>
  </tr>
</table>


 <br/>
  <p>
    <input type="submit" name="envoi" value="Ajouter News" onclick="if(valideForm()){document.Formdescription.submit()}">
  </p>
    </form><br><br>
<a href="javascript:history.go(-1)">Retour</a>
<?php
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