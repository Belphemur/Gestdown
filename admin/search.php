<?php
require('../includes/bbcode.php');
/*
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "gestionnaire";

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die                      ('Error connecting to mysql');
mysql_select_db($dbname);
*/
require_once('conf.php');
$db->disableDiskCache();
if(isset($_GET['query']))
{ 
    $query = $_GET['query'];
} else
{ 
    $query = "";
}
if(isset($_GET['type']))
{ 
    $type = $_GET['type'];
} else
{ 
    $query = "count";
}

$liant='AND';
$query=$query;

if(strpos($query,'+'))
{
    list($serie,$episode)= explode('+',$query,2);
    $serie=trim($serie);
    $episode=trim($episode);
}
else
{
    $serie=trim($query);
    $episode=trim($query);
    $liant='OR';
}

if($type == "count")
{
    $sql = 'SELECT count(downloads.id)
								FROM downloads
								INNER JOIN categorie
								ON categorie.id=downloads.categorie
								WHERE (categorie.nom LIKE "%'.$serie.'%" '.$liant.' downloads.nom LIKE "%'.$episode.'%" )';
    $num = $db->get_var($sql);
    if($num==0)
    {
        $sql='SELECT count(downloads.id) FROM downloads WHERE description LIKE "%'.$serie.'%"';
        $num = $db->get_var($sql);
    }

    echo $num;
}

if($type == "results")
{
    $sql = 'SELECT c.nom cat_nom,downloads.nom,downloads.id,downloads.screen,downloads.description
						FROM downloads 
						INNER JOIN categorie c
						ON c.id=downloads.categorie
						WHERE (c.nom LIKE "%'.$serie.'%" '.$liant.' downloads.nom LIKE "%'.$episode.'%" ) 
						ORDER BY downloads.id';

    if(!$db->query($sql))
    {
        $sql='SELECT c.nom cat_nom,downloads.nom,downloads.id,downloads.screen,downloads.description
		FROM downloads 
		INNER JOIN categorie c
		ON c.id=downloads.categorie
		WHERE downloads.description LIKE "%'.$serie.'%" 
		ORDER BY downloads.id';
        $datas=$db->get_results($sql);
    }
    else
        $datas=$db->get_results();
    echo'<table width="720" height="21" border="1" cellpadding="0" cellspacing="0">
<tr>
   <td width="77">Nom</td>
   <td width="300">Description</td>
   <td width="44">Modifier</td>
   <td width="57">Supprimer</td>
</tr>';

    if(!empty($datas))
    {
        foreach($datas as $array)
        {

            $url_id =$array['id'];
            $url_title =$array['cat_nom'].' : '.$array['nom'];
            $url_desc = $array['description'];
            $screen=$array['screen'];
            $url_desc=replacement($url_desc);

            echo'
			<tr id="cellule_'.$url_id.'">
			<td>'.$url_title;

            if($screen!='')
                echo' <br><img src="'.$screen.'" alt="" width="200" height="auto" />';

            echo '</td><td>'.$url_desc.'</td>
			<td><a class="modifier" href="javascript:void(0);"  id="modif_'.$url_id.'"><img src="./images/mod.gif" width="23" height="23" border="0" alt=""></a></td>
			<td><a <a class="supprimer" href="javascript:void(0);"  id="supp_'.$url_id.'" ><img src="supprimer.png" width="23" height="23" border="0" alt=""></a></td>
			</tr>';


            /*		echo "<div class=\"url-holder\"><a href=\"" . $url_url . "\" class=\"url-title\" target=\"_self\">" . $url_title . "</a>
	
	<div class=\"url-desc\">" . $url_desc . "</div></div>";*/

        }
        echo'</table>';
    }
    else
        echo "Aucun Résultat";

}

unset($db);

?>