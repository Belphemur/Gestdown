<?php
require_once("admin/conf.php"); //Commme d'ab
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->enableDiskCache($diskCache, 1);
if (isset($_GET['h']))
{
    $host = $_GET['h'];
    $type = $_GET['type'];
    if ($type == 'mirror')
    {
        $id = (int) $_GET['i'];
        $q = $_GET['q'];
        $query = $_GET['s'];
        $sql = 'SELECT link,fileID FROM mirror_links WHERE epNum=? AND quality=? AND serieID=? AND hoster=?';
        $result = $db->pQuery($sql, array('isss', $id, $q, $query, $host == 'MV' ? 'MU' : $host));
        $info = $db->getRow();
    } else
    {
        $fid = $_GET['fid'];
        $sql = 'SELECT link FROM mirror_links WHERE fileID=? AND hoster=?';
        $result = $db->pQuery($sql, array('ss', $fid, $host == 'MV' ? 'MU' : $host));
        $info = new stdClass();
        $info->link = $db->getVar();
        $info->fileID = $fid;
    }
    if ($result)
    {
        if ($host == 'MU')
            $urlHost = 'http://www.megaupload.com/?d=' . str_replace("http://www.megaupload.com/?d=", '', $info->link);
        else if ($host == 'MV')
            $urlHost = 'http://www.megavideo.com/?d=' . str_replace("http://www.megaupload.com/?d=", '', $info->link);
        else
            $urlHost=$info->link;
        $db->query('UPDATE mirror_files SET downloads=downloads+1 WHERE fileID=\'' . $info->fileID . '\'');
        header('Location: ' . $urlHost);
    } else
    {
        header("HTTP/1.0 404 Not Found");
        echo 'Désolé le fichier demandé n\'existe pas.';
    }
    exit();
}
if ($_GET['type'] == 'mirror')
{
    $id = $_GET['i'];
    $q = $_GET['q'];
    $query = $_GET['s'];
    $sql = 'SELECT l.hoster, f.`name`,f.downloads,f.`lastDl`,f.added FROM mirror_links l JOIN mirror_files f ON f.`fileID` = l.`fileID` WHERE epNum=? AND quality=? AND serieID=?';
    $result = $db->pQuery($sql, array('iss', $id, $q, $query));
} else
{
    $fid = $_GET['fid'];
    $sql = 'SELECT l.hoster, f.`name`,f.downloads,f.`lastDl`,f.added FROM mirror_links l JOIN mirror_files f ON f.`fileID` = l.`fileID` WHERE l.fileID=?';
    $result = $db->pQuery($sql, array('s', $fid));
}

if ($result)
{
    $infos = $db->getRow();
    $hosters = $db->getResults();
    $date = date("j-m-y à H:i", strtotime($infos->added));
    $lastdl = date("j-m-y à H:i", strtotime($infos->lastDl));

    $dl = $infos->downloads;
    $nom = $infos->name;
    $arrayHosters = array();
    foreach (simplexml_load_file('./xml/hosters.xml') as $hoster)
    {
        $arrayHosters[(string) $hoster->short] = $hoster->display;
    }
} else
{
    header("HTTP/1.0 404 Not Found");
    $error = "Désolé l'épisode demandé n'existe pas.";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>


        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Télécharger <?php echo empty($error) ? $nom : ''; ?> sur Gestdown</title>
        <link rel="stylesheet" href="http://css.gestdown.info/miroriii.css,bubbles.css,hoster.css" type="text/css" />
        <script type="text/JavaScript" src="http://js.gestdown.info/rounded_corners.js"></script>
    </head><body>
        <div class="menu">
            <div><a href="http://www.gestdown.info/">Accueil</a></div>
        </div>
        <div class="block">
            <br />

            <div style="text-align: center;"><img width="580" height="150" src="http://style.gestdown.info/gestdownMirror.jpg"  alt="Logo Gestdown Mirror"/></div>
<?php
if (empty($error))
{
?><div id="haut_detail"></div>
            <div id="detail">
                <h1>Télécharger <span class="fichier"><?php echo str_replace('_', ' ', $nom); ?></span></h1>
                <br />
                <br /><br />
                <table width="300" border="0" align="center" class="droite">
                    <tbody><tr>

                            <td width="100" class="left">Nom du fichier :</td>
                            <td class="right"><?php echo $nom; ?></td>
                        </tr>
                        <tr>
                            <td width="100" class="left">Description :</td>
                            <td width="150" class="right"></td>
                        </tr>
                    </tbody></table>
                <table width="335" border="0" align="center" lass="droite">
                    <tbody><tr>

                            <td width="150" class="left">Fichier téléchargé :</td>
                            <td class="right"><?php echo $dl; ?> fois</td>
                        </tr>
                        <tr>
                            <td width="150" class="left">Fichier ajouté le :</td>
                            <td class="right"><?php echo $date; ?></td>
                        </tr>
                        <tr>
                            <td width="175" class="left">Dernier téléchargement :</td>
                            <td width="150" class="right"><?php echo $lastdl; ?></td>
                        </tr>
                    </tbody></table>
            </div>




            <div id="bas_detail"></div> <?php } ?>



            <div align="center">

                <div align="center">


                    <div class="emplacement2" align="center">
                        <p>
                        </p><p><br />

                        </p>
                    </div>
                    <div id="acces">
                        <script language='javascript' type='text/javascript' src='http://a01.gestionpub.com/GP6856a262e5a74e'></script>
                        <div id="dl">Télécharger le fichier !</div>
                        <br />

<?php
        if (empty($error))
        {
            foreach ($hosters as $host)
            {
                if ($host->hoster == 'MU')
                {
                    echo '<div id="' . $host->hoster . '"><a target="_blank" href="./redirect/' . $host->hoster . '/' . $nom . '" rel="nofollow">Télécharger sur ' . $arrayHosters[$host->hoster] . '</a></div>';
                    echo '<div id="MV"><a target="_blank" href="./redirect/MV/' . $nom . '" rel="nofollow">Regarder sur MegaVideo</a></div>';
                }

                else
                    echo '<div id="' . $host->hoster . '"><a target="_blank" href="./redirect/' . $host->hoster . '/' . $nom . '" rel="nofollow">Télécharger sur ' . $arrayHosters[$host->hoster] . '</a></div>';
            }
        } else
            echo $error;
?>

                    </div>

                    <div class="footer">

                    </div></div></body></html>
