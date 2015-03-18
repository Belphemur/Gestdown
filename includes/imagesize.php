<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 18/03/2015
 * Time: 17:44
 */
function ranger($url){
    $headers = array(
        "Range: bytes=0-32768"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

/**
 * Return image dimension for remote image
 * @param $url
 * @return stdClass
 */
 function getImageDimension($url) {
     $img = new stdClass();

     $raw = ranger($url);
     $im = imagecreatefromstring($raw);
     $img->width = imagesx($im);
     $img->height = imagesy($im);
     return $img;
 }