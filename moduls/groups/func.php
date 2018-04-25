<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


function check() {
    
    global $user;
    
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS." WHERE `user_id` = ? ", array($user['user_id']));
    
    if ($sql == TRUE) {
        return ''. $lng['Sizda guruh bor'] .'';
    }

    return FALSE;
    
}

function my() {
    
    global $user;

    $group = DB::$dbs->queryFetch("SELECT `id` FROM ".GROUPS." WHERE `user_id` = ? ",array($user['user_id']));
    
    if (!empty($group)) {
        return $group['id'];
    }

    return FALSE;
    
}

function new_posts ($theme) {
    
    global $user;
    
    DB::$dbs->query("DELETE FROM ".GROUPS_NEW_POST." WHERE `theme_id` = ? ", array($theme));

    $sql = DB::$dbs->query("SELECT `user_id` FROM ".USERS." WHERE `user_id` != ? ", array($user['user_id']));
    while($ank = $sql -> fetch()) {
        DB::$dbs->query("INSERT INTO ".GROUPS_NEW_POST." (`theme_id`, `user_id`, `status`) VALUES (?,?,?)", array($theme, $ank['user_id'], 1));        
    }  
      
}

function group_level ($level = NULL) {
    
    $array = array(''. $lng['Ishtrokchi'] .'', ''. $lng['Moderator'] .'', ''. $lng['Guruh yaratuvchisi'] .'');
    return $array[$level];
    
}

function check_private ($group) {
    
    global $user;
    
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `user_id` = ? ", array($group, $user['user_id']));

    return $sql;
    
}
?>