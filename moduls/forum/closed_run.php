<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/

require_once('../../core/start.php');

check_auth();

$theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array(abs(num($_GET['theme']))));
if (empty($theme)) {
    head(''. $lng['Mavzu topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if ( $theme['user_id'] != $user['user_id'] && privilegy('forum_moder') == FALSE ) {
    head(''. $lng['Kirishda xatolik'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit();     
}

$status = ($theme['status'] == 0 ? 1 : 0);
DB::$dbs->query("UPDATE ".FORUM_THEME." SET `status` = ? WHERE `id` = ? ", array($status, $theme['id']));
header("Location: ".HOME."/forum/".$theme['forum_id']."/".$theme['forumc_id']."/".$theme['id']."/");
?>