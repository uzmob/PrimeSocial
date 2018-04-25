<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


function new_posts ($theme) {
    
    global $user;
    
    DB::$dbs->query("DELETE FROM ".FORUM_NEW_POST." WHERE `theme_id` = ? ", array($theme));

    $sql = DB::$dbs->query("SELECT `user_id` FROM ".USERS." WHERE `user_id` != ? ", array($user['user_id']));
    while($ank = $sql -> fetch()) {
        DB::$dbs->query("INSERT INTO ".FORUM_NEW_POST." (`theme_id`, `user_id`, `status`) VALUES (?,?,?)", array($theme, $ank['user_id'], 1));        
    }  
      
}
?>