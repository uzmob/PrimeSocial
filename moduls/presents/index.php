<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();
  
switch ($select) {

    default:
    $id = abs(intval($_GET['id']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));
    
    if ($user['user_id'] == $ank['user_id'] || empty($ank)) {
        header("Location: ".HOME."");
    } 
    
    head(' ' . $ank['nick'] . ' '. $lng['ga sovg`a'] .'');
     
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRESENTS."");  
    
    $n = new Navigator($all,9,'id='.$id); 
        
    $sql = DB::$dbs->query("SELECT * FROM ".PRESENTS." LIMIT {$n->start()}, 9");
	
echo '<div class="white"><center>';
    while($present = $sql -> fetch()){
echo '<a href="'.HOME.'/present/'.$id.'/send/'.$present['id'].'/">';
echo '<img src="'.HOME.'/files/presents/'.$present['url'].'" width=64 height=64>';
echo '</a> ';
echo ' <span style="margin-left: -10px;
	padding: 3px;
        color: #fff;font-size: 10px;
	background: rgba(0,0,0,0.5);
border-radius: 0 4px 0 4px;">'.$config['write']['pay_present'].' </span>';

    }   
echo '</center></div>'; 
    echo $n->navi(); 
    echo DIV_LI . '- <a href="'.HOME.'/id'.$ank['user_id'].'">'. $lng['Sahifaga qaytish'] .'</a>' . CLOSE_DIV;
    break;
    
    case 'send':
    $id = abs(intval($_GET['id']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));
        
    if ($user['user_id'] == $ank['user_id'] || empty($ank)) {
        header("Location: ".HOME."");
    } 
        
    head(' ' . $ank['nick'] . ' '. $lng['ga sovg`a'] .'');
     
    
    if ($user['balls'] < $config['write']['pay_present']) {
        echo DIV_BLOCK . ''. $lng['Sizda ballar yetarli emas'] .'' . CLOSE_DIV;
    } else {
        $pr = abs(intval($_GET['present']));
        $pr = DB::$dbs->queryFetch("SELECT * FROM ".PRESENTS." WHERE `id` = ? LIMIT 1",array($pr));
        
        if (empty($pr['id'])) {
            header("Location: ".HOME."/present/" . abs(num($_GET['id'])) . "/");
        }

        if (!empty($_POST['send'])) {
            $_POST['coment'] = html($_POST['coment']);
            $_POST['anonim'] = abs(intval($_POST['anonim']));
            
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a>  <a href="'.HOME.'/present/list/'.$id.'/"><b>'. $lng['sizga sovg`a jo`natdi'] .'</b></a> ';
            lenta($lenta, $ank['user_id']);
        
            DB::$dbs->query("INSERT INTO ".PRESENTS_LIST." SET `present_id` = ?, `user_id` = ?, `friend_id` = ?, `anonim` = ?, `comm` = ?, `time` = ? ", array($pr['id'], $user['user_id'], $id, $_POST['anonim'], $_POST['coment'], time()));
            
            DB::$dbs->query("UPDATE ".USERS." SET `balls` = ?  WHERE `user_id` = ?",array(($user['balls'] - $config['write']['pay_present']), $user['user_id']));
            
            echo DIV_BLOCK;
            echo ''. $lng['Sovg`a muvaffaqiyatli jo`natildi'] .'.<br />';
            echo '- <a href="'.HOME.'/id'.$ank['user_id'].'"> '. $lng['Sahifaga qaytish'] .'</a>';
            echo CLOSE_DIV;
            #header("Location: ".HOME."/id".$id); 
        }
        echo DIV_AUT;
        echo '<center><img src="'.HOME.'/files/presents/'.$pr['url'].'" style="width:96px;height:96px;"></center><br />';
        echo '<form action="#" method="post">
        '. $lng['Sharh'] .':<br /><textarea name="coment" style="width:95%;height:5pc;"></textarea><br />
        '. $lng['Anonim'] .': <input name="anonim" type="checkbox" value="1" /><br />
        <input type="submit" name="send" value="'. $lng['Jo`natish'] .'"></form>';
        echo CLOSE_DIV;        
    }
    echo DIV_LI . '- <a href="'.HOME.'/id'.$ank['user_id'].'">'. $lng['Sahifaga qaytish'] .'</a>' . CLOSE_DIV;
    break;
    
    case 'list':
    if (empty($_GET['id'])) {
        $id = abs(intval($_SESSION['id']));
    } else {
        $id = abs(intval($_GET['id']));
    }

    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));
        
    head(''. $lng['Sovg`alar'] .': ' . $ank['nick']); 
    
    $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".PRESENTS_LIST." WHERE `friend_id` = ?", array($id));
        
    if (!empty($all)) {
        $n = new Navigator($all,10,'select=list&id='.$id); 
        $sql = DB::$dbs->query("SELECT * FROM ".PRESENTS_LIST." WHERE `friend_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 10 ", array($id));
        while($list = $sql -> fetch()){
        $present = DB::$dbs->queryFetch("SELECT * FROM ".PRESENTS." WHERE `id` = ? LIMIT 1",array($list['present_id']));
echo '<div class="lines">';
            echo '<img src="'.HOME.'/files/presents/'.$present['url'].'" width=64 height=64><br />';
            echo ''.($list['anonim'] == 1 ? ''. $lng['Anonim foydalanuvchi'] .'' : userLink($list['user_id'])).' <br />';
            echo '' . vrem($list['time']);

echo ($list['comm'] ? '<br/><span class="mini">' . text($list['comm']) . '</span>' : null);
echo '</div>';

        }    
        echo $n->navi();  
    } else {
        echo DIV_BLOCK . ''. $lng['Sovg`alar yo`q'] .'' . CLOSE_DIV;
    }     
    echo DIV_LI . '- <a href="'.HOME.'/id'.$ank['user_id'].'">'. $lng['Sahifaga qaytish'] .'</a>' . CLOSE_DIV;
    break;

}
require_once('../../core/stop.php');
?>