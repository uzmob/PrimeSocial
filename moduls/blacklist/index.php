<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

head(''. $lng['Qora ro`yhat'] .'');

switch ($_GET['act']) {
    
    default:
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLACKUSERS." WHERE `user_id` = ? ", array($user['user_id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Ro`yhat bo`sh'] .'' . CLOSE_DIV;
    } else {
        $n = new navigator($total, 10, '');
        $sql = DB::$dbs->query("SELECT * FROM ".BLACKUSERS." WHERE `user_id` = ? ORDER BY `id` DESC {$n->limit}", array($user['user_id']));
        foreach ($sql AS $ank) {
            echo DIV_LI . user_choice($ank['black_id'], 'link') . ' <a href="'.HOME.'/blacklist/del/'.$ank['id'].'/">[x]</a>' . CLOSE_DIV;
        }
        echo $n->navi();           
    }
    echo DIV_LI . ''.icon('plus.png').' <a href="'.HOME.'/blacklist/add/"><b>'. $lng['Kiritish'] .'</b></a>' . CLOSE_DIV;
    break;
    
    case 'user':
    if (empty($_GET['user']) || $_GET['user'] == $user['user_id']) {
        header("Location: ".HOME."/blacklist/");
    }
    
    $id = abs(num($_GET['user']));
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLACKUSERS." WHERE `user_id` = ? ", array($id));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Ro`yhat bo`sh'] .'' . CLOSE_DIV;
    } else {
        $n = new navigator($total, 10, '');
        $sql = DB::$dbs->query("SELECT * FROM ".BLACKUSERS." WHERE `user_id` = ? ORDER BY `id` DESC {$n->limit}", array($id));
        foreach ($sql AS $ank) {
            echo DIV_LI . user_choice($ank['black_id'], 'link') .  CLOSE_DIV;
        }
        echo $n->navi();           
    }
    break;

    case 'del':
    $id = abs(num($_GET['id']));
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLACKUSERS." WHERE `user_id` = ? && `id` = ?", array($user['user_id'], $id)) == TRUE) {
        DB::$dbs->query("DELETE FROM ".BLACKUSERS." WHERE `id` = ? ", array($id));  
            
        header("Location: ".HOME."/blacklist/");
    } else {
        echo ''. $lng['Xatolik'] .'!<br />';
    }
    break;
        
    case 'add':
    if (!empty($_POST['send'])) {
        $us = html($_POST['user']);

        if (is_numeric($us)) {
            /* ID */
            $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($us));
        } else {
            /* Nik */
            $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `nick` = ?",array($us));
        }
        
        if (empty($ank)) {
            $err .= ''. $lng['Foydalanuvchi topilmadi'] .'!<br />';
        }
        
        if ($ank['user_id'] == $user['user_id']) {
            $err .= ''. $lng['Siz o`zingizni qora ro`yhatingizga kirita olmaysiz'] .'!<br />';
        }
        
        if (empty($err)) {
            DB::$dbs->query("INSERT INTO ".BLACKUSERS." (`user_id`, `black_id`) VALUES (?, ?)", array($user['user_id'], $ank['user_id']));
            
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizni qora ro`yhatiga kiritdi'] .'.';
            lenta($lenta, $ank['user_id']);   
             
            header("Location: ".HOME."/blacklist/");
        } else {
            echo $err . '<br />';
        }
    }
    
    echo DIV_BLOCK;
    echo '<form action="#" method="POST">';
        echo ''. $lng['Foydalanuvchining Niki yoki ID raqami'] .':<br />
		<input type="text" name="user" maxlength="20" /><br />';
        echo '<input type="submit" name="send" value="'. $lng['Qora ro`yhatga'] .'" /><br />';
    echo '</form>'; 
    
    echo CLOSE_DIV;    
    break;
    
    case 'go':
    if (empty($_GET['user']) || $_GET['user'] == $user['user_id']) {
        header("Location: ".HOME."/blacklist/");
    }    
    $id = abs(num($_GET['user']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLACKUSERS." WHERE `user_id` = ? && `black_id` = ?", array($user['user_id'], $ank['user_id'])) == TRUE) {
        DB::$dbs->query("DELETE FROM ".BLACKUSERS." WHERE `user_id` = ? && `black_id` = ?", array($user['user_id'], $ank['user_id']));  
    } else {
        DB::$dbs->query("INSERT INTO ".BLACKUSERS." (`user_id`, `black_id`) VALUES (?, ?)", array($user['user_id'], $ank['user_id']));
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizni qora ro`yhatiga kiritdi'] .'.';
        lenta($lenta, $ank['user_id']);   
    }
    header("Location: ".HOME."/id" . $ank['user_id']);
    break;
}  

require_once('../../core/stop.php');
?>