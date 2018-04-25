<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

head(''. $lng['Tasma'] .'');



if (isset($_GET['del'])) {
    DB::$dbs->query("DELETE FROM ".LENTA." WHERE `id` = ? && `user_id` = ? ", array(num($_GET['del']), $user['user_id']));
    header("Location: ".HOME."/lenta/"); 
}

if (!empty($_POST['clean'])) {
    DB::$dbs->query("DELETE FROM ".LENTA." WHERE `user_id` = ? ", array($user['user_id'])); 
    header("Location: ".HOME."/lenta/");     
}  


$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LENTA." WHERE `user_id` = ? ", array($user['user_id']));

if (empty($all)) {
    echo DIV_BLOCK . ''. $lng['Tasma bo`sh'] .'' . CLOSE_DIV;
} else {
    echo '<form action="#" method="POST">';
    $n = new Navigator($all,$config['write']['guest'],''); 
    $sql = DB::$dbs->query("SELECT * FROM ".LENTA." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['guest']."", array($user['user_id']));
    
    while($post = $sql -> fetch()) {
        echo '<div class="lines"><span class="mini">' . vrem($post['time']) . '</span> 
		<a href="?del='.$post['id'].'" style="float:right;">'.icon('del.png').'</a><br />' . text($post['text']) . CLOSE_DIV;
        
        if ($post['status'] == 1) {
            DB::$dbs->query("UPDATE ".LENTA." SET `status` = '0' WHERE `id` = ? ", array($post['id'])); 
        } 
    }
    echo $n->navi();
    echo DIV_LI . '<input type="submit" name="clean" value="'. $lng['Tasmani tozalash'] .'"/></form>' . CLOSE_DIV;
}


    
require_once('../../core/stop.php');
?>