<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


        /* Шутник */
        $shutnik_last = mysql_fetch_assoc(mysql_query("SELECT * FROM ".CHAT_MSG." WHERE `room_id` = '".$room['id']."' AND `user_id` = '2' ORDER BY id DESC LIMIT 1"));
        if ($shutnik_last==NULL || $shutnik_last['time']<time()-15) {
            $msg = ''. $lng['Kulguli'] .'';
            DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`, `umnik_st`) VALUES (?, ?, ?, ?, ?, ?, ?)", array($room['id'], $config['bot']['umnik'], 0, 0, time(), $msg, 1));
        }
?>