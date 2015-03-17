<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); ?>
<?php
require_once("admin/conf.php");
require_once("includes/bbcode.php");

$db->enableDiskCache($diskCache,15);
if(isset($_GET['id']))
{
    $id=intval($_GET['id']);
    $ratingManager = RatingManager::getInstance();
    $stars=$ratingManager->drawStars($id);
    $db->pQuery("SELECT * FROM categorie c
        LEFT JOIN informations i
        ON i.cat_id = c.id
        WHERE c.id=?",array('i',$id));
    $cat=$db->getRow(false,false);
    $db->pQuery("SELECT SUM(nbhits) as dl, COUNT(id) as nb FROM downloads WHERE categorie=?",array('i',$id));
    $row =$db->getRow(false,false);
    $nbre=$row['dl'];
    $nbre_ep=$row['nb'];
    $captures='';
    $data=$db->pQuery("SELECT screen,nom FROM downloads WHERE categorie=? AND screen!='' ORDER BY id DESC LIMIT 8 ",array('i',$id));


    if($data)
    {
        $data=$db->getResults(false,false);
        $captures='<div class="separator">
	<h2>| Captures</h2></div><div id="slideshow" class="pics">';
        foreach($data as $img)
        {
            $captures.='<IMG name="'.$img['nom'].'" src="'.$img['screen'].'" alt="'.$img['nom'].'" width="460">';
        }
        $captures.="</div>";
    }
    echo'<meta property="og:title" content="',$cat['nom'],' traduit par la Ame no Tsuki"/>
        <meta property="og:image" content="',$cat['image'],'"/>
        <div class="gauche">
  <div class="droit">
  <div class="haut">
   <div>
   	<h2>| ',$cat['nom'],'</h2></div>
  </div><!-- /haut -->
  <div style="text-align:center;"><img src="',$cat['image'],'" alt="Image de la série" /><br /></div>',$stars,'  <fb:like title="',$cat['nom'],' traduit par la Ame no Tsuki" show_face="true" width="450" action="like" href="',$url_site,'serie-',$id,'-',strtr($cat['nom'], ' ', '_'),'.html" xid="serie-',$id,'"></fb:like>
    <div class="separator">
	<h2>| Informations</h2></div>
	<label class="info">Année de Production : </label>',$cat['annee'],'<br />
	<label class="info">Studio(s) : </label>',$cat['studio'],'<br />
	<label class="info">Genre(s) : </label>',$cat['genre'],'<br />
	<label class="info">Auteur(s) : </label>',$cat['auteur'],'<br />
	<label class="info">Type et Durée : </label>',$cat['episode'],'<br />
	(Source : <a href="http://www.animeka.com/" title="Animeka" target="_blank">Animeka</a>)<br /><br />';
    if(!empty($nbre_ep))
        echo'
		<label class="info">Téléchargements : </label>',$nbre,'<br />
		<label class="info">Episodes sortis : </label>',$nbre_ep,'<br />';
    echo $captures,'
<div class="separator">
	<h2>| Synopsis</h2></div>'
    ,replacement($cat['description']);
    if(!$cat['licencie'])
    {
        echo '<div class="separator">
		<h2>| Télécharger</h2></div>

<a target="_self" class="news_liens_dl" href="serie-',$id,'-',strtr($cat['nom'], ' ', '_'),'.html"> La liste des épisodes</a><br />';
    }
    echo '
  </div><!-- /droit -->
 </div><!-- /gauche -->';
    unset($db);
}
else
{
    $imageVide="miniatures/Aucune.jpg";
    $cours='';
    $finies="";
    $lic="";
    $img="";
    $stop='';
    $i=1;
    $datas=$db->getResults("SELECT id,nom,finie,licencie,stopped FROM categorie WHERE nom!='Prob de lien' ORDER BY nom ASC",false);
    foreach ($datas as $serie)
    {
        if($serie['finie'] && !$serie['licencie'])
            $finies.="<a rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />";
        else if($serie['licencie'])
            $lic.="<a rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />";
        else if($serie['stopped'])
            $stop.="<a rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />";
        else
            $cours.="<a rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />";
        $img.='<img rel="'.$serie['id'].'"  title="'.$serie['nom'].'" class="" style="width: 80px; display: block; height: 103px; margin-top: 47px;" src="http://min.gestdown.info/'.$serie['nom'].'.jpg" id="class_rotator_'.$i.'" alt="Miniature de la série" onError="this.src=\'http://images.gestdown.info/'.$imageVide.'\'"/>
		';
        $i++;
    }

    ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Projets de la Ame no Tsuki</title>
        <meta name="TITLE" content="Projets de la Ame no Tsuki" />
        <meta name="AUTHOR" content="Balor" />
        <meta name="SUBJECT" content="Tous les projets de la Ame no Tsuki" />
        <meta name="DESCRIPTION" content="Page regroupant tous les projets de la Ame no Tsuki autant les terminés, que les en cours." />
        <meta name="KEYWORDS" content="ame no tsuki, mangas, anime, japon, fansub, téléchargement, projets" />
        <meta name="REVISIT-AFTER" content="30 DAYS" />
        <meta name="LANGUAGE" content="FR" />
        <meta name="OWNER" content="contact@imagidream.eu" />
        <meta name="ROBOTS" content="All" />
        <meta name="RATING" content="Fansub" />
        <meta property="og:site_name" content="IMDb"/>
        <link rel="alternate" type="application/rss+xml" title="Gestdown (RSS 2.0)" href="http://feeds.feedburner.com/GestdownSortiesDeLaAmeNoTsuki?format=xml" />
        <link rel="shortcut icon" href="templates/img/favicon.ico" />
        <link rel="apple-touch-icon" href="templates/img/apple-touch-icon.png" />
        <link href="http://www.gestdown.info/css/thrColLiq.css,rotator.css,stars.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="http://www.gestdown.info/js/jquery-1.6.2.min.js,jquery.livequery.js,rating.js,jquery.cycle.lite.1.0.min.js,projets_full.js"></script>
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


        <!--[if IE 5]>
        <style type="text/css">
        /* placer les corrections pour le modèle de boîte css pour IE 5.x dans ce commentaire conditionnel */
        .thrColFixHdr #sidebar1 { width: 180px; }
        .thrColFixHdr #sidebar2 { width: 190px; }
        </style>
        <![endif]--><!--[if IE]>
        <style type="text/css">
        /* placer les corrections pour toutes les version d'IE dans ce commentaire conditionnel */
        .thrColFixHdr #sidebar2, .thrColFixHdr #sidebar1 { padding-top: 30px; }
        .thrColFixHdr #mainContent { zoom: 1; }
        /* la propriété propriétaire zoom ci-dessus transmet à IE l'attribut hasLayout nécessaire pour éviter plusieurs bogues */
        </style>
        <![endif]-->
        <style type="text/css">
            .pics { height: 291px; width: 492px; padding:0; margin:15px; overflow: hidden; text-align:center; }
            .pics img { height: 259px; width: 460px; padding: 15px; border: 1px solid #999; background-color: #000; }
            .pics img {
                -moz-border-radius: 10px; -webkit-border-radius: 10px;
            }
        </style>
    </head>

    <body class="thrColFixHdr">
        <div id="fb-root"></div>
        <script language="javascript" type="text/javascript" src="http://a01.gestionpub.com/GP6206c16ab36d4c"></script>
        <div id="container">
            <div id="header">
                <!-- fin de #header -->
                <div id="menu"><DIV id="topnav">
                        <UL>
                            <LI><A href="http://ame-no-tsuki.fr" title="projets">Site de la team</A></LI>
                            <LI><A href="index.php">gestdown</A></LI>
                            <LI id="first-item" class="current_page_item"><A href="projets.php" title="projets">Projets</A></LI>
                        </UL>
                    </DIV>
                </div></div>
            <div id="sidebar1">
                <p><div style="text-align:center; margin-top: 20px;"><a href="http://feeds.feedburner.com/GestdownSortiesDeLaAmeNoTsuki?format=xml" target="_blank"><img src="http://feeds.feedburner.com/~fc/GestdownSortiesDeLaAmeNoTsuki?bg=000099&amp;fg=33CC99&amp;anim=1" height="26" width="88" style="border:0" alt="" /></a>
                    </div></p>
                <div style="text-align:center;"><img src="http://style.gestdown.info/series.png" width="88" height="27" alt="Series" /></div>
                <div id="class_list">
                    <div style="text-align:center;"><img src="http://style.gestdown.info/cours.png" width="126" height="28" alt="en cours" /></div>
                    <p><?php echo $cours;?></p>
                    <div style="text-align:center;"><img src="http://style.gestdown.info/sFinished.png" width="145" height="29" alt="terminée" /></div>
                    <p><?php echo $finies;?></p>
                    <div style="text-align:center;"><img src="http://style.gestdown.info/cancel.png" width="180" height="29" alt="abandonnées" /></div>
                    <p><?php echo $stop;?></p>
                    <div style="text-align:center;"><img src="http://style.gestdown.info/licencie.png" width="142" height="28" alt="licenciées" /></div>
                    <p><?php echo $lic;?></p>
                </div>
                <!-- SHOUTBOX 
                <div style="text-align:center;"><img src="http://style.gestdown.info/shoutbox.png" width="138" height="28" alt="chat" class="imgMenu" /></div><br />
                <object width="100%" height="600" id="obj_1244648174251">
                    <param name="movie" value="http://ant-chat.chatango.com/group"/>
                    <param name="wmode" value="transparent"/><param name="AllowScriptAccess" value="always"/>
                    <param name="AllowNetworking" value="all"/><param name="AllowFullScreen" value="true"/>
                    <param name="flashvars" value="cid=1244648174251&b=60&f=50&l=999999&q=999999&r=100&s=1&t=0&v=0&w=0"/>
                    <embed id="emb_1244648174251" src="http://ant-chat.chatango.com/group" width="100%" height="600" wmode="transparent" allowScriptAccess="always" allowNetworking="all" type="application/x-shockwave-flash" allowFullScreen="true" flashvars="cid=1244648174251&b=60&f=50&l=999999&q=999999&r=100&s=1&t=0&v=0&w=0"></embed></object>
                <a style="left: 145.933px ! important; top: -500px ! important;" title="Cliquer ici afin qu'Adblock Plus bloque cet objet" class="npdrhvbyeywrnysjeyzd visible ontop" href="http://ant-chat.chatango.com/group"></a><br />
               FIN SHOUTBOX !--><br />
                <div style="text-align:center;"><img src="http://style.gestdown.info/chibi6.png" width="111" height="193" alt="chibi6" /></div>
                <!-- fin de #sidebar1 --></div>

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
                                        <?php echo $img; ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- /droit -->
                </div><!-- /gauche -->

                <div style="text-align:center;color:#CCC" class="gauche">
	Publicité de la Team:<br />
                    <script language="javascript" type="text/javascript" src="http://a01.gestionpub.com/GP6ef6b168047a3d"></script>
                </div>
                <div id="synopsis"></div>


                <!-- fin de #mainContent --></div>

            <!-- Cet élément de suppression doit suivre immédiatement l'élément div #mainContent afin de forcer l'élément div #container à contenir tous les éléments flottants enfants --><br class="clearfloat" />
            <div id="footer">
                <div style="background: url(http://style.gestdown.info/4.png) no-repeat right top; height:151px;">
                    <div style="margin-top:25px;"><img src="http://style.gestdown.info/3.png" width="94" height="123" alt="chibi" /><center><img src="http://style.gestdown.info/credits.png" width="262" height="33" alt="Site fait par Threaton et Balor" /></center></div></div>
                <div style="text-align:center;">
                    <p> Copyright 2010 <br />
                        Gestdown©<br />
                        Tout contenu, vidéos, images, textes appartiennent à leurs auteurs respectifs et sont utilisés à titre illustratif et/ou informatif. </p>
                </div>
                <!-- fin de #footer --></div>
            <!-- fin de #container --></div>
        <script type="text/javascript">
            var advst_glob_scan = 1;
        </script>
        <script type="text/javascript" src="http://ad.advertstream.com/advst_f.php?affiliate=11442"></script>
    </body>
</html>
    <?php
}
?>