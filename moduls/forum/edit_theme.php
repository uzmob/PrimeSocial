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

if ($theme['status'] == 1) {
    head(''. $lng['Mavzu yopilgan'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if ($theme['user_id'] != $user['user_id'] && !privilegy('forum_moder')) {
    header("Location: ".HOME."");
}

head(''. $lng['Tahrirlash'] .': ' . $theme['name']);
        

if ($_POST['edit']) {
    
    $name = html($_POST['name']);
    $uvedom = abs(num($_POST['uvedom']));
    $vote = html($_POST['vote']);
    $vote_1 = html($_POST['vote_1']);
    $vote_2 = html($_POST['vote_2']);
    $vote_3 = html($_POST['vote_3']);
    $vote_4 = html($_POST['vote_4']);
    $vote_5 = html($_POST['vote_5']);
    $vote_6 = html($_POST['vote_6']);
    $vote_7 = html($_POST['vote_7']);
    $vote_8 = html($_POST['vote_8']);
    $vote_9 = html($_POST['vote_9']);
    $vote_10 = html($_POST['vote_10']);
    
    if (empty($name)) {
        $err = ''. $lng['Mavzu nomini kiriting'] .'.<br />';
    }
    
    if (strlen($name) < 8) {
        $err .= ''. $lng['Mavzu nomi juda qisa'] .'. [min. 8]<br />';
    }

    if (!empty($vote) && strlen($vote) < 20) {
        $err .= ''. $lng['So`rovnoma nomi juda qisqa'] .'. [min. 10]<br />';
    }
    
    if (!empty($vote) && (empty($vote_1) || empty($vote_2))) {
        $err .= ''. $lng['So`rovnomaning asosiy variantlarini to`ldiring'] .'';
    }

    if (empty($err)) {

        DB::$dbs->query("UPDATE ".FORUM_THEME." SET `name` = ?, `uvedom` = ?, `vote` = ?, `vote_1` = ?, `vote_2` = ?, `vote_3` = ?, `vote_4` = ?, `vote_5` = ?, `vote_6` = ?, `vote_7` = ?, `vote_8` = ?, `vote_9` = ?, `vote_10` = ? WHERE `id` = ? ", 
        array($name, $uvedom, $vote, $vote_1, $vote_2, $vote_3, $vote_4, $vote_5, $vote_6, $vote_7, $vote_8, $vote_9, $vote_10, $theme['id']));

        header("Location: ".HOME."/forum/".$theme['forum_id']."/".$theme['forumc_id']."/".$theme['id']."/");
    
    } else {
        echo DIV_ERROR . $err . CLOSE_DIV;
    }            
}
echo '<div class="white"><form action="#" enctype="multipart/form-data" method="POST">';
echo '<b>'. $lng['Mavzu nomi'] .':</b> [min. 8]<br />
<input type="text" name="name" value="'.$theme['name'].'" style="width:95%;"/><br />';
echo '<input type="checkbox" name="uvedom" value="1" '.($theme['uvedom'] ? 'checked' : NULL).' /> '. $lng['Mavzuni kuzatish'] .' ';

echo '</div><div class="white">';
echo '<b>'. $lng['So`rovnoma kiritish'] .'</b> ('. $lng['shart emas'] .')<br/><b>'. $lng['Nomi'] .':</b> [min. 10]<br />
<input type="text" name="vote" value="'.$theme['vote'].'" style="width:95%;"/><br />';
echo '<b>'. $lng['Javob variantlari'] .':</b><br /> 
<span class="mini">'. $lng['Kamida 2 ta maydoncha to`ldirilishi shart'] .'</span><br /><br />';
echo '<input type="text" name="vote_1" value="'.$theme['vote_1'].'" placeholder="1" style="width:95%;"/><br />';
echo '<input type="text" name="vote_2" value="'.$theme['vote_2'].'" placeholder="2" style="width:95%;"/><br />';
echo '<input type="text" name="vote_3" value="'.$theme['vote_3'].'" placeholder="3" style="width:95%;"/><br />';
echo '<input type="text" name="vote_4" value="'.$theme['vote_4'].'" placeholder="4" style="width:95%;"/><br />';
echo '<input type="text" name="vote_5" value="'.$theme['vote_5'].'" placeholder="5" style="width:95%;"/><br />';
echo '<input type="text" name="vote_6" value="'.$theme['vote_6'].'" placeholder="6" style="width:95%;"/><br />';
echo '<input type="text" name="vote_7" value="'.$theme['vote_7'].'" placeholder="7" style="width:95%;"/><br />';
echo '<input type="text" name="vote_8" value="'.$theme['vote_8'].'" placeholder="8" style="width:95%;"/><br />';
echo '<input type="text" name="vote_9" value="'.$theme['vote_9'].'" placeholder="9" style="width:95%;"/><br />';
echo '<input type="text" name="vote_10" value="'.$theme['vote_10'].'" placeholder="10" style="width:95%;"/><br /><br />';

echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /><br />';
echo '</form>';
echo CLOSE_DIV;

$forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($theme['forum_id']));
$forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($theme['forumc_id']));
require_once('../../core/stop.php');
?>