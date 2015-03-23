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


require_once 'header.php';
require_once('../includes/uploadEpisodeFunctions.php');
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
    $name = pathinfo($key, PATHINFO_BASENAME);
    $files .= ' <tr>
    <td><input type="checkbox" name="files[]" value="' . $key . '" /></td>
    <td>' . $name . '</td>
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
