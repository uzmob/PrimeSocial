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
    head(''. $lng['Kim qayerda'] .'');
    
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".CHAT_ROOM."");

    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Xonalar yo`q'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".CHAT_ROOM." ORDER BY `id` DESC");
        while($room = $sql -> fetch()) {
            echo DIV_BLOCK;
            $online = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `location` LIKE '% ".$room['name']."%' AND `last_time` > ?", array(time() - 300));
            echo '<a href="'.HOME.'/chat/'.$room['id'].'/">'.$room['name'].'</a> [<b>' . $online . '</b> '. $lng['kishi'] .']<br />';
            if (empty($online)) {
                echo ''. $lng['Xonada hech kim yo`q'] .'...<br />';
            } else {
                $sql2 = DB::$dbs->query("SELECT `user_id` FROM ".USERS." WHERE `location` LIKE '% ".$room['name']."%' AND `last_time` > ?", array((time() - 300)));
                while($ank = $sql2 -> fetch()) {  
                    echo userLink($ank['user_id']) . ', ';
                }
            }
            echo CLOSE_DIV;
        }
    }  
   break;
    
    case 'room':
    $room = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_ROOM." WHERE `id` = ? ",array(num($_GET['room'])));
    
    if (empty($room)) {
        header("Location: ".HOME."");     
    }

    head(''. $lng['xonada kim bor'] .': '.$room['name'].' ');
    
    
        
    echo DIV_BLOCK;
    $online = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `location` LIKE '% ".$room['name']."%' AND `last_time` > ?", array((time() - 300)));
    echo '<a href="'.HOME.'/chat/'.$room['id'].'/">'.$room['name'].'</a> [<b>' . $online . '</b> '. $lng['kishi'] .']<br />';
    if (empty($online)) {
        echo ''. $lng['Xonada hech kim yo`q'] .'...<br />';
    } else {
        $sql2 = DB::$dbs->query("SELECT `user_id` FROM ".USERS." WHERE `location` LIKE '% ".$room['name']."%'");
        while($ank = $sql2 -> fetch()) {  
            echo userLink($ank['user_id']) . ', ';
        }
    }
    echo CLOSE_DIV;    
    break;
    
}

require_once('../../core/stop.php');
?>