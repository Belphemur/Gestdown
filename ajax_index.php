<?php
header("Content-type: text/html; charset=UTF-8");
require_once("admin/conf.php");
require_once ("classes/Episode.class.php");
require_once ("classes/Serie.class.php");
require_once ("classes/Index.class.php");
require_once ("classes/Stats.class.php");
require_once ("classes/Result.class.php");
require_once ("classes/View.class.php");
require_once("includes/login.php");

define ('VIEW','./templates/vues/');

require_once('./includes/bbcode/nbbc_main.php');
$bbcode = new BBCode;
$bbcode->SetLocalImgDir("http://images.gestdown.info/smileys");
$bbcode->SetSmileyURL("http://images.gestdown.info/smileys");

$admin=login();
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->enableDiskCache($diskCache);
$db->query("SET NAMES 'utf8'");

function index($av=false)
{
    global $db,$bbcode,$timeout;
    $index;
    $avancement= new View(VIEW);
    $av_res=new Result();


    $file='templates/news_pres.txt';
    try
    {
        $index = new Index($file,$db);
        $index->setBBcode($bbcode);
        if($av)
            $avancement->convert($index->avancement());
    } catch (Error $e)
    {
        if($e->getCode() == 10)
        {
            $av_res->html="Aucun épisode en cours de production pour le moment.";
            return $av_res ;
        }
        return "Caught Exception ('{$e->getMessage()}')<br />\n{$e}<br />\n";
    }
    if($av)
    {
        $av_res->html=$avancement->render('v_avancement.php');

        return $av_res;
    }
    else
    {
        $db->changeCacheTimeout(25);
        $daily_s=new Stats(time(),$db);
        $daily_stats=$daily_s->daily_display();
        $stat=$daily_s->total_display();
        $vue= new View(VIEW);
        $vue->convert($index->execute());
        $vue->daily_stats=$daily_stats;
        $vue->_stat=$stat;
        $db->changeCacheTimeout($timeout/60);

        return $vue->render('v_ajax_index.php');
    }

}
function serie($id,$lite=false)
{
    global $db,$url_site,$timeout;
    $result=new Result();
    $vue=new View(VIEW);
    $serie;
    try
    {
        $serie = new Serie($id,$db);
        $vue->convert($serie->execute());
        $vue->id=$id;
        $vue->url_site=$url_site;
        $result->html = $vue->render('v_ajax_serie.php');
        $result->nom = $serie->getNom();

    } catch (Error $e)
    {

        $result->nom = "Inconnue";
        $result->synopsis = "Série vide";
        if($e->getCode() == 2)
        {
            //header("HTTP/1.0 404 Not Found");
            $result->html = '<div class="gauche">
	  <div class="droit">
	  <div class="haut">
	   <div>
		<h2>| Vide</h2></div>
		Nous somme désolé mais pour le moment il n\'y a aucun épisode de sortit pour cette série.
	  </div><!-- /haut -->
	  </div><!-- /droit -->
	 </div><!-- /gauche -->';
        }
        else
            $result->html = "Caught Exception ('{$e->getMessage()}')<br />\n{$e}<br />\n";
    }
    return $result;
}

function episode($id,$ext=false)
{
    global $db,$admin,$bbcode;
    $result=new Result();
    $tmp= new Result();
    $vue = new View(VIEW.'Episodes/');
    $ep;
    try
    {
        $ep = new Episode($id,$db);
        $ep->setBBcode($bbcode);
        $tmp=$ep->execute();


        if($ext)
        {
            $result->catId=$ep->getCatId();
            $serie = new Serie($result->catId,$db,true);
            $tmp->add($serie->execute());
            $vue->convert($tmp);
            $vue->id=$id;
            if($admin)
                $result->html = $vue->render('a_ext.php');
            else
                $result->html = $vue->render('na_ext.php');
        }
        else
        {
            $vue->convert($tmp);
            $vue->id=$id;
            if($admin)
                $result->html = $vue->render('a_int.php');
            else
                $result->html = $vue->render('na_int.php');
        }
        $result->nom = $ep->getNom();

    } catch (Error $e)
    {
        header("HTTP/1.0 404 Not Found");
        $result->html ="Episode Inexistant";
        $result->nom = "Inconnu au Bataillon";
        //return "Caught Exception ('{$e->getMessage()}')<br />\n{$e}<br />\n";
    }


    return $result;
}
if(isset($_GET['id'],$_GET['module']) && ($_GET['module']=="index"))
{
    echo index();
    unset($GLOBALS['db']);
}
if(isset($_GET['id'],$_GET['module']) && ($_GET['module']=="avancement"))
{
    $result= index(true);
    echo $result->html;
    unset($GLOBALS['db']);
}
else if(isset($_GET['id'],$_GET['module']) && ($_GET['module']=="serie"))
{
    $id=$_GET['id'];
    $result= serie($id);
    echo $result->html;
    unset($GLOBALS['db']);
}
else if(isset($_GET['id']) && isset($_GET['module']) && ($_GET['module']=="episode"))
{
    $id=$_GET['id'];
    $result= episode($id);
    echo $result->html;
    unset($GLOBALS['db']);
}
?>