<?php
header("Content-Type: application/json");
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 20/03/2015
 * Time: 17:09
 */
require_once('../admin/conf.php');
$id = $_POST['id'];
$type = $_POST['type'];

if(!is_numeric($id)) {
    header("HTTP/1.1 404 Not Found");
    echo '{}';
    return;
}

$sql= 'SELECT filepath FROM DirectDownloads WHERE episode=? AND type = ?';
$db->pQuery($sql,array('is',$id,$type));
$link = $db->getVar();

if(empty($link)) {
    header("HTTP/1.1 404 Not Found");
    echo '{}';
    return;
}

$object = new stdClass();
$object->dlpath = $link;

echo json_encode($object);