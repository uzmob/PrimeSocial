<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$id = abs(num($_GET['user']));
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));

if (empty($ank)) {
    head(''. $lng['Statuslar tarihi topilmadi'] .'');
    echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
    exit();
}
head('' . $ank['nick'] . ' '. $lng['statuslari'] .''); 

function check($id) {
    
    global $user;
    $post = DB::$dbs->queryFetch("SELECT * FROM ".STATUS." WHERE `id` = ? ",array($id));
    
    if (privilegy('statush')) {
        return TRUE;
    }
    
    if ($post['user_id'] == $user['user_id']) {
        return TRUE;
    }
    
    return FALSE;
}

if (isset($_GET['del']) && check($_GET['del'])) {
    DB::$dbs->query("DELETE FROM ".STATUS." WHERE `id` = ?", array(num($_GET['del'])));
    header("Location: ".HOME."/statush/".$ank['user_id']."/"); 
}

if (!empty($_POST['post_delete']) && (privilegy('statush') || $ank['user_id'] == $user['user_id'])) {
    foreach ($_POST as $name => $value) {
        DB::$dbs->query("DELETE FROM ".STATUS." WHERE `id` = ?", array($name));
    } 
    header("Location: ".HOME."/statush/" . $ank['user_id'] . "/");        
}

if ( (privilegy('statush') || $ank['user_id'] == $user['user_id']) && !empty($_POST['clean'])) {
    DB::$dbs->query("DELETE FROM ".STATUS." WHERE `user_id` = ? ", array($ank['user_id'])); 
    header("Location: ".HOME."/statush/" . $ank['user_id'] . "/");     
}  


$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".STATUS." WHERE `user_id` = ? ", array($ank['user_id']));

if (empty($all)) {
    echo DIV_BLOCK . ''. $lng['Statuslar hali yozilmagan'] .'' . CLOSE_DIV;
} else {
    echo '<form action="#" method="POST">';
    $n = new Navigator($all,$config['write']['guest'],''); 
    $sql = DB::$dbs->query("SELECT * FROM ".STATUS." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['guest']."", array($ank['user_id']));
    
    while($post = $sql -> fetch()) {
        $rating = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".STATUS_RATING." WHERE `status_id` = ?", array($post['id']));
        $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".STATUS_COMM." WHERE `status_id` = ?", array($post['id']));
        echo DIV_BLOCK . (privilegy('statush') ? '<input type="checkbox" name="'.$post['id'].'" /> ' : NULL) . '<b>' . vrem($post['time']) . '</b>:' . (check($post['id']) ? ' <a href="?del='.$post['id'].'" style="font-size:12px;">[x]</a>' : null) . '<br />
		' . text($post['status']) . '<br /><b><a href="'.HOME.'/status/'.$post['id'].'/comm/" style="font-size:12px;">'.icon('bubl.png').'  '.$comm.' </a> 
		<a href="'.HOME.'/status/'.$post['id'].'/like/" style=font-size:12px;">'.icon('cls.png',13,15).' '.$rating.'</a></b>' . CLOSE_DIV; 
    }
    echo $n->navi();
    echo (privilegy('statush') || $guest['user_id'] == $user['user_id'] ? DIV_LI . '<input type="submit" name="post_delete" value="'. $lng['Belgilanganlarni o`chirish'] .'"/> <input type="submit" name="clean" value="'. $lng['Barchasini o`chirish'] .'"/></form>' . CLOSE_DIV : NULL);
}
   
require_once('../../core/stop.php');
?>