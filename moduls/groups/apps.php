<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');
check_auth();

switch ($select) {
    
    default:
    head(''. $lng['Guruhga kirish'] .'');
     
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        echo DIV_BLOCK . ''. $lng['Guruh topilmadi'] .'' . CLOSE_DIV;
    } else {
        
        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_APPS." WHERE `group_id` = ? && `friend_id` = ? ", array($group['id'], $user['user_id'])) == TRUE) {
            if (!isset($_GET['yes']) && !isset($_GET['no'])) {
                echo DIV_BLOCK . ''. $lng['Parametrlar kiritlmadi'] .'' . CLOSE_DIV;
            } else {
                if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `user_id` = ? ", array($group['id'], $user['user_id'])) == FALSE) {
                    if (isset($_GET['yes'])) {
                        DB::$dbs->query("INSERT INTO ".GROUPS_PEOPLES." (`group_id`, `user_id`, `level`) VALUES (?, ?, ?)", array($group['id'], $user['user_id'], 0));
                        DB::$dbs->query("DELETE FROM ".GROUPS_APPS." WHERE `group_id` = ? && `friend_id` = ?", array($group['id'], $user['user_id']));
                        echo DIV_BLOCK . ''. $lng['Siz guruhga muvaffaqiyatli kirdingiz'] .': <a href="'.HOME.'/groups/'.$group['id'].'/"><b>' . $group['name'] . '</b></a>' . CLOSE_DIV;
                    } elseif (isset($_GET['no'])) {
                        DB::$dbs->query("DELETE FROM ".GROUPS_APPS." WHERE `group_id` = ? && `friend_id` = ?", array($group['id'], $user['user_id']));
                        echo DIV_BLOCK . ''. $lng['Taklif rad qilindi'] .'' . CLOSE_DIV;
                    } else {
                        header("Location: ".HOME);
                    }
                    
                }
            }
        }
        
    }
        
    break;
    
    case 'list':

    break;

}

require_once('../../core/stop.php');
?>