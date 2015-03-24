<?php
header("Content-Type: application/json");
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 20/03/2015
 * Time: 17:09
 */
require_once('../admin/conf.php');

if (!isset($_POST['id']) || !isset($_POST['type'])) {
    header("HTTP/1.1 404 Not Found");
    echo '{}';
    return;
}

$id = $_POST['id'];
$type = strtoupper($_POST['type']);

if (!is_numeric($id)) {
    header("HTTP/1.1 404 Not Found");
    echo '{}';
    return;
}

if($_POST['downloaded']) {
    $sql = 'UPDATE DirectDownloads SET  downloads=downloads+1 WHERE episode=? AND type = ?';
    if($db->pQuery($sql, array('is', $id, $type))) {
        header("HTTP/1.1 204 No Response");
    } else {
        header("HTTP/1.1 404 Not Found");
    }
    die();
}

$sql = 'SELECT filepath FROM DirectDownloads WHERE episode=? AND type = ?';
$db->pQuery($sql, array('is', $id, $type));
$link = $db->getVar();

if (empty($link)) {
    header("HTTP/1.1 404 Not Found");
    echo '{}';
    return;
}

$object = new stdClass();
$object->dlpath = base64_encode($link);

echo json_encode($object);
$requete = 'UPDATE downloads SET nbhits=nbhits+1 WHERE id=?';
$db->pQuery($requete, array('i', $id));