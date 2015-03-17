<?php

header('Content-type: text/html; charset=utf-8');
$myUploadsTxt[] = '/var/www/gestdown.info/web/rapid/files/myuploads.txt';
$myUploadsTxt[] = 'http://imagidream.eu/upload2/files/myuploads.txt';
if (isset($_GET['url']))
    $myUploadsTxt[] = $_GET['url'];

include_once('conf.php');
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->query("SET NAMES 'utf8'");
include_once('../includes/addSerieXml.php');
include_once('../includes/host.php');
$i = 0;
$max = count($myUploadsTxt);
while ($i < $max)
{
    $result = autoXml($myUploadsTxt[$i], false);
    if ($result === false)
    {
        $i++;
        while ($i < $max && $result === false)
        {
            $result = autoXml($myUploadsTxt[$i], false);
            $i++;
        }
    }

    if ($result === false)
    {
        die('Tout les fichiers possible ont été parsé.' . PHP_EOL);
    }
    else
        echo PHP_EOL . '<br /> Fichier : ' . $myUploadsTxt[$i] . '<br />' . PHP_EOL . '===================================================(' . date("d-m-Y H:i:s") . ')<br />' . PHP_EOL;
    foreach ($result as $file => $linkInfo)
    {
        $info = explode(',', $linkInfo);
        $link = $info[0];
        $xmlResult = $info[1];
        echo 'MIRROR RESULT -> ' . $xmlResult . ' (' . $file . '):              ';
        $serie;
        $num;
        $qual;
        $MQ = '';
        $HD = '';
        $FHD = '';

        list($num, $serie, $qual) = linkInformations($file, true);
        $$qual = $url_site . $link;
        $sql = "SELECT Count(d.id) cid, d.id id, d.lien, d.lien2, d.lien3
                FROM downloads d
                INNER JOIN categorie c
                ON c.id=d.categorie
                WHERE c.nom LIKE ? AND d.nom= ?
                GROUP BY d.id";
        $epNum = 'Episode ' . $num;
        try
        {
            $db->pQuery($sql, array('ss', $serie, $epNum));
            $episode = $db->getRow();
            if (is_null($episode))
            {
                $date = time();
                $sql = "INSERT INTO `downloads`
                (`id`, `categorie`, `nom`, `date`, `description`, `auteur`, `lien`, `lien2`, `lien3`, `torrentMQ`, `torrentHD`, `torrentFHD`, `screen`, `actif`, `nbhits`, `mort`)
                VALUES
                (NULL, (SELECT c.id FROM categorie c WHERE c.nom LIKE ?), ?, ?, '', 'Ame no Tsuki', ?, ?, ?, '', '', '', NULL, '0', '0', '0')";

                if ($db->pQuery($sql, array('ssisss', $serie, $epNum, $date, $MQ, $HD, $FHD)))
                {
                    echo 'Ajout du mirror dans la base de donnée : ' . $file . '<br />' . PHP_EOL;
                    $num = $db->getVar("SELECT COUNT(news_id) FROM rss");
                    if ($num > 7)
                    {
                        $nb = $db->getVar('SELECT MIN(news_id) FROM rss');

                        $db->pQuery("DELETE FROM rss WHERE news_id=?", array('i', $nb));
                    }
                    $db->query("INSERT INTO rss VALUES('','EPISODE','','',(SELECT MAX(id) FROM downloads))");
                }
                else
                    echo 'Ajout FAILED : ' . $file . ' <br />' . PHP_EOL;
            }
            else
            {
                $sql = 'UPDATE downloads ';
                $boundParams = array();
                $type = '';
                $links = array($episode->lien, $episode->lien2, $episode->lien3);
                if (!empty($MQ) && !in_array($MQ, $links))
                {
                    $boundParams[] = $MQ;
                    $type.='s';
                    $sql.="SET lien=?";
                } else if (!empty($HD) && !in_array($HD, $links))
                {
                    $boundParams[] = $HD;
                    $type.='s';
                    $sql.="SET lien2=?";
                } else if (!empty($FHD) && !in_array($FHD, $links))
                {
                    $boundParams[] = $FHD;
                    $type.='s';
                    $sql.="SET lien3=?";
                }
                if (empty($type))
                    echo 'Aucune modification effectuée sur l\'épisode (' . $episode->id . ') le lien est valide<br />' . PHP_EOL;
                else
                {
                    $type.='i';
                    $boundParams[] = $episode->id;
                    $sql.=", mort=0 WHERE id=?";
                    array_unshift($boundParams, $type);
                    if ($db->pQuery($sql, $boundParams))
                        echo 'Modification de l\'épisode (' . $episode->id . ') dans la base de donnée ';
                    else
                        echo 'DB MOD FAILED (' . $episode->id . ') ';
                    if($db->pQuery('DELETE FROM `descriptions` WHERE `mort`!=0 AND download=?',array('i',$episode->id)))
                            echo ' -- Suppression du lien mort effectuée.';
                    echo '<br />' . PHP_EOL;
                }
            }
        } catch (Exception $e)
        {
            echo 'ERROR : '.$serie.' n\'existe pas.<br />'.PHP_EOL;
        }
    }
    $i++;
}
?>
