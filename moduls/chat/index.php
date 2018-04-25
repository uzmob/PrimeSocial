<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');


switch ($select) {
    
    default:
    head(''. $lng['Chat'] .'');
      

	
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".CHAT_ROOM."");

    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Xonalar yo`q'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".CHAT_ROOM." ORDER BY `id` DESC");
        while($room = $sql -> fetch()) {
            echo DIV_BLOCK;
            echo ($room['icon'] ? '<img src="'.HOME.'/files/icons_chat/'.$room['icon'].'"/> ' : NULL) . ' <a href="'.HOME.'/chat/'.$room['id'].'/">'.$room['name'].'</a> ';
            
            $online = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `location` LIKE '% ".$room['name']."%' AND `last_time` > ?", array((time() - 300)));
            echo '<span class="count"> ' . $online . ' '. $lng['kishi'] .'</span>';
            echo CLOSE_DIV;
        }
    }   

    break;
    
    case 'room':
    $room = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_ROOM." WHERE `id` = ? ",array(num($_GET['room'])));
    
    if (empty($room)) {
        header("Location: ".HOME."");     
    }
    
    if ($room['type'] == 2) {
        require_once('bots/shutnik.php');
    }
    if ($room['type'] == 1) {
        require_once('bots/umnik/bot.php');
    }
    head('' . $room['name']);
    

    if (!empty($user['chat_restart'])) {
        echo '<meta http-equiv="refresh" content="'.$user['chat_restart'].'">';
    }
        
    if (privilegy('chat_moder') && !empty($_POST['post_delete'])) {
        foreach ($_POST as $name => $value) {
            DB::$dbs->query("DELETE FROM ".CHAT_MSG." WHERE `id` = ?", array($name));
        } 
        header("Location: ".HOME."/chat/".$room['id']."/");        
    }

    if (privilegy('chat_moder') && !empty($_POST['clean'])) {
        DB::$dbs->query("DELETE FROM ".CHAT_MSG." WHERE `room_id` = ?", array($room['id'])); 
        header("Location: ".HOME."/chat/".$room['id']."/");      
    }
    
    if (isset($_GET['restart'])) {
        
        if ($_POST['restart_ok']) {
            $restart = num($_POST['restart']);
            DB::$dbs->query("UPDATE ".USERS." SET `chat_restart` = ? WHERE `user_id` = ? ", array($restart, $user['user_id'])); 
            header("Location: ".HOME."/chat/".$room['id']."/");  
        }
        
        echo DIV_AUT . '<b>'. $lng['Oraliqdagi vaqt'] .':</b>';
        echo '<form action="#" method="POST">';
        echo '<select name="restart">';
        echo '<option value="0">'. $lng['O`rnatilmagan'] .'</option>';
        echo '<option value="5">5 '. $lng['soniyada'] .'</option>';
        echo '<option value="10">10 '. $lng['soniyada'] .'</option>';
        echo '<option value="20">20 '. $lng['soniyada'] .'</option>';
        echo '<option value="30">30 '. $lng['soniyada'] .'</option>';
        echo '<option value="45">45 '. $lng['soniyada'] .'</option>';
        echo '<option value="60">60 '. $lng['soniyada'] .'</option>';
        echo '</select>';
        echo '<input type="submit" name="restart_ok" value="'. $lng['Saqlash'] .'" /></form>';        
        echo CLOSE_DIV;    
    }   
     
    if ($_POST['add']) {
        $msg = html($_POST['msg']);
        $privat = abs(num($_POST['privat']));
                        
        if (empty($msg)) {
            $err = ''. $lng['Bo`sh habar'] .'<br />';
        }
        
        if (!empty($_GET['otv']) && $_GET['otv'] != $user['user_id']) {
            $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
            if (!empty($ank)) {
                $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
            }
        }            
        if (!empty($err)) {
            echo DIV_ERROR . $err . CLOSE_DIV;
        } else {
            
            if ($room['type'] == 1) {
                require_once('bots/umnik/bot.php');
                require_once('bots/umnik/answer.php');
            }
            $kont_id = (!empty($ank) ? $ank['user_id'] : 0);
            DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `user_id` = ?",array((++$user['chat_post']), $user['user_id']));
            DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`) VALUES (?, ?, ?, ?, ?, ?)", array($room['id'], $user['user_id'], $kont_id, $privat, time(), $msg));
            header("Location: ".HOME."/chat/".$room['id']."/"); 
        }
                             
    }
if ($user) {
    echo DIV_AUT;
    echo '<a href="'.HOME.'/chat/'.$room['id'].'/"><b>'. $lng['Yangilash'] .'</b></a> | '.(empty($user['chat_restart']) ? ' <a href="'.HOME.'/chat/'.$room['id'].'/?restart">'. $lng['Yangilashni sozlash'] .'</a>' : ''. $lng['Har'] .': <a href="'.HOME.'/chat/'.$room['id'].'/?restart"><b>'.$user['chat_restart'].'</b> '. $lng['soniyada'] .'</a> ').' <br />';
    if (!empty($_GET['otv'])) {
        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
        if (!empty($ank) && $ank['user_id'] != $user['id']) {
            echo '<form action="?otv='.$ank['user_id'].'&room='.$room['id'].'&select=room" method="POST">';
            echo '<b>' . $ank['nick'] . '</b> '. $lng['Habar'] .'<br />';
        }
    } else {
        echo '<form action="?room='.$room['id'].'&select=room" method="POST">';
    }
    echo CLOSE_DIV;
    echo DIV_AUT;
    echo '<textarea name="msg" style="width:95%;height:4pc;"></textarea>';
    
    if (!empty($ank) && $ank['user_id'] != $user['id']) {
        echo '<select name="privat">';
        echo '<option value="0">'. $lng['Hammaga'] .'</option>';
        echo '<option value="1">'. $lng['Shahsan'] .'</option>';
        echo '</select>';
               
    }
    echo '<br/><input type="submit" name="add" value="'. $lng['Yozish'] .'" />';
    bbsmile(); 
	echo '</form>'; 
    
    if ($room['type'] == 1) {
        echo '<small>* <b>'. $lng['javoblarni kichik harflar bilan hamda ortiqcha so`zlarsiz yozamiz'] .'</b></small>';   
    }
    echo CLOSE_DIV;
}
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".CHAT_MSG." WHERE `room_id` = ? ", array($room['id']));

    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
    } else {
        
        echo '<form action="#" method="POST">';
        $n = new Navigator($all,$config['write']['room'],'select=room&room='.$room['id']);
        $sql = DB::$dbs->query("SELECT * FROM ".CHAT_MSG." WHERE `room_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['room']."", array($room['id']));
        while($post = $sql -> fetch()) {
            echo DIV_BLOCK;
            
            if (DB::$dbs->querySingle("SELECT COUNT(*) FROM ".BLACKUSERS." WHERE `user_id` = ? && `black_id` = ?", array($user['user_id'], $post['user_id'])) == FALSE) {
                if ($post['privat'] == TRUE) {
                    if ($post['user_id'] == $user['user_id'] || $post['kont_id'] == $user['user_id']) {
                        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ?",array($post['user_id']));
                        echo (privilegy('chat_moder') ? '<input type="checkbox" name="'.$post['id'].'" /> ' : NULL);
                        echo '[' . vrem($post['time']) . '] ['. $lng['P'] .'!] ' . ($post['user_id'] != $user['user_id'] ? '<a href="?otv='.$post['user_id'].'&room='.$room['id'].'&select=room"><b>'.user_choice($post['user_id'], 'link').'</b></a>' : '<b>'.$ank['nick'].'</b>') . ' ' . userLink($post['user_id'], '['. $lng['ank'] .'.]') .  ': ' . '<font color="#FF0000">' . text($post['msg']) . '</font>';
                    }
                } else {
                        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ?",array($post['user_id']));
                        echo (privilegy('chat_moder') ? '<input type="checkbox" name="'.$post['id'].'" /> ' : NULL);
                        echo '[' . vrem($post['time']) . '] ' . ($post['user_id'] != $user['user_id'] ? '<a href="?otv='.$post['user_id'].'&room='.$room['id'].'&select=room"><b>'.user_choice($post['user_id'], 'link').'</b></a>' : '<b>'.$ank['nick'].'</b>') . ' ' . userLink($post['user_id'], '['. $lng['ank'] .'.]') .  ': ' . text($post['msg']);                
                }
            }
            
            

            echo CLOSE_DIV;
        }
        echo $n->navi();
        
        echo (privilegy('chat_moder') ? DIV_LI . '<input type="submit" name="post_delete" value="'. $lng['Belgilangan sharhlarni o`chirish'] .'"/> <input type="submit" name="clean" value="'. $lng['Xonani tozalash'] .'"/></form>' . CLOSE_DIV : NULL);
    }
    $online = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `location` LIKE '% ".$room['name']."%' AND `last_time` > ?", array((time()-300)));
    echo DIV_LI . '<a href="'.HOME.'/chat/who/'.$room['id'].'/"><b>'. $lng['Xonada'] .': '.$online.' '. $lng['kishi'] .'.</b></a> | <a href="'.HOME.'/chat/who/"><b>'. $lng['Kim qayerda'] .'</b></a>' . CLOSE_DIV;         
    break;
    
}

require_once('../../core/stop.php');
?>