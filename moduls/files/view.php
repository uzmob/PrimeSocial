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

/* **** */    
head(''. $lng['Fayl'] .'');

if (isset($_GET['delete'])) {
    @unlink('../../files/usfiles/'.$file['url']);
    
    DB::$dbs->query("DELETE FROM ".FILES_COMM." WHERE `file_id` = ?", array(num($_GET['file'])));
    DB::$dbs->query("DELETE FROM ".FILES_FILE." WHERE `id` = ? ", array(abs(num($_GET['file']))));
    header("Location: ".HOME."/files/".$ank['user_id']."/".$folder['id']."/");
}
 

echo DIV_BLOCK;

if ( strstr($file['url'], 'gif') || strstr($file['url'], 'jpg') || strstr($file['url'], 'jpeg') || strstr($file['url'], 'JPEG') || strstr($file['url'], 'png') || strstr($file['url'], 'GIF') || strstr($file['url'], 'JPG') || strstr($file['url'], 'PNG')) {
    echo '<img src="'.HOME.'/files/usfiles/'.$file['url'].'" height="80" /><br />';
}

echo '<b>' . $file['name'] . '</b> [' . get_size($file['size']) . ']<br /><br />';

   
echo ''.icon('yuklash.png').' <a href="'.HOME.'/files/'.$ank['user_id'].'/'.$folder['id'].'/'.$file['id'].'/download/">'. $lng['Yuklab olish'] .'</a> ['.$file['type'].']<br />';


if ($ank['user_id'] == $user['user_id']) {
    echo ''.icon('trash.png').' <a href="'.HOME.'/files/'.$ank['user_id'].'/'.$folder['id'].'/'.$file['id'].'/delete/">'. $lng['Faylni o`chirish'] .'</a><br />';
}
echo CLOSE_DIV;

echo '<div class="sts" style="font-size:12px;">';
echo ''. $lng['Yuklangan'] .': ' . $file['loads'] . ' <br />';
echo ''. $lng['Joylangan'] .': ' . vrem($file['time']) . '<br />';

$comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES_COMM." WHERE `file_id` = ?", array($file['id']));
echo ''. $lng['Sharhlar'] .': '.$comm.'<br />';

echo CLOSE_DIV;
require_once('comm.php');

require_once('../../core/stop.php');
?>