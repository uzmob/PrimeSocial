<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');
check_auth();

$W = num($_GET['W']);
$H = num($_GET['H']);

header('Content-type: image/jpeg');

$file = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_FILE." WHERE `id` = ? ", array(abs(num($_GET['id']))));

$file['url'] = '../../files/loads/files/' . $file['url'];

$pic = urldecode(htmlspecialchars($file['url']));


if($file['type'] == '.gif') { 
    $old = imageCreateFromGif($pic);
} elseif($file['type'] == '.jpg') {
    $old = imageCreateFromJpeg($pic);
} elseif($file['type'] == '.png') {
    $old = imageCreateFromPNG($pic);
}

$wn = imageSX($old);
$hn = imageSY($old);


$new = imageCreateTrueColor($W, $H);
imageCopyResampled($new, $old, 0, 0, 0, 0, $W, $H, $wn, $hn);

imageJpeg($new,null,100);

DB::$dbs->query("UPDATE ".LOADS_FILE." SET `loads` = ? WHERE `id` = ? ", array( (++$file['loads']), $file['id'] ));

require_once('../../core/stop.php');
?>