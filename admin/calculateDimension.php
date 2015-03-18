<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 18/03/2015
 * Time: 18:20
 */
require_once('conf.php');
require_once('../includes/imagesize.php');

$sql = 'SELECT image, id FROM categorie';
$categories = $db->getResults($sql);
$sqlUpdate = 'UPDATE categorie SET width=?, height=? WHERE id=?';
foreach($categories as $cat) {
    echo "ID:",$cat->id,PHP_EOL;
    echo "url:",$cat->image,PHP_EOL;
    $img = getImageDimension($cat->image);
    if(isset($img->error)) {
        print_r( $img->error);
        echo PHP_EOL;
        continue;
    }
    $db->pQuery($sqlUpdate,array('iii',$img->width, $img->height, $cat->id));
}