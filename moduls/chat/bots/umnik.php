<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


        /* Умник */
        $umnik_last = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_MSG." WHERE `room_id` = ? && `user_id` = ? ORDER BY `id` DESC",array($room['id'], $config['bot']['umnik']));
    
        if ($umnik_last != NULL && $umnik_last['umnik_st'] !=4) {
            $umnik_vopros = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_VOPROS." WHERE `id` = ? LIMIT 1 ",array($umnik_last['vopros']));
            $umnik_post = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_MSG." WHERE `room_id` = ? && `msg` LIKE '".$umnik_vopros['otvet']."' ORDER BY `id` DESC LIMIT 1 ",array($room['id']));
        
        
            if ($umnik_post != NULL) {
                $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ",array($umnik_post['user_id']));
                
                if($umnik_last['umnik_st'] == 1){
                	$add_balls = 3;
                	$pods='не используя подсказок';
               	}
                
                if($umnik_last['umnik_st'] == 2){
                    $add_balls=2;
        	        $pods = 'используя одну подсказку';
                }
                
                if($umnik_last['umnik_st']==3){
        	       $add_balls=1;
        	       $pods='используя обе подсказки';
                }
                
                $msg = 'Молодец, [b]'.$ank['nick'].'[/b], Вы дали верный ответ [b]'.$umnik_vopros['otvet'].'[/b] первее всех, '.$pods.'. [b]'.$ank['nick'].'[/b] получает '.$add_balls.' баллов. Через 5 секунд напишу следующий вопрос!';
                DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`, `umnik_st`) VALUES (?, ?, ?, ?, ?, ?, ?)", array($room['id'], $config['bot']['umnik'], 0, 0, time(), $msg, 4));
                DB::$dbs->query("UPDATE ".USERS." SET `balls` = ? WHERE `user_id` = ? ",array( ($ank['balls'] + $add_balls), $ank['user_id'] ));
            }
        }
        
        $umnik_last1 = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_MSG." WHERE `room_id` = ? && `umnik_st` = ? ",array($room['id'], 1));
        if ($umnik_last1 != NULL && $umnik_last['umnik_st']!=4 && $umnik_last1['time']<time()-120) {
            $umnik_vopros = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_VOPROS." WHERE `id` = ? LIMIT 1 ",array($umnik_last1['vopros']));
            $msg = 'Эх Вы, не ответили на такой легкий вопрос :-(. Правильный ответ был: '.$umnik_vopros['otvet'].". Через 5 сек спрошу что-нибудь полегче может угадаите:-)";
            DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`, `umnik_st`) VALUES (?, ?, ?, ?, ?, ?, ?)", array($room['id'], $config['bot']['umnik'], 0, 0, time(), $msg, 4));
        }
        
        $umnik_last = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_MSG." WHERE `room_id` = ? && `umnik_st` <> '0' ORDER BY id DESC",array($room['id']));
        if ($umnik_last == NULL || $umnik_last['umnik_st']==4 && $umnik_last1['time']<time()-5) {
            $umnik_vopros = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_VOPROS." ORDER BY RAND() LIMIT 1");
            $msg = '[b]Так,внимательно читаем вопрос:[/b] ' .$umnik_vopros['vopros']. '. Это слово состоит из '.strlen($umnik_vopros['otvet']).' букв, угадываем! ;-)';
            DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`, `umnik_st`) VALUES (?, ?, ?, ?, ?, ?, ?)", array($room['id'], $config['bot']['umnik'], 0, 0, time(), $msg, 1));
        }
        
        if($umnik_last!=NULL && $umnik_last['umnik_st']==1 && $umnik_last['time']<time()-60){
            $umnik_vopros = DB::$dbs->queryFetch("SELECT * FROM ".CHAT_VOPROS." WHERE `id` = ? LIMIT 1 ",array($umnik_last['vopros']));
    
            if(function_exists('iconv_substr')) {
        	   $help = iconv_substr($umnik_vopros['otvet'], 0, 1, 'utf-8');
        	} else {
        		$help = substr($umnik_vopros['otvet'], 0, 2);
        	}
        
            for($i=0; $i < strlen($umnik_vopros['otvet'])-1 ;$i++){
        	   $help.='*';
        	}
            
            $msg = '[b]Угадываем я сказал![/b], '.$umnik_vopros['vopros'].' [b]ну хорошо, помогу Вам :-) Слово начинается на букву:[/b] '.$help;
            DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`, `umnik_st`) VALUES (?, ?, ?, ?, ?, ?, ?)", array($room['id'], $config['bot']['umnik'], 0, 0, time(), $msg, 2));
        }
        /* *** */
        
?>