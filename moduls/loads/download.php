<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

$folder = DB::$dbs->queryFetch("SELECT * FROM ".LOADS." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
if (empty($folder)) {
    head(''. $lng['Bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 
    
$folderc = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_CAT." WHERE `id` = ? ", array(abs(num($_GET['folderc']))));
if (empty($folderc)) {
    head(''. $lng['Ichki bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}    

$file = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_FILE." WHERE `id` = ? ", array(abs(num($_GET['file']))));
if (empty($file)) {
    head(''. $lng['Fayl topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 

DB::$dbs->query("UPDATE ".LOADS_FILE." SET `loads` = ? WHERE `id` = ? ", array( (++$file['loads']), $file['id'] ));

header("Location: ".HOME."/files/loads/files/".$file['url']."");


require_once('../../core/stop.php');
?>