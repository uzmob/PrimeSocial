<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');
require_once('../../core/class/id.php'); 

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

/* **** */    
head(''. $lng['Fayl reytingi'] .': ' . $file['name']); 

if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_RATING." WHERE `file_id` = ? && `user_id` = ? ", array($file['id'], $user['user_id'])) == TRUE) {
    head(''. $lng['Siz ovoz bergansiz'] .''); 
    echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
    require_once('../../core/stop.php');
    exit();        
}
    
if ($_GET['type'] == 'plus') {
    DB::$dbs->query("INSERT INTO ".LOADS_RATING." (`file_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($file['id'], $user['user_id'], 'plus'));
    DB::$dbs->query("UPDATE ".LOADS_FILE." SET `rating` = ? WHERE `id` = ? ", array(($file['rating'] + 1), $file['id']));
} else {
    DB::$dbs->query("INSERT INTO ".LOADS_RATING." (`file_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($file['id'], $user['user_id'], 'minus'));
    DB::$dbs->query("UPDATE ".LOADS_FILE." SET `rating` = ? WHERE `id` = ? ", array(($file['rating'] - 1), $file['id']));
}

header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$file['id']."/");

require_once('../../core/stop.php');
?>