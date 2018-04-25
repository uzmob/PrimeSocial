<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

switch ($_GET['act']) {
    
    default:
    head(''. $lng['Menga amal bajarganlar'] .'');
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".DEY." WHERE `friend_id` = ?", array($user['user_id']));
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Ro`yhat bo`sh'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".DEY." WHERE `friend_id` = ? ORDER BY `id` DESC ", array($user['user_id']));
        while($post = $sql -> fetch()) {
            if ($post['status'] == 1) {
                echo DIV_LI . '<b>' . $post['msg'] . '</b>' . CLOSE_DIV;
                DB::$dbs->query("UPDATE ".DEY." SET `status` = ? WHERE `id` = ?", array(0, $post['id']));
            } else {
                echo DIV_LI . $post['msg'] . CLOSE_DIV;
            }
        }
    }
    break;
    
    case 'go':
    head(''. $lng['Qiziqish bildirish'] .'');
    

    $id = abs(intval($_GET['id']));
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));
        
    $array = array(
        array(''. $lng['Ko`z qisish'] .'', ''. $lng['Sizga ko`z qisdi'] .'', ''. $lng['skq'] .'', 50),
        array(''. $lng['O`pib olish'] .'', ''. $lng['Chat'] .'', ''. $lng['so'] .'', 50),
        array(''. $lng['Jilmayish'] .'', ''. $lng['Chat'] .'', ''. $lng['sj'] .'', 50),
        array(''. $lng['Quchoqlash'] .'', ''. $lng['Chat'] .'', ''. $lng['sq'] .'', 50),
        array(''. $lng['Salomlashish'] .'', ''. $lng['Chat'] .'', ''. $lng['sbs'] .'', 50),
        array(''. $lng['Sevgi izhor qilish'] .'', ''. $lng['Sizga sevgi izhor qildi'] .'', ''. $lng['ssiq'] .'', 100),
        array(''. $lng['Tishlab olish'] .'', ''. $lng['Sizni tishlab oldi'] .'', ''. $lng['sto'] .'', 80),
        array(''. $lng['So`kish'] .'', ''. $lng['Sanga so`kdi'] .'', ''. $lng['ss'] .'', 150)
    );

    $err = array();
    if (empty($ank)) {
        $err[] = ''. $lng['Foydalanuvchi ma`lumotlar bazasida topilmadi'] .'';
    }
    
    if ($ank['user_id'] == $user['user_id']) {
        $err[] = ''. $lng['Siz o`zingizga amal jo`nata olmaysiz'] .'';
    }
    
    if (!empty($err)) {
        echo DIV_ERROR;
        foreach ($err AS $value) {
            echo $value . '<br />';
        }
        echo CLOSE_DIV;
    } else {
        if (isset($_GET['dey'])) {
            $dey = abs(num($_GET['dey']));
            
            $err = array();
            if (empty($array[$dey])) {
                $err[] = ''. $lng['Harakatlar topilmadi'] .'';
            }
            
            if ($array[$dey][3] > $user['balls']) {
                $err[] = ''. $lng['Sizda yetarlicha ballar yo`q'] .'';
            }
            
            if (!empty($err)) {
                echo DIV_ERROR;
                foreach ($err AS $value) {
                    echo $value . '<br />';
                }
                echo CLOSE_DIV;
            } else {
                /* Matnni shakllantiramiz */
                if ($user['gender'] == 1) {
                    $msg = '<a href="'.HOME.'/id'.$user['user_id'].'">' . $user['nick'] . '</a> ' . $array[$dey][1];
                } else {
                    $msg = '<a href="'.HOME.'/id'.$user['user_id'].'">' . $user['nick'] . '</a> ' . $array[$dey][2];
                }
                lenta($msg, $ank['user_id']);
                
                DB::$dbs->query("UPDATE ".USERS." SET `balls` = ? WHERE `user_id` = ?", array(($user['balls'] - $array[$dey][3]), $user['user_id']));
                
                DB::$dbs->query("INSERT INTO ".DEY." (`user_id`, `friend_id`, `time`, `msg`, `status`) VALUES (?, ?, ?, ?, ?)", array($user['user_id'], $ank['user_id'], time(), $msg, 1));
                
                echo DIV_MSG . ''. $lng['Siz foydalanuvchi ustidan muvaffaqiyatli harakat jo`natdingiz'] .' ' . user_choice($ank['user_id'], 'link') . '' . CLOSE_DIV;
            }
        }
        foreach ($array AS $key => $value) {
            echo DIV_LI . ' <a href="?dey='.$key.'">'.$value[0].'</a> ['.$value[3].' '. $lng['ball'] .']' . CLOSE_DIV;
        }
    }
    break;

}


    
require_once('../../core/stop.php');
?>