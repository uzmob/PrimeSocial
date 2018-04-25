<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array(abs(num($_GET['forum']))));
if (empty($forum)) {
    head(''. $lng['Forum topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

$forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['forumc']))));
if (empty($forumc)) {
    head(''. $lng['Podforum topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}
    
head(''. $lng['Yangi mavzu'] .' |  ' . $forumc['name']);
        


if ($_POST['add']) {
    
    $name2 = html($_POST['name']);
    $msg = html($_POST['msg']);
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
    
    if (empty($name2)) {
        $err = ''. $lng['Mavzu nomini kiriting'] .'.<br />';
    }
    
    if (strlen($name2) < 8) {
        $err .= ''. $lng['Mavzu nomi juda qisqa'] .'. [min. 8]<br />';
    }
    
    if (empty($msg)) {
        $err .= ''. $lng['Habarni kiriting'] .'.<br />';
    }
    
    if (strlen($msg) < 20) {
        $err .= ''. $lng['Juda qisqa habar'] .'. [min. 20]<br />';
    }
    
    if (!empty($vote) && strlen($vote) < 20) {
        $err .= ''. $lng['So`rovnoma nomi juda qisqa'] .'. [min. 10]<br />';
    }
    
    if (!empty($vote) && (empty($vote_1) || empty($vote_2))) {
        $err .= ''. $lng['So`rovnomaning asosiy variantlarini to`ldiring'] .'';
    }
    
    if (!empty($_FILES['file'])) {
        $name = $_FILES['file']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name, '.')); # Fayl formati
        $size = $_FILES['file']['size']; # Fayl hajmi
        $time = time();
        $file = $time.$ext;

        if ($size > (1048576 * $config['max_upload_forum'])) {
            $err .= ''. $lng['Fayl hajmi belglangan miqdordan ortmoqda'] .'. [Max. '.$config['max_upload_forum'].'Mb.]<br />';
        }
            
        if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
            $err .= ''. $lng['Fayl formatida xatolik'] .'.<br />';
        }
    }
            
    if (empty($err)) {
        if (!empty($ext)) {
            copy($_FILES['file']['tmp_name'], '../../files/forum/'.$time.$ext);
        }
        
        $file = (empty($ext) ? 0 : $file);
        
        DB::$dbs->query("INSERT INTO ".FORUM_THEME." (`forum_id`, `forumc_id`, `name`, `user_id`, `uvedom`, `time`, `vote`, `vote_1`, `vote_2`, `vote_3`, `vote_4`, `vote_5`, `vote_6`, `vote_7`, `vote_8`, `vote_9`, `vote_10`, `activ`) VALUES 
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array($forum['id'], $forumc['id'], $name2, $user['user_id'], $uvedom, time(), $vote, $vote_1, $vote_2, $vote_3, $vote_4, $vote_5, $vote_6, $vote_7, $vote_8, $vote_9, $vote_10, time()));
                
        $lastid = DB::$dbs->lastInsertId();
        
        DB::$dbs->query("INSERT INTO ".FORUM_POST." (`forum_id`, `forumc_id`, `theme_id`, `msg`, `user_id`, `time`, `file`) VALUES 
        (?,?,?,?,?,?,?)", array($forum['id'], $forumc['id'], $lastid, $msg, $user['user_id'], time(),$file));
        
        balls_operation(10);
        rating_operation(1);
        
        header("Location: ".HOME."/forum/".$forum['id']."/".$forumc['id']."/".$lastid."/");
    
    } else {
        echo DIV_ERROR . $err . CLOSE_DIV;
    }            
}


	
echo DIV_BLOCK;
echo '<form action="#" enctype="multipart/form-data" method="POST">';
echo '<b>'. $lng['Mavzu nomi'] .':</b> [min. 8]<br />
<input type="text" name="name" style="width:95%;"/><br /><br />';
echo '<b>'. $lng['Habar'] .':</b> [min. 20]<br />
<textarea name="msg" style="width:95%;height:5pc;"></textarea><br />';

echo '<b>'. $lng['Fayl biriktirish'] .':</b> [max. '.$config['max_upload_forum'].'mb.]<br />
<input type="file" name="file" style="width:95%;"/><br /><br />';
echo '<input type="checkbox" name="uvedom" value="1" /> '. $lng['Mavzuni kuzatish'] .' <br /><br />';

echo '<b>'. $lng['So`rovnoma kiritish'] .'</b> ('. $lng['shart emas'] .')<br/>
<b>'. $lng['Nomi'] .':</b> [min. 10]<br />';
echo '<input type="text" name="vote" style="width:95%;"/><br />';
echo '<b>'. $lng['Javob variantlari'] .':</b> <br/>
<span class="mini">'. $lng['Kamida 2 ta maydoncha to`ldirilishi shart'] .'</span><br />';
echo '<input type="text" name="vote_1" placeholder="1" style="width:95%;"/><br />';
echo '<input type="text" name="vote_2" placeholder="2" style="width:95%;"/><br />';
echo '<input type="text" name="vote_3" placeholder="3" style="width:95%;"/><br />';
echo '<input type="text" name="vote_4" placeholder="4" style="width:95%;"/><br />';
echo '<input type="text" name="vote_5" placeholder="5" style="width:95%;"/><br />';
echo '<input type="text" name="vote_6" placeholder="6" style="width:95%;"/><br />';
echo '<input type="text" name="vote_7" placeholder="7" style="width:95%;"/><br />';
echo '<input type="text" name="vote_8" placeholder="8" style="width:95%;"/><br />';
echo '<input type="text" name="vote_9" placeholder="9" style="width:95%;"/><br />';
echo '<input type="text" name="vote_10" placeholder="10" style="width:95%;"/><br /><br />';

echo '<input type="submit" name="add" value="'. $lng['Mavzu ochish'] .'" /><br />';
echo '</form>';
echo CLOSE_DIV;
  
require_once('../../core/stop.php');
?>