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

head(''. $lng['Sharhni tahrirlash'] .'');

if ($_POST['edit']) {
    $msg = html($_POST['msg']);
    
    if (empty($msg)) {
        DIV_ERROR . ''. $lng['Habarni kiriting'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".FORUMS_POST." SET `msg` = ? WHERE `id` = ? ", array($msg, $post['id']));
        header("Location: ".HOME."/forum/".$post['forum_id']."/".$post['forumc_id']."/".$post['theme_id']."/");
    }
}

echo DIV_BLOCK;
echo '<form action="#" method="POST">';
echo '<b>'. $lng['Habar'] .':</b> [min. 20]<br />
<textarea name="msg" style="width:95%;height:4pc;">'.$post['msg'].'</textarea><br />';
echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /><br />';
echo '</form>';
echo CLOSE_DIV;

$forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($post['forum_id']));
$forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($post['forumc_id']));
$theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array($post['theme_id']));

require_once('../../core/stop.php');
?>