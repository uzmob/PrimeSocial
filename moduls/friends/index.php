<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FRIENDS." WHERE (`id_user` = ? OR `id_friend` = ?) AND (`status` = ?) ", array($user['user_id'],$user['user_id'], 1));
$all2 = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FRIENDS." WHERE (`id_user` = ?) AND (`status` = ?) ", array($user['user_id'], 0));
$all3 = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FRIENDS." WHERE (`id_friend` = ?) AND (`status` = ?) ", array($user['user_id'], 0));
        
switch ($select) {

    default:

    
    switch ($_GET['list']) {
        /* Mening dustlarim */
        default:
        head(''. $lng['Do`stlar'] .' '.$all.'');
		    if ($_GET['list'] != 'friend') {
        
        echo DIV_LI . '<a href="'.HOME.'/friends/my_appl/">'. $lng['Chiquvchi takliflar'] .'</a> '.$all2.' / ';
        echo '<a href="'.HOME.'/friends/user_appl/">'. $lng['Kiruvchi takliflar'] .'</a> '.$all3.'' . CLOSE_DIV;
        
    }
        if ($all == 0) {
            echo DIV_BLOCK . ''. $lng['Do`stlar ro`yhati bo`sh'] .'.' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT `id`, `id_user`, `id_friend` FROM ".FRIENDS." WHERE (`id_user` = ? OR `id_friend` = ?) AND (`status` = ?) ", array($user['user_id'],$user['user_id'], 1));
            while($friend = $sql -> fetch()){
                if ($friend['id_friend'] == $user['user_id']) {
                    $a = $friend['id_user'];
                } else {
                    $a = $friend['id_friend'];
                }
                echo DIV_LI . ' ' . userLink($a) . ' <a href="'.HOME.'/friends/delete/'.$a.'/">[x]</a>' . CLOSE_DIV;
            }
        }
        break;
            
        /* Chiquvchi takliflarim */
        case 'my_appl':
        head(''. $lng['Chiquvchi takliflar'] .'');
		    if ($_GET['list'] != 'friend') {
        
        echo DIV_LI . ' <a href="'.HOME.'/friends/">'. $lng['Mening do`stlarim'] .'</a> '.$all.'';
        echo ' / <a href="'.HOME.'/friends/user_appl/">'. $lng['Qoidalar'] .'</a> '.$all3.'' . CLOSE_DIV;
        
    }
        if ($all2 == 0) {
            echo DIV_BLOCK . ''. $lng['Takliflar yo`q'] .'.' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT `id_friend` FROM ".FRIENDS." WHERE (`id_user` = ?) AND (`status` = ?) ", array($user['user_id'], 0));
            while($friend = $sql -> fetch()){
                echo '<div class="lines"> '. userLink($friend['id_friend']) . ' <a href="'.HOME.'/friends/delete/'.$friend['id_friend'].'">
				['. $lng['Rad etish'] .']</a></div>';
            }
        }        
        break;
            
        case 'user_appl':
        head(''. $lng['Kiruvchi takliflar'] .'');
		    if ($_GET['list'] != 'friend') {
        
        echo DIV_LI . '<a href="'.HOME.'/friends/">'. $lng['Mening do`stlarim'] .'</a> '.$all.' / 
		<a href="'.HOME.'/friends/my_appl/">'. $lng['Chiquvchi takliflar'] .'</a> '.$all2.'' . CLOSE_DIV;
        
    }
	
        if ($all3 == 0) {
            echo DIV_BLOCK . ''. $lng['Takliflar yo`q'] .'.' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT `id_user` FROM ".FRIENDS." WHERE (`id_friend` = ?) AND (`status` = ?) ", array($user['user_id'], 0));
            while($friend = $sql -> fetch()){
                echo DIV_LI . userLink($friend['id_user']) . ' <a href="'.HOME.'/friends/add/'.$friend['id_user'].'/">['. $lng['qabul qilish'] .']</a> 
				<a href="'.HOME.'/friends/otkl/'.$friend['id_user'].'/">['. $lng['rad etish'] .']</a>' . CLOSE_DIV;
            }
        }                    
        break;
    }
    break;
        
    case 'add':
    $id = abs(intval($_GET['friend']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));
    
    if (empty($ank)) {
        head(''. $lng['Foydalanuvchi baza ma`lumotlarida topilmadi'] .'');
        echo ''. $lng['Xatolik'] .'!<br />';
        exit();
    }
        
    if ($ank['user_id'] == $user['user_id']) {
        head(''. $lng['Siz o`zingizni do`slaringiz orasiga qo`shib bilmaysiz'] .'');
        echo ''. $lng['Xatolik'] .'!<br />';
        exit();
    }
   
    $sql = DB::$dbs->queryFetch("SELECT * FROM ".FRIENDS." WHERE (`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?) LIMIT 1 ",array($ank['user_id'], $user['user_id'], $user['user_id'], $ank['user_id']));
    if ($sql['id'] && $sql['status'] == 1) {
        $err .= '' . $ank['nick'] . ' '. $lng['do`stlaringiz orasida bor'] .'.<br />';
    } else {
        if (($sql['id'] && $sql['status'] == 0) && $sql['id_friend'] == $user['user_id'])  {
            DB::$dbs->query("UPDATE ".FRIENDS." SET `status` = ? WHERE (`id_user` = ? AND `id_friend` = ?)",array(1, $sql['id_user'], $user['user_id']));
            $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($sql['id_user']));
            $msg = '' . $ank['nick'] . ' '. $lng['taklifi muvaffaqiyatli qabul qilindi'] .' ';
            
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['do`stlik taklifingizni qabul qildi'] .'';
            lenta($lenta, $ank['user_id']);
        } else {
            DB::$dbs->query("INSERT INTO ".FRIENDS." SET `id_user` = ?, `id_friend` = ? ",array($user['user_id'], $ank['user_id']));     
            $msg = '' . $ank['nick'] . ' '. $lng['ga do`stlik taklifini muvaffaqiyatli jo`natdingiz'] .'!'; 
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizga '] .' <a href="'.HOME.'/friends/user_appl/">
			'. $lng['do`stlashishni taklif qilyapti'] .'</a>  ';
            lenta($lenta, $ank['user_id']);           
        }
        head(''. $lng['Do`stlashish'] .'');  
        echo DIV_BLOCK . $msg . CLOSE_DIV;
        }
    break;
  
    case 'otkl':
    $id = abs(intval($_GET['friend']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));
    
    if (empty($ank)) {
        head(''. $lng['Foydalanuvchi topilmadi'] .'');
        echo ''. $lng['Xatolik'] .'!<br />';
        exit();
    }
        
    if ($ank['user_id'] == $user['user_id']) {
        head(''. $lng['Siz o`zingizni do`slaringiz orasiga qo`shib bilmaysiz'] .'');
        echo ''. $lng['Xatolik'] .'!<br />';
        exit();
    }
   
   $sql = DB::$dbs->queryFetch("SELECT `id`, `status` FROM ".FRIENDS." WHERE (`id_user` = ? AND `id_friend` = ? AND `status` = ?) LIMIT 1 ",array($ank['user_id'], $user['user_id'], 0));
   
   if ($sql['id'] && $sql['status'] == 0) {
    DB::$dbs->query("DELETE FROM ".FRIENDS." WHERE `id` = ? ",array($sql['id']));   
    } else {
        echo ''. $lng['Xatolik'] .''; 
        exit();            
    } 
    
    if (isset($_GET['anceta'])) {
        header("Location: ".HOME."/id".$ank['user_id']);
    } else {
        header("Location: ".HOME."/friends/");
    }   
    break;
              
    case 'delete':
    $id = abs(intval($_GET['friend']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));
    
    if (empty($ank)) {
        head(''. $lng['Foydalanuvchi topilmadi'] .'');
        echo ''. $lng['Xatolik'] .'!<br />';
        exit();
    }
        
    if ($ank['user_id'] == $user['user_id']) {
        head(''. $lng['Siz o`zingizni do`slaringiz orasiga qo`shib bilmaysiz'] .'');
        echo ''. $lng['Xatolik'] .'!<br />';
        exit();
    }
        
    $sql = DB::$dbs->queryFetch("SELECT * FROM ".FRIENDS." WHERE (`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?)LIMIT 1",array($ank['user_id'], $user['user_id'], $ank['user_id'], $user['user_id']));    
    if($sql['id'] && $sql['status'] == 1) {
        DB::$dbs->query("DELETE FROM ".FRIENDS." WHERE `id` = ? ",array($sql['id']));   
    } else {
        $sql2 = DB::$dbs->queryFetch("SELECT * FROM ".FRIENDS." WHERE (`id_user` = ? AND `id_friend` = ?) LIMIT 1",array($user['user_id'], $ank['user_id']));    
        if($sql2['id'] && $sql2['status'] == 0) {
            DB::$dbs->query("DELETE FROM ".FRIENDS." WHERE `id` = ? LIMIT 1",array($sql2['id']));     
        }
    }
    if (isset($_GET['anceta'])) {
        header("Location: ".HOME."/id".$ank['user_id']);
    } else {
        header("Location: ".HOME."/friends/");
    }     
    break;
}
require_once('../../core/stop.php');
?>