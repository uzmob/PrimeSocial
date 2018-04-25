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

$id = abs(num($_GET['user']));
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));

if (empty($ank)) {
    head(''. $lng['Foydalanuvchi topilmadi'] .'');
    echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
    exit();
}

$folder = DB::$dbs->queryFetch("SELECT * FROM ".FILES." WHERE `id` = ? && `user_id` = ? ", array(abs(num($_GET['folder'])), $ank['user_id']));
    if (empty($folder)) {
    head(''. $lng['Bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 

$file = DB::$dbs->queryFetch("SELECT * FROM ".FILES_FILE." WHERE `id` = ? ", array(abs(num($_GET['file']))));
if (empty($file)) {
    head(''. $lng['Fayl topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}  

DB::$dbs->query("UPDATE ".FILES_FILE." SET `loads` = ? WHERE `id` = ? ", array( (++$file['loads']), $file['id'] ));

header("Location: ".HOME."/files/usfiles/".$file['url']."");


require_once('../../core/stop.php');
?>