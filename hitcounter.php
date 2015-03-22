<?php
require_once("admin/conf.php"); //Commme d'ab
$sess=SessionManager::getInstance('GestDownSession',false);
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->enableDiskCache($diskCache,15);

// Lecture du paramètre id (méthode GET pour PHP>=4.1)
if(isset ($_GET['id']))
    $id = intval($_GET["id"]);
else
{
    die('Désolé ce lien n\'existe pas <br/>
		<script language="Javascript">
		function Fermer()
	{
		opener=self;
		self.close();
	}
	</script>
		<a href="#" onClick="Fermer();">Fermer</a>'
    );
}
if(isset($_GET['q']))
{
    $qualite= $_GET['q'];

    // Recherche de l'URL correspondant à l'identifiant id
    if(isset($_GET['t']))
    {
        if($qualite=='mq')
        {
            $link='torrentMQ';
            $title='Lien Torrent MQ';
        }
        elseif($qualite=='hd')
        {
            $link='torrentHD';
            $title='Lien Torrent HD';
        }
        elseif($qualite=='fhd')
        {
            $link='torrentFHD';
            $title='Lien Torrent FHD';
        }
        else
            die("Ce lien n'existe pas");
    }
    else
    {
        if($qualite=='mq')
        {
            $link='lien';
            $title='Lien DDL MQ';
        }
        elseif($qualite=='hd')
        {
            $link='lien2';
            $title='Lien DDL HD';
        }
        elseif($qualite=='fhd')
        {
            $link='lien3';
            $title='Lien DDL FHD';
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            die("Ce lien n'existe pas");
        }
    }
    $requete = "SELECT d.$link lien, d.nom dl_nom, c.nom cat_nom,d.mort mort, d.description FROM downloads d
	INNER JOIN categorie c
	ON c.id=d.categorie
	WHERE d.id=?";
    $db->pQuery($requete,array('i',$id));
    $result=$db->getRow(false,false);
    $mort=$result['mort'];

    $enreg= $result['lien'];
    if (!empty($enreg))
    {
        // Incrementation du compteur
        $requete = "UPDATE downloads SET nbhits=nbhits+1 WHERE id=$id";
        @$db->query($requete);

        // Redirection vers le lien sélectionné
        if($mort)
            $msgMort='<blink><font style="color:orange;font-size:26px;font-weight:bold;">Il est possible qu\'un ou plusieurs liens de cet épisode soit mort(s)</font></blink>';
        else
        {
            $captcha=new MathCaptcha();
            $captcha=new MathCaptcha();
            $captcha->generateProblem($id);
            $calcul=$captcha->printProblem(FALSE);
            $hiddenjava=$captcha->hiddenJavascript(FALSE,FALSE);
            $dead_template="<a class=\"reporter_lien\" style=\"color:orange;font-size:20px;text-decoration:underline; cursor:pointer;\" >Reporter lien(s) mort</a>
			\n<br /><div id=\"reporter\" style=\"display:none;\">
			
			<form id=\"formDead\"action=\"suggestion.php\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" id=\"mort\" name=\"mort\" target=\"_blank\">
<label class=\"mort\">Calculez : %s</label> <input name=\"math_captcha\" id=\"captcha_$id\" type=\"text\" size=\"4\" maxlength=\"3\" /><br />
<input name=\"quality\" type=\"hidden\" value=\"$title\" />
<label class=\"mort\">Explications :</label> <textarea name=\"explication\" cols=\"30\" rows=\"5\"></textarea><br />
<input name=\"mort\" type=\"hidden\" value=\"1\" />
<input name=\"id\" type=\"hidden\" value=\"$id\" />
<input name=\"\" type=\"submit\" id=\"Dead\" value=\"Reporter\" />
</form>
%s
</div>";
            $msgMort=sprintf($dead_template,$calcul,$hiddenjava);
        }

        ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Page de téléchargement de la Ame no Tsuki [AnT] (<?php echo $title," ",$result['dl_nom']," de ",$result['cat_nom'] ;?>)</title>
        <meta name="DESCRIPTION" content="<?php echo $title," ",$result['dl_nom']," de ",$result['cat_nom'] ;?> : <?php echo $result['description']; ?>" />
        <script src="static/js/jquery.js"></script>
        <script type="application/javascript" src="js/deadEpisodeHit.js"></script>
    </head>

    <body>

        <div style="text-align:center;">
            Publicitée de la team :<br />
           <script language="javascript" type="text/javascript" src="http://a01.gestionpub.com/GP6856a262e5a74e"></script>
           <script language="javascript" type="text/javascript" src="http://a01.gestionpub.com/GP6206c16ab36d4c"></script>
            <br />
        <?php echo $msgMort;?><br />
            <div id="result"></div>
        </div>
        <script type="text/javascript">

            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-9163128-1', 'auto');
            ga('send', 'pageview');
            setTimeout(function(){
                location.assign("<?php echo $enreg ?>");
            },1000)

        </script>
    </body>
</html>
        <?php
        //header("Location: ".$enreg[$link]);
        unset($db);
        die(); // Inutile de poursuivre...
    } else
    {
        header("HTTP/1.0 404 Not Found");
        echo "Etrangement... le lien n'a pu être trouvé";
    }

}
else
{
    header("HTTP/1.0 404 Not Found");
    die('Désolé ce lien n\'existe pas <br/>
		<script language="Javascript">
		function Fermer()
	{
		opener=self;
		self.close();
	}
	</script>
		<a href="#" onClick="Fermer();">Fermer</a>'
    );
}
unset($db);


?>


