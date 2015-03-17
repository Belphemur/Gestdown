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
        <meta name="REVISIT-AFTER" content="7 DAYS" />
        <meta name="COPYRIGHT" content="Tout contenu, vidéos, images, textes appartiennent à leurs auteurs respectifs et sont utilisés à titre illustratif et/ou informatif. " />
        <meta name="LANGUAGE" content="FR" />
        <meta name="OWNER" content="contact@imagidream.eu" />
        <meta name="ROBOTS" content="All" />
        <meta name="RATING" content="General" />
        <meta name="google-site-verification" content="u5EBaJ0m7q4fc-P3XpHv1qbduymAfNqcEuCJoMJ88kE" />
        <meta name="msvalidate.01" content="2014CE1E3D3BAD4B6218115A64DBD92F" />
        <META name="y_key" content="7f35830ae93a5f5f">
            <link rel="alternate" type="application/rss+xml" title="Gestdown (RSS 2.0)" href="http://feeds.feedburner.com/GestdownSortiesDeLaAmeNoTsuki?format=xml" />
            <link rel="shortcut icon" href="favicon.ico" />
            <link rel="apple-touch-icon" href="templates/img/apple-touch-icon.png" />
            <link href="<?php echo $this->cssUrl;?>rotator.css,stars.css,thrColLiq.css" rel="stylesheet" type="text/css" />
            <!-- js/markitup! skin -->
            <link rel="stylesheet" type="text/css" href="http://js.gestdown.info/markitup/skins/markitup/style.css" />
            <!--  js/markitup! toolbar skin -->
            <link rel="stylesheet" type="text/css" href="http://js.gestdown.info/markitup/sets/bbcode/style.css" />
            <script src="<?php echo $this->jsUrl;?>jquery-1.6.2.min.js,rating.js,swfaddress-optimizer.js,swfaddress.js,ajax_full.js?swfaddress=%2F&amp;base=%2F" type="text/javascript"></script>
            <!-- js/markitup! -->
            <script type="text/javascript" src="<?php echo $this->jsUrl;?>markitup/jquery.markitup.pack.js"></script>
            <!-- js/markitup! toolbar settings -->
            <script type="text/javascript" src="<?php echo $this->jsUrl;?>markitup/sets/bbcode/set.js"></script>
            <script type="text/javascript">
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', 'UA-9163128-1']);
                _gaq.push(["_setDomainName", "none"]);
            </script>
            <?php echo $this->header_js; ?>
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
    </head>
    <body class="thrColFixHdr">
        <div id="fb-root"></div>
        <div id="container">
            <div id="header">
                <!-- fin de #header -->
                <div id="menu"><div id="topnav">
                        <ul>
                            <li><a href="http://ame-no-tsuki.fr" title="projets">Site de la team</a></li>
                            <li id="first-item" class="current_page_item"><a href="index.php">gestdown</a></li>
                            <li><a href="projets.php" title="projets">Projets</a></li>
                            <li><a href="http://mononoke-bt.org/browse2.php?team=438" title="Mononoké-BT">Torrents</a></li>
                        </ul>
                    </div>
                </div></div>
            <div id="sidebar1">
                <a id="admin"><img src="<?php echo $this->styleUrl; ?>chibi1.png" id="chibi1" />
                    <div style="text-align:center;"><img src="<?php echo $this->styleUrl; ?>connexion.png" width="139" height="27" alt="Connexion" border="0"/></div></a><br />
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
                    <br /><a href="index.php?logout" title="Se déconnecter" class="lienMenuGauche" target="_self">Deconnexion</a>
                </div>
                    <?php
                }
                ?>
                <p><div style="text-align:center; margin-top: 20px;"><a href="http://feeds.feedburner.com/GestdownSortiesDeLaAmeNoTsuki?format=xml" target="_blank"><img src="http://feeds.feedburner.com/~fc/GestdownSortiesDeLaAmeNoTsuki?bg=000099&amp;fg=33CC99&amp;anim=1" height="26" width="88" style="border:0" alt="" /></a>
                    </div></p>
                <div style="text-align:center;"><img src="<?php echo $this->styleUrl; ?>series.png" width="88" height="27" alt="Series" /></div>
                <div id="class_list">
                    <br /><p><a rel="0" title="index" class="" id="class_nav_1">Index</a></p>
                    <p><a rel="-1" href="avancement.html" onclick="javascript:return false;" title="Avancement" class="" id="class_nav_2">Avancement</a></p>
                    <div style="text-align:center;"><img src="<?php echo $this->styleUrl; ?>cours.png" width="126" height="28" alt="en cours" /></div>
                    <p><?php echo $this->cours;?></p>
                    <div style="text-align:center;"><img src="<?php echo $this->styleUrl; ?>sFinished.png" width="145" height="29" alt="terminée" /></div>
                    <p><?php echo $this->finies;?></p>
                    <div style="text-align:center;"><img src="http://style.gestdown.info/cancel.png" width="180" height="29" alt="abandonnées" /></div>
                    <p><?php echo $this->stop;?></p>
                </div>
                <!-- SHOUTBOX 
                <div style="text-align:center;"><img src="<?php echo $this->styleUrl; ?>shoutbox.png" width="138" height="28" alt="chat" class="imgMenu" /></div><br />
                <object width="100%" height="600" id="obj_1244648174251">
                    <param name="movie" value="http://ant-chat.chatango.com/group"/>
                    <param name="wmode" value="transparent"/><param name="AllowScriptAccess" value="always"/>
                    <param name="AllowNetworking" value="all"/><param name="AllowFullScreen" value="true"/>
                    <param name="flashvars" value="cid=1244648174251&b=60&f=50&l=999999&q=999999&r=100&s=1&t=0&v=0&w=0"/>
                    <embed id="emb_1244648174251" src="http://ant-chat.chatango.com/group" width="100%" height="600" wmode="transparent" allowScriptAccess="always" allowNetworking="all" type="application/x-shockwave-flash" allowFullScreen="true" flashvars="cid=1244648174251&b=60&f=50&l=999999&q=999999&r=100&s=1&t=0&v=0&w=0"></embed></object>
                <a style="left: 145.933px ! important; top: -500px ! important;" title="Cliquer ici afin qu'Adblock Plus bloque cet objet" class="npdrhvbyeywrnysjeyzd visible ontop" href="http://ant-chat.chatango.com/group"></a><br />
                FIN SHOUTBOX <br /> !-->
                <div style="text-align:center;"><img src="<?php echo $this->styleUrl; ?>chibi6.png" width="111" height="193" alt="chibi6" /></div>
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
                                    <?php echo $this->img; ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- /droit -->
                </div><!-- /gauche -->

                <div style="text-align:center;color:#CCC" class="gauche">
	Publicité de la Team:<br />
                    <script language="javascript" type="text/javascript" src="http://a01.gestionpub.com/GP6ef6b168047a3d"></script>
                </div>
                <div id="synopsis"><?php echo $this->synopsis; ?>
                </div>


                <!-- fin de #mainContent --></div>

            <!-- Cet élément de suppression doit suivre immédiatement l'élément div #mainContent afin de forcer l'élément div #container à contenir tous les éléments flottants enfants --><br class="clearfloat" />
            <div id="footer">
                <div style="background: url(<?php echo $this->styleUrl; ?>4.png) no-repeat right top; height:151px;">
                    <div style="margin-top:25px;"><img src="<?php echo $this->styleUrl; ?>3.png" width="94" height="123" alt="chibi" /><center><img src="<?php echo $this->styleUrl; ?>credits.png" width="262" height="33" alt="Site fait par Threaton et Balor" /></center></div></div>
                <div style="text-align:center;color:#0045b6;">
                    <p> Copyright 2010 <br />
                        Gestdown©<br />
                        Tout contenu, vidéos, images, textes appartiennent à leurs auteurs respectifs et sont utilisés à titre illustratif et/ou informatif. <br />
                        Gestdown.info does not store any files on its server. Gestdown.info does not reserve any rights to, nor claims copyright to, any animes listed on these pages. All references are copyright to their respective owners. Gestdown.info is not affiliated to Mirorii.com in any way.</p>
                </div>
                <!-- fin de #footer --></div>
            <!-- fin de #container --></div>
        <script type="text/javascript">  (function() {
            var ga = document.createElement('script');     ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:'   == document.location.protocol ? 'https://ssl'   : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
        </script>

    </body>
</html>
