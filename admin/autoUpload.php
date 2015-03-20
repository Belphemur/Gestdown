<?php
include_once('../includes/host.php');
set_time_limit(0); // Removes the time limit, so it can upload as many as possible
ini_alter("memory_limit", "1024M"); // Set memory limit, in case it runs out when processing large files
$url = '../rapid/files/myuploads.txt';
if (isset($_POST['action']) && $_POST['action'] == 'delmyup') {
    if (@unlink($url))
        die('Fichier supprimé');
    else
        die('Fichier non supprimé, peut-être introuvable');
}

function getDirectoryList($directory)
{
    global $epExt;
    $files = glob($directory . '/' . '*.' . $epExt, GLOB_BRACE);
    usort($files, function ($a, $b) {
        return filemtime($a) < filemtime($b);
    });
    return $files;
}

function getDirectoryListOld($directory)
{
    global $epExt;
    // create an array to hold directory list
    $results = array();
    // create a handler for the directory
    $handler = opendir($directory);
    // open directory and walk through the filenames
    while ($file = readdir($handler)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        // if file isn't this directory or its parent, add it to the results
        if ($file != "." && $file != ".." && in_array($ext, $epExt)) {
            $results[] = $file;
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
    global $jheberg, $episodeDir;
    $uploadUrl = getServer();

    //This needs to be the full path to the file you want to send.
    $file_name_with_full_path = realpath($episodeDir . '/' . $name);
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
    $fullPath = $episodeDir . '/' . $serie;
    mkdir($fullPath, 0775, true);
    $newName = $fullPath . '/' . $name;
    rename($file_name_with_full_path, $newName);
    chmod($newName, 0775);
    $dlPath = $episodeHttpPath . '/' . $serie . '/' . $name;
    $sql = "INSERT INTO `DirectDownloads` (`id`, `episode`, `type`, `filepath`) VALUES (NULL, ?, ?, ?);";
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

require_once 'header.php';
include('./templates/linksUp.html');
if (isset($_GET['action']) && $_GET['action'] == 'upload') {
    if (isset($_POST["files"])) {
        foreach ($_POST["files"] as $file) {
            sendFileToJheberg($file);
        }
    } else
        echo "Aucun fichier sélectionné";
}
$files = "";
foreach (getDirectoryList($episodeDir) as $key) {
    $key = pathinfo($key, PATHINFO_BASENAME);
    $files .= ' <tr>
    <td><input type="checkbox" name="files[]" value="' . $key . '" /></td>
    <td>' . $key . '</td>
    </tr>';
}
?>
<script type="text/javascript">
    function delMyUp() {
        if (confirm('Êtes-vous sûr de vouloir vider le fichier txt ?')) {
            $.ajax({
                type: "POST",
                url: "./autoUpload.php",
                data: "action=delmyup",
                success: function (data) {
                    $("#result").html(data);
                    $("#result").fadeIn(750);
                }
            });
        }
    }
</script>


<?php
exec("cd /var/www/gestdown.info/web/ant/Episodes/; chmod 0777 *.avi; chmod 0777 *.mp4; chmod 0777 *.mkv");
?>
<div id="content">

    <h2>Choisser le fichier que vous voulez upper.</h2>


    <a href="javascript:delMyUp()">Vider le fichier contenant les urls.</a><br/>
    <br/>

    <div id="result"
         style="display: none; border: 1px solid black; width: 500px; background-color: white; color: blue;"></div>

    <form name="flist" method="post" action="autoUpload.php?action=upload">
        <table><?php echo $files ?></table>
        <input type="submit" name="submit" value="Upload"/>
    </form>

</div>
<div id="myupload"
     style="display: none;border: 1px solid black; width: 500px; background-color: black; color: white;"></div>

<div id="footer">
    <?php echo $close; ?>
</div>
</div>
</body>
</html>
