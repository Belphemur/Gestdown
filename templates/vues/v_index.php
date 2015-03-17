<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->titre; ?></title>
<meta name="TITLE" content="Centralisation des épisode de la Ame no Tsuki" />
<meta name="AUTHOR" content="Balor" />
<meta name="SUBJECT" content="Toutes les séries de la Ame no Tsuki avec leurs épisodes et leurs lien de téléchargement." />
<meta name="DESCRIPTION" content="<?php echo $this->meta_desc; ?>" />
<meta name="KEYWORDS" content="ame no tsuki, mangas, anime, japon, fansub, téléchargement, projets, <?php echo $this->meta_tag_serie; ?>" />
<meta name="REVISIT-AFTER" content="5 DAYS" />
<meta name="LANGUAGE" content="FR" />
<meta name="OWNER" content="contact@imagidream.eu" />
<meta name="ROBOTS" content="All" />
<meta name="RATING" content="Fansub" />
<meta name="google-site-verification" content="u5EBaJ0m7q4fc-P3XpHv1qbduymAfNqcEuCJoMJ88kE" />
<meta name="msvalidate.01" content="2014CE1E3D3BAD4B6218115A64DBD92F" />
<META name="y_key" content="7f35830ae93a5f5f">
<link rel="alternate" type="application/rss+xml" title="Gestdown (RSS 2.0)" href="http://feeds.feedburner.com/GestdownSortiesDeLaAmeNoTsuki?format=xml" />
<link rel="shortcut icon" href="templates/img/favicon.ico" />
<link rel="apple-touch-icon" href="templates/img/apple-touch-icon.png" />
<link rel="stylesheet" href="templates/projets_min.css,rotator.css,stars.css" />
<!-- js/markitup! skin -->
<link rel="stylesheet" type="text/css" href="http://js.gestdown.info/markitup/skins/markitup/style.css" />
<!--  js/markitup! toolbar skin -->
<link rel="stylesheet" type="text/css" href="http://js.gestdown.info/markitup/sets/bbcode/style.css" />
<script src="http://js.gestdown.info/jquery-1.4.2.min.js,rating.js,ajax.js?310310"></script>
<!-- js/markitup! -->
<script type="text/javascript" src="http://js.gestdown.info/markitup/jquery.markitup.pack.js"></script>
<!-- js/markitup! toolbar settings -->
<script type="text/javascript" src="http://js.gestdown.info/markitup/sets/bbcode/set.js"></script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9163128-1");
pageTracker._setDomainName("www.gestdown.info");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php echo $this->header_js; ?>
<!--[if IE]>
<style type="text/css"> 
/* placer les corrections pour toutes les version d'IE dans ce commentaire conditionnel */
/*.thrColHybHdr #sidebar1, .thrColHybHdr #sidebar2 { padding-top: 30px; }*/
.thrColHybHdr #mainContent { zoom: 1; padding-top: 15px; }
/* la propriété propriétaire zoom ci-dessus transmet à IE l'attribut hasLayout nécessaire pour éviter plusieurs bogues */
</style>
<![endif]-->
</head>

<body class="thrColHybHdr">
  <div id="header">
<div id="toaru">
<div id="eleven">
<div id="container">




  <div id="sidebar1">
  <div class="separator_m1">
	<h3>| Liens</h3></div>
    <div id="liens_menu">
    <a href="http://www.ame-no-tsuki.fr" title="Retour au site de la team" target="_self">| Accueil |</a><br />
    <a href="projets.php" title="Projets de la team" target="_self">| Projets |</a><br />
     <a href="lite.php" title="Version super lite" target="_self">| VERSION LITE |</a><br />
    <a id="admin">| Panel d'Admin |</a></div>
    <?php if(!$this->admin)
	{?>
   <p> <div class="invisible" id="admin_conn">
    <form action="index.php" method="post" enctype="application/x-www-form-urlencoded" name="identification" target="_self">
    <label class="ident">Login:</label><br /><input name="userName" type="text" size="20" /><br />
    <label class="ident">Mdp:</label><br /><input name="password" type="password" size="20" /><br />
    <input name="do_login" type="hidden" value="1" />
  	<input name="input" type="submit" value="Connexion" class="submit"  /></form><br />
    <label class="ident"> Mot de passe perdu ?</label> <a id="mdp_perdu" title="Récupérer son mot de passe">cliquer ici</a>
    <div class="invisible" id="admin_mdp">
    <form action="admin/recup.php" method="post" enctype="application/x-www-form-urlencoded" name="R&eacute;cup&eacute;ration mdp" target="_blank">
     <label class="ident">Adresse Mail:</label><input name="mail" type="text" size="30" /><br />
     <input name="input" type="submit" value="Envoyer" class="submit"  />
     </form>
     </div>
    </div></p>
    <?php
    }
	else
	{
    ?>
    <div id="admin_conn">
    <br /><a href="./admin/index.php" target="_blank">Panel d'admin</a><br />
    <a href="javascript:na_open_window('episode', './admin/add_download.php', 0, 0, 950, 750, 0, 0, 0,1 , 1)">Ajouter un épisode</a><br />
    <a href="javascript:na_open_window('episode', './admin/avancement.php', 0, 0, 950, 750, 0, 0, 0,1 , 1)">Avancement</a><br />
    <a href="javascript:na_open_window('episode', './admin/ajout_news.php?id=0', 0, 0, 950, 750, 0, 0, 0,1 , 1)">Newser</a><br />
    <a href="javascript:na_open_window('episode', './admin/edit_news.php', 0, 0, 950, 750, 0, 0, 0,1 , 1)">Editer la première news</a><br />
        <br /><a href="index.php?logout" title="Se déconnecter" class="LienMenuGauche" target="_self">Deconnexion</a>
    </div>
    <?php
	}
	?>
    <p><div style="text-align:center; margin-top: 20px;"><a href="http://feeds.feedburner.com/GestdownSortiesDeLaAmeNoTsuki?format=xml" target="_blank"><img src="http://feeds.feedburner.com/~fc/GestdownSortiesDeLaAmeNoTsuki?bg=000099&amp;fg=33CC99&amp;anim=1" height="26" width="88" style="border:0" alt="" /></a>
    </div></p>
  <div id="class_list">
  <br /><p><a rel="0" title="index" class="" id="class_nav_1">Index</a></p><br />
  <p><a rel="-1" href="avancement.html" onclick="javascript:return false;" title="Avancement" class="" id="class_nav_2">Avancement</a></p><br />
  <div class="separator_m">
		<h3>| Séries en Cours</h3></div>

<p><?php echo $this->cours;?></p>
	 <div id="menu">
<div class="separator_m">
	<h3>| Séries Terminées</h3></div>
<p><?php echo $this->finies;?></p>

<div class="separator_m">
	<h3>| Shoutbox</h3></div>
<div style="text-align:center;"> <object width="200" height="500" id="obj_1244648174251"><param name="movie" value="http://ant-chat.chatango.com/group"/><param name="wmode" value="transparent"/><param name="AllowScriptAccess" value="always"/><param name="AllowNetworking" value="all"/><param name="AllowFullScreen" value="true"/><param name="flashvars" value="cid=1244648174251&b=60&f=50&l=999999&q=999999&r=100&s=1&t=0&v=0&w=0"/><embed id="emb_1244648174251" src="http://ant-chat.chatango.com/group" width="200" height="500" wmode="transparent" allowScriptAccess="always" allowNetworking="all" type="application/x-shockwave-flash" allowFullScreen="true" flashvars="cid=1244648174251&b=60&f=50&l=999999&q=999999&r=100&s=1&t=0&v=0&w=0"></embed></object></div><br />
</div>
    </div>      
  <!-- fin de #sidebar1 -->
        <div id="menu_fin"> </div>  </div>


  <div id="mainContent">
    
    <div class="gauche">
      <div class="droit">
        <div class="haut">
          <div>
            <h2>| Miniatures</h2></div>
        </div><!-- /haut -->
  <div id="class_rotator">
    <div id="class_rotator_wrapper">
      <div style="left: -420px;" id="class_rotator_classes">
        <?php echo $this->img; ?>
        </div>
      </div>
  </div>
      </div><!-- /droit -->
    </div><!-- /gauche -->
    
    <div style="text-align:center;color:#CCC">
	Publicité de la Team:<br />
	<script type="text/javascript">
	var advst_zoneid = 23796;
	var advst_width = 728;
	var advst_height = 90;
	var advst_border_style = "solid";
	var advst_border_color = "000000";
	var advst_title_color = "336699";
	var advst_title_bgcolor = "FFFFFF";
	var advst_text_color = "000000";
	var advst_text_bgcolor = "FFFFFF";
	var advst_url_color = "008000";
	var advst_url_bgcolor = "FFFFFF";
	var advst_page_scan = 1;
	var advst_withpos = 1;
	var advst_parrainage = "1";
	var advst_parrainage_style = "1";
	var advst_parrainage_position = "h";
</script>
<script type="text/javascript" src="http://ad.advertstream.com/advst_p.php"></script>
<noscript>
	<a href="http://ad.advertstream.com/adsclick.php?what=zone:23796&inf=no&n=" target="_blank">
	<img src="http://ad.advertstream.com/ads.php?what=zone:23796&inf=no&n=" border="0" alt=""></a>
</noscript>
</div>
  <div id="synopsis"><?php echo $this->synopsis; ?>
    </div>
  
    
</div>

	<!-- Cet élément de suppression doit suivre immédiatement l'élément div #mainContent afin de forcer l'élément div #container à contenir tous les éléments flottants enfants --><br class="clearfloat" />

<!-- fin de #container --></div>
</div>
</div>
  	<!-- fin de #header -->
  </div>
   <div id="footer">

  <!-- fin de #footer --></div>
 <script type="text/javascript">
    var advst_glob_scan = 1;
</script>
<script type="text/javascript" src="http://ad.advertstream.com/advst_f.php?affiliate=11442"></script>
  </body>