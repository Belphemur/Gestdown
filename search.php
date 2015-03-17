<?php
function filter($in) {
	$search = array ('@[éèêë]@','@[ÊËÉÈ]@','@[àâä]@','@[ÂÄÀÁ]@','@[îï]@i','@[ûùü]@i','@[ôö]@i','@[ç]@i');
	$replace = array ('e','E','a','A','i','u','o','c');
	return preg_replace($search, $replace, $in);
}
require_once('includes/bbcode.php');
/*
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "gestionnaire";

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die                      ('Error connecting to mysql');
mysql_select_db($dbname);
*/
require_once('admin/conf.php');
$db->cache_queries = true;
$db->cache_timeout = 2*3600;

$query=isset($_GET['query'])?$_GET['query']:'';
if(isset($_GET['type'])) 
	$type = $_GET['type']; 
else 
	$query = "count"; 

$liant='AND';
//$query=filter($query);


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
	$sql = 'SELECT c.nom cat_nom,downloads.nom,downloads.id,downloads.lien mq,downloads.lien2 hd,downloads.lien3 fhd
						FROM downloads 
						INNER JOIN categorie c
						ON c.id=downloads.categorie
						WHERE (c.nom LIKE "%'.$serie.'%" '.$liant.' downloads.nom LIKE "%'.$episode.'%" ) 
						ORDER BY downloads.id';
	
	if(!$db->query($sql))
	{
		$sql='SELECT c.nom cat_nom,downloads.nom,downloads.id,downloads.lien mq,downloads.lien2 hd,downloads.lien3 fhd
		FROM downloads 
		INNER JOIN categorie c
		ON c.id=downloads.categorie
		WHERE downloads.description LIKE "%'.$serie.'%" 
		ORDER BY downloads.id';
		$datas=$db->get_results($sql);
	}
	else
		$datas=$db->get_results();
	if(isset($datas[0]))
	{
		foreach($datas as $array) {
			
			$url_url ="ep-".$array['id'].'.html';
			$url_title =$array['cat_nom'].' : '.$array['nom'];
			//$url_desc = $array[2];
			$hd="";
			$fhd="";
			if($array['hd']!="")
			{
				$hd="<label class=\"liens\">lien HD = </label>
						<a target=\"_blank\" class=\"news_liens_dl\" href=\"dl-".$array['id']."-hd.html\">Par ICI</a><br />";
			}
			if($array['fhd']!="")
			{
				$fhd="<label class=\"liens\">lien FHD = </label>
						<a target=\"_blank\" class=\"news_liens_dl\" href=\"dl-".$array['id']."-fhd.html\">Par ICI</a><br />";
			}
			//$url_desc=filter($url_desc);
			//$url_desc=replacement($url_desc);
			echo "<div class=\"url-holder\"><a href=\"" , $url_url , "\" class=\"url-title\" target=\"_self\">" , $url_title , "</a>";
			
			//<div class=\"url-desc\">" , $url_desc , "<br /></div>
			echo "<div class=\"liens_search\"><label class=\"liens\">lien MQ = </label>
					<a target=\"_blank\" class=\"news_liens_dl\" href=\"dl-".$array['id']."-mq.html\">Par ICI</a><br />
					",$hd,$fhd,"</div></div>";
			
			
		}
	}
	else
	{
		$url_url ="#";
		$url_title ="Aucun resultat";
		$url_desc = "Veuillez reessayer avec la synthaxe suivante : Nom_de_la_serie + Num_episode. <br /><br />Exemple : Pandora+4 donnera l'episode 4 de pandora hearts.";
		echo "<div class=\"url-holder\"><a href=\"" . $url_url . "\" class=\"url-title\" target=\"_self\">" . $url_title . "</a>
				
				<div class=\"url-desc\">" . $url_desc . "</div></div>";
	}
}
unset($db);
?>