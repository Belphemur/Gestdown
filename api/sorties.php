<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 08-10-14
 * Time: 10:44
 */
require_once('../admin/conf.php');
$db->disableDiskCache();
$num = $_GET['nb'];
if ($num < 0 || $num > 20) {
    $num = 20;
}
$bindParam = array('i', $num);


$sql = 'SELECT d.id, d.nom episode, c.nom serie, d.screen, d.date FROM  downloads d
LEFT JOIN categorie c
ON c.id= d.categorie
WHERE d.actif = 1
ORDER BY d.date DESC
LIMIT ?';

$db->pQuery($sql, $bindParam);

$datas = $db->getResults(false, false);
$lastEpDate = $datas[0]['date'];
//http_cache_last_modified($lastEpDate);
header("Content-Type: application/json");
echo json_encode($datas);