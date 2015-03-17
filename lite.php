<?php
require_once("admin/conf.php");
$datas=$db->get_results("SELECT nom,finie FROM categorie WHERE nom!='Prob de lien' AND licencie!=1 ORDER BY nom ASC");
$finies="";
$cours="";
foreach ($datas as $serie)
	{
		if($serie['finie'])
			$finies.="<a href=\"javascript:change('".$serie['nom']."')\" title='".$serie['nom']."'>".$serie['nom']."</a><br />\n";
		else
			$cours.="<a href=\"javascript:change('".$serie['nom']."')\"  title='".$serie['nom']."'>".$serie['nom']."</a><br />\n";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Version Lite de Gestdown</title>
<script src="js/jquery-1.3.2.min.js" type="text/javascript" ></script>
<script src="js/jquery-ui-1.7.custom.min.js" type="text/javascript" ></script>
<script src="js/controller.js" type="text/javascript" ></script>
<script type="text/javascript">
function change(txt) {
	obj=document.getElementById("searchbox");
	obj.value=txt;
	$.get("search.php",{query: txt, type: "results"}, function(data){
		
	$("#resultsContainer").html(data);
	$("#resultsContainer").show("blind");
	});
}
</script>
<link href="templates/live.css" rel="stylesheet" type="text/css" />
<link href="templates/projets.css" rel="stylesheet" type="text/css" />
<link href="templates/rotator.css" rel="stylesheet" type="text/css" />
</head>
<body class="thrColHybHdr">

<div id="container">
  <div id="header">
  	<!-- fin de #header -->
  </div>
  <div id="sidebar1">
  <div id="class_list">
  <div class="separator_m1">
		<h3>| Séries en Cours</h3></div>

<?php echo $cours;?>

<div class="separator_m">
	<h3>| Séries Terminés</h3></div>
<?php echo $finies;?>

</div>
        
  <!-- fin de #sidebar1 --></div>

  <div id="mainContent">
  <div class="gauche">
  <div class="droit">
  <div class="haut">
   <div>
   	<h2>| Recherche</h2></div>
  </div><!-- /haut -->
<div style="color:#CCC">Vous voici sur la version (super) Lite du site, pour ceux qui aurait quelques problèmes.<br  />
Bon cette "version" se résume pour le moment à une simple fonction recherche <br  />
Le fonctionnement est SUPER simple :Soit vous cliquez sur le nom de la série a gauche<br />
Soit vous tapez le nom de la série (ou juste un bout (exemple ghost pour 07-Ghost, umineko pour Umineko no Naku Koro ni))<br />
Si vous voulez en plus un épisode en particulier, ajouter simplement un <span style="color:#C00; font-weight:bold;">+</span> entre le nom de la série et l'épisode (exemple pandora+4 vous donnera l'épisode 4 de Pandora Hearts)<br /><br /></div>
 <div id="form">
		  <input type="text" id="searchbox" name="searchbox" value="pandora+4 -> l'ep 4 de Pandora" size="255"/>
		  <div id="buttonContainer">
				<a class="button" id="submitbutton" href="#"><span id="buttontext">Rechercher</span></a>
	  </div>
		
		<div style="text-align:center;" id="resultsContainer"></div><br />
  </div><!-- /droit -->
 </div><!-- /gauche -->


</div>
	<!-- Cet élément de suppression doit suivre immédiatement l'élément div #mainContent afin de forcer l'élément div #container à contenir tous les éléments flottants enfants --><br class="clearfloat" />

<!-- fin de #container --></div>
   <div id="footer">
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9163128-1");
pageTracker._trackPageview();
} catch(err) {}</script>
  <!-- fin de #footer --></div>
</body>