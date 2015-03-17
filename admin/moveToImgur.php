<?php

include_once('conf.php');
$db->disableDiskCache();

function sendToImgur($image, $name) {
    global $imgur, $nom_site;

    // $data is file data
    $pvars = array('image' => $image, 'key' => $imgur['API_KEY'], 'name' => $name, 'title' => $nom_site . ' : ' . $name, 'type' => 'url');
    $timeout = 30;
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'http://api.imgur.com/2/upload.json');
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);

    $json = json_decode(curl_exec($curl));
    curl_close($curl);
    if (isset($json->upload))
        return $json->upload->links->original;
    return null;
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$sql = 'SELECT id,nom,screen FROM `downloads`
    WHERE `screen` IS NOT NULL AND `screen` !=""
    AND `screen` NOT LIKE "%imagidream.info%"
    AND`screen` NOT LIKE "%imgur%"
    ORDER BY id ASC LIMIT 25';
$results = $db->getResults($sql);
$sqlUpdate = "UPDATE downloads SET screen=? WHERE id=?";
foreach ($results as $res) {
    $screen = sendToImgur($res->screen, $res->nom);
    if (empty($screen))
        continue;
    $db->preparedQuery($sqlUpdate, array('si', $screen, $res->id));
    echo 'id :', $res->id, ' screen -> ', $screen, '<br />',PHP_EOL;
}
?>
