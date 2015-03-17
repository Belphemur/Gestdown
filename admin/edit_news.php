<?php
require_once 'header.php';
include ('./templates/links.html');

function write_file($filename,$data="")
{
    $f = @fopen($filename,"w");
    if(!$f) return false;
    @fwrite($f,$data);
    @fclose($f);
    return true;
}
function load_file($filename,$all=true)
{
    if(!@is_readable($filename)) return false;

    $f = @fopen($filename,"r");
    $data = @fread($f,$all ? filesize($filename) : $all);
    @fclose($f);
    return $data;
}

if(isset($_POST['description']))
{
    $fn = "../templates/news_pres.txt";
    @chmod($fn,0777);
    if(!file_exists($fn) or !is_writable($fn))
        $center = message("ERROR: File is either not writable or does not exist");
    else
        write_file($fn, str_replace('\r\n', PHP_EOL, $_POST['description']));
    ?>
<div id="content">
    <h2>Editer la news</h2>
    <p> La news a bien été modifiée. <br  />
        <a href="javascript:history.go(-1)">Retour</a>
            <?php }
        else
        {
            chmod("../templates/",0777);
            $fn = "../templates/news_pres.txt";
            if(!file_exists($fn))
                write_file($fn);

            $data = load_file($fn);
            ?>
    <div id="content">
        <h2>Editer la news</h2>
        <p>

        <form name="Formdescription" id="Formdescription" method="post" action="edit_news.php" onSubmit="return false;">
            <table width="200" style="border: #999 solid 1px " border="0">

                <tr>
                    <th scope="row">News:</th>
                    <td> <script language="javascript">initBBcode("description","Prévisualiser",500,400,"<?php echo $db->real_escape($data); ?>",0); </script></td>

            </table>
            <input name="id" type="hidden" value="'.$modifier.'" />
            <input type="submit" name="envoi" value="Modifier la news" onclick="if(valideForm()){document.Formdescription.submit()}"></form><br><br>
            <?php } ?>
        </p>
    </div>
    <div id="footer">
        <?php echo $close; ?>
    </div>

</div>
</body>
</html>