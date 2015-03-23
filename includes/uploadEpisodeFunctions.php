<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 23/03/2015
 * Time: 09:03
 */
require_once ('../admin/conf.php');
include_once('host.php');
function getFileList($directory)
{
    global $epExt;
    $files = glob($directory . DIRECTORY_SEPARATOR . '*.' . $epExt, GLOB_BRACE);
    usort($files, function ($a, $b) {
        return filemtime($a) < filemtime($b);
    });
    return $files;
}

function getDirectories($root) {
    $result = array();
    $root = rtrim($root, '/');

    $cdir = scandir($root);
    foreach ($cdir as $key => $value)
    {
        if (!in_array($value,array(".","..")))
        {
            if (is_dir($root . DIRECTORY_SEPARATOR . $value))
            {
                $dir = new stdClass();
                $dir->path = $root . DIRECTORY_SEPARATOR . $value;
                $dir->name = $value;
                $dir->next = getDirectories($root . DIRECTORY_SEPARATOR . $value);
                $result[$value] = $dir;
            }

        }
    }

    return $result;
}

function plotTree($arr, $indent=0, $mother_run=true){
    if ($mother_run) {
        // the beginning of plotTree. We're at rootlevel
       echo '<div class="tree">';
    }

    foreach ($arr as $k=>$v){
        // skip the baseval thingy. Not a real node.
        if ($k == "path" || $k == "name") continue;
        // determine the real value of this node.
        $show_val = (is_array($v) && !empty($v) ? $v["path"] : $v);
        // show the indents
        echo str_repeat("  ", $indent);
        echo "<ul><li>";

        // show the actual node
        echo '<input type="checkbox" name="folders[]" value="' . $show_val->path . '" />',$k, '</li>';
        if (is_array($v->next)) {
            // this is what makes it recursive, rerun for childs
            plotTree($v->next, ($indent+1), false);
        }
        echo "</ul>".PHP_EOL;
    }

    if ($mother_run) {
        echo "</div>";
    }
}


function getDirectoryListOld($directory)
{
    global $epExtArray;
    // create an array to hold directory list
    $results = array();
    // create a handler for the directory
    $handler = opendir($directory);
    // open directory and walk through the filenames
    while ($file = readdir($handler)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        // if file isn't this directory or its parent, add it to the results
        if ($file != "." && $file != ".." && in_array($ext, $epExtArray)) {
            $results[] = $directory.DIRECTORY_SEPARATOR.$file;
        }
    }
    // tidy up: close the handler
    closedir($handler);
    // done!
    return $results;
}

function getServer()
{
    global $jheberg;
    $ch = curl_init();
    $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $jheberg['url']);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result);
    return $data->url . $jheberg['upload_page'];
}

function sendFileToJheberg($name)
{
    global $jheberg;
    $uploadUrl = getServer();

    //This needs to be the full path to the file you want to send.
    $file_name_with_full_path = realpath($name);
    /* curl will accept an array here too.
     * Many examples I found showed a url-encoded string instead.
     * Take note that the 'key' in the array will be the key that shows up in the
     * $_FILES array of the accept script. and the at sign '@' is required before the
     * file name.
     */
    $file = new CURLFile($file_name_with_full_path);
    $post = array('username' => $jheberg['user'], 'password' => $jheberg['pass'], 'file' => $file);

    $ch = curl_init();
    $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $uploadUrl, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $post);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result);
    if(empty($data->url)) {
        echo "Problem while uploading the episode <br />";
    } else {
        $episodeId = addEpisodeDB($name, $data->url);
        if($episodeId == -1) {
            return;
        }
        moveEpsiode($name, $file_name_with_full_path, $episodeId);
    }
}

/**
 * @param $name
 * @param $file_name_with_full_path
 * @param $epiId
 * @return string
 * @internal param $episodeDir
 */
function moveEpsiode($name, $file_name_with_full_path, $epiId)
{
    global $episodeDir, $episodeHttpPath, $db;
    $serie = '';
    list($num, $serie, $qual) = linkInformations($name, true);
    $name = basename($name);
    $fullPath = $episodeDir . '/' . $serie;
    mkdir($fullPath, 0775, true);
    $newName = $fullPath . '/' . $name;
    rename($file_name_with_full_path, $newName);
    chmod($newName, 0775);
    $dlPath = $episodeHttpPath . '/' . $serie . '/' . $name;
    $sql = "INSERT INTO `DirectDownloads` (`episode`, `type`, `filepath`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE
            filepath=VALUES(filepath)";
    if(!$db->pQuery($sql,array('iss',$epiId,$qual,$dlPath))) {
        echo 'Impossible de définir le DDL';
    }
}

function addEpisodeDB($file, $link)
{
    global $db;
    $serie;
    $num;
    $qual;
    $MQ = '';
    $HD = '';
    $FHD = '';

    list($num, $serie, $qual) = linkInformations($file, true);
    $$qual = $link;
    $sql = "SELECT Count(d.id) cid, d.id id, d.lien, d.lien2, d.lien3
                FROM downloads d
                INNER JOIN categorie c
                ON c.id=d.categorie
                WHERE c.nom LIKE ? AND (d.nom= ? OR d.nom =?)
                GROUP BY d.id";
    $epNum = 'Episode ' . $num;
    $epNum2 = 'Episode ' . intval($num);
    try {
        $db->pQuery($sql, array('sss', $serie, $epNum, $epNum2));
        $episode = $db->getRow();
        if (is_null($episode)) {
            $date = time();
            $sql = "INSERT INTO `downloads`
                (`id`, `categorie`, `nom`, `date`, `description`, `auteur`, `lien`, `lien2`, `lien3`, `torrentMQ`, `torrentHD`, `torrentFHD`, `screen`, `actif`, `nbhits`, `mort`)
                VALUES
                (NULL, (SELECT c.id FROM categorie c WHERE c.nom LIKE ?), ?, ?, '', 'Ame no Tsuki', ?, ?, ?, '', '', '', NULL, '0', '0', '0')";

            if ($db->pQuery($sql, array('ssisss', $serie, $epNum, $date, $MQ, $HD, $FHD))) {
                $episodeId = $db->getLastID();
                echo 'Ajout de l\'épisode dans la base de donnée : ' . $file . '<br />' . PHP_EOL;
                $num = $db->getVar("SELECT COUNT(news_id) FROM rss");
                if ($num > 7) {
                    $nb = $db->getVar('SELECT MIN(news_id) FROM rss');

                    $db->pQuery("DELETE FROM rss WHERE news_id=?", array('i', $nb));
                }
                $db->query("INSERT INTO rss VALUES('','EPISODE','','',(SELECT MAX(id) FROM downloads))");

            } else
                echo 'Ajout FAILED : ' . $file . ' <br />' . PHP_EOL;
        } else {
            $sql = 'UPDATE downloads ';
            $boundParams = array();
            $type = '';
            $links = array($episode->lien, $episode->lien2, $episode->lien3);
            $episodeId = $episode->id;
            if (!empty($MQ) && !in_array($MQ, $links)) {
                $boundParams[] = $MQ;
                $type .= 's';
                $sql .= "SET lien=?";
            } else if (!empty($HD) && !in_array($HD, $links)) {
                $boundParams[] = $HD;
                $type .= 's';
                $sql .= "SET lien2=?";
            } else if (!empty($FHD) && !in_array($FHD, $links)) {
                $boundParams[] = $FHD;
                $type .= 's';
                $sql .= "SET lien3=?";
            }
            if (empty($type))
                echo 'Aucune modification effectuée sur l\'épisode (' . $episode->id . ') le lien est valide<br />' . PHP_EOL;
            else {
                $type .= 'i';
                $boundParams[] = $episode->id;
                $sql .= ", mort=0 WHERE id=?";
                array_unshift($boundParams, $type);
                if ($db->pQuery($sql, $boundParams))
                    echo 'Modification de l\'épisode (' . $episode->id . ') dans la base de donnée ';
                else
                    echo 'DB MOD FAILED (' . $episode->id . ') ';
                if ($db->pQuery('DELETE FROM `descriptions` WHERE `mort`!=0 AND download=?', array('i', $episode->id)))
                    echo ' -- Suppression du lien mort effectuée.';
                echo '<br />' . PHP_EOL;
            }
        }
        return $episodeId;
    } catch (Exception $e) {
        echo 'ERROR : ' . $serie . ' n\'existe pas.<br />' . PHP_EOL;
        print_r($e);
        return -1;
    }
}
