<?php
require_once("../classes/rim.php");
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 18/03/2015
 * Time: 17:44
 */

/**
 * Return image dimension for remote image
 * @param $url
 * @return stdClass
 */
 function getImageDimension($url) {
     $img = new stdClass();

     $rim = new rim();
     $image_data = $rim->getSingleImageTypeAndSize($url);
     if(isset($image_data['error'])) {
         $img->error = $image_data['error'];
         return $img;
     }
     $img->width = $image_data['width'];
     $img->height = $image_data['height'];
     return $img;
 }