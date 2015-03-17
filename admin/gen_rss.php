<?php

//Génération du flux RSS
require_once('../includes/gestion_xml_rss.php');
require_once('../includes/bbcode/nbbc_main.php');
require_once('conf.php');
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$bbcode = new BBCode;
$bbcode->SetLocalImgDir("http://imagidream.eu/ant/images/smileys");
$bbcode->SetSmileyURL("http://imagidream.eu/ant/images/smileys");

function replacement($text) {
    $text = stripslashes($text);
    global $bbcode;
    return $bbcode->Parse($text);
}

$memoryfile = createNewXML('../rss', 'Gestdown : Sorties de la Ame no Tsuki', 'Flux RSS contenant les dernières sorties de la Ame no Tsuki', $url_site, 'http://imagidream.eu/ant/templates/sobre/images/apple-touch-icon.png');
$sql = "
(
	SELECT r.date,r.news_id, r.description, r.title, c.nom cat_nom, c.image cat_img, d.nom dl_nom, d.id dl_id, d.screen dl_screen, d.description dl_desc,d.date dl_date, d.auteur dl_auteur
	FROM rss r
	LEFT JOIN downloads d ON d.id = r.dl_id
	RIGHT JOIN categorie c ON d.categorie = c.id
	WHERE r.title != '' AND d.actif=1
)
UNION
(
	SELECT r.date,r.news_id, r.description, r.title, null cat_nom, null cat_img, null dl_nom, r.dl_id dl_id, null dl_screen, null dl_desc, null dl_date, null dl_auteur
	FROM rss r, downloads d, categorie c
	WHERE r.dl_id =0
)
ORDER BY dl_date DESC, date DESC
";
$db->mQuery($sql);
$datas = $db->getResults(false,false);
foreach ($datas as $news)
{

    if ($news['title'] == "EPISODE")
    {
        /* $feed = $db->get_row("SELECT c.nom cat_nom, c.image cat_img,d.nom,d.id,d.screen,d.auteur,d.date,d.description
          FROM `downloads` d
          INNER JOIN categorie c
          ON c.id=d.categorie
          WHERE c.nom!='Prob de Lien' AND d.id=".$news['dl_id']); */
        $feed = $news;
        $titre = $feed['dl_nom'] . " de " . $feed['cat_nom'];
        $lien = $url_site . 'ep-' . $feed['dl_id'] . '.html';
        $date = $feed['dl_date'];

        if ($feed['dl_screen'] != "")
            $img = '<img src="' . $feed['dl_screen'] . '" width=460  alt="Screen" /><br />';
        else
            $img='<img src="' . $feed['cat_img'] . '" width=460  alt="Screen" /><br />';

        if ($feed['dl_desc'] != "")
            $desc = $img . replacement($feed['dl_desc']);
        else
            $desc=$img . "Désolé aucune description";
        $auteur = "$email_admin ({$feed['dl_auteur']})";
        $categorie = $feed['cat_nom'];
        $guid = array('false', sha1($news['news_id']));
    }
    else
    {
        $titre = stripslashes($news['title']);
        $download = $news['dl_id'];
        if ($news['dl_id'] == 0)
        {
            $lien = $url_site;
            $guid = array('false', sha1($news['news_id']));
            $img = '<img src="http://imagidream.eu/image-4895_4A8FC75C.jpg" alt="Logo" /><br />';
        } else
        {
            $lien = $url_site . 'ep-' . $news['dl_id'] . '.html';
            $img = '<img src="' . $news['cat_img'] . '" alt="Logo" /><br />';
            $guid = array('false', sha1($news['news_id']));
            $titre.=" (" . $news['dl_nom'] . ' de ' . $news['cat_nom'] . " )";
        }
        $date = $news['date'];



        $desc = $img . replacement($news['description']);
        $auteur = "contact@imagidream.info (Ame no Tsuki)";
        $categorie = 'News';
    }

    addOneNews($memoryfile, $titre, $desc, $lien, $date, $auteur, $guid, $categorie);
}
saveXML($memoryfile, '../rss');
$db->query("SELECT c.nom cat_nom,downloads.nom,downloads.id
 FROM downloads 
 INNER JOIN categorie c
 ON c.id=downloads.categorie
 WHERE actif=1 AND downloads.nom!='EPISODE A MODIFIER' AND c.nom!='Prob de Lien' 
 ORDER BY date DESC LIMIT 10");
$datas = $db->getResults(false,false);

$sortie = gen_xml("../sorties");
foreach ($datas as $sorties)
{
    addOneEpisode($sortie, $sorties['id'], $sorties['nom'], $sorties['cat_nom']);
}
saveXML($sortie, '../sorties');
?>