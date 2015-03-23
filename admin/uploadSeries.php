<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 23/03/2015
 * Time: 09:05
 */
set_time_limit(0); // Removes the time limit, so it can upload as many as possible
ini_alter("memory_limit", "1024M"); // Set memory limit, in case it runs out when processing large files
require_once('conf.php');
require_once('../includes/uploadEpisodeFunctions.php');
require_once('header.php');
include('templates/linksUp.html');

if(isset($_POST['folders'])) {
 foreach ($_POST['folders'] as $folder) {
     foreach(getFileList($folder) as $file) {
         sendFileToJheberg($file);
     }
 }
    die();

}
?>
    <form name="flist" method="post" action="uploadSeries.php">

<?php
plotTree(getDirectories($episodeDir));
?>
        <input type="submit" name="submit" value="Upload"/>
    </form>