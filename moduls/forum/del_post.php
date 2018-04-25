<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$post = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_POST." WHERE `id` = ? ", array(abs(num($_GET['post']))));
$theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array($post['theme_id']));
if (empty($post)) {
    head(''. $lng['Sharh topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

//if (privilegy('forum_moder') == FALSE) {
if ($post['user_id'] != $user['user_id'] && privilegy('forum_moder') == FALSE) {
    head(''. $lng['Kirishda xatolik'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if ($theme['status'] == 1) {
    head(''. $lng['Mavzu yopilgan'] .'');
        
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if (!empty($post['file'])) {
    unlink('../../files/forum/'.$post['file']);
}
DB::$dbs->query("DELETE FROM ".FORUMS_POST." WHERE `id` = ? ", array($post['id']));
header("Location: ".HOME."/forum/".$post['forum_id']."/".$post['forumc_id']."/".$post['theme_id']."/");

require_once('../../core/stop.php');
?>