<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$variant = abs(num($_POST['variant']));
$theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array(abs(num($_GET['theme']))));
if (empty($theme)) {
    head(''. $lng['Mavzu topilmadi'] .'');
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

if (empty($theme['vote'])) {
    head(''. $lng['Mavzuda so`rovnoma ochilmagan'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if (empty($theme['vote_'.$variant]) || $variant > 10) {
    head(''. $lng['Mavjud bo`lmagan variant'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_VOTE." WHERE `theme_id` = ? && `user_id` = ? ", array($theme['id'], $user['user_id'])) == TRUE) {
    head(''. $lng['Siz ovoz bergansiz'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit();    
}

head(''. $lng['So`rovnoma'] .': ' . $theme['name']);

$forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($theme['forum_id']));
$forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($theme['forumc_id']));

DB::$dbs->query("INSERT INTO ".FORUM_VOTE." (`theme_id`, `user_id`, `variant`) VALUES (?,?,?)", array($theme['id'], $user['user_id'], $variant));

header("Location: ".HOME."/forum/".$forum['id']."/".$forumc['id']."/".$theme['id']."/");

require_once('../../core/stop.php');
?>