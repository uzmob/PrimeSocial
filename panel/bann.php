<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Ban olganlar ro`yhati'] .''); 

if (privilegy('anceta_bann')) {
    switch ($select) {
            
        default:
        if ($_GET['del']) {
            $del = num($_GET['del']);
            $bann = DB::$dbs->queryFetch("SELECT `moder` FROM ".BANN." WHERE `id` = ?",array($del));
            $moder = DB::$dbs->queryFetch("SELECT `user_id`, `name`, `level` FROM ".USERS." WHERE `user_id` = ?",array($bann['moder']));
            
            if ($user['user_id'] == $bann['moder'] || $user['level'] > $moder['level']) {
                DB::$dbs->query("DELETE FROM ".BANN." WHERE `id` = ?",array($del));
                echo DIV_MSG . ''. $lng['Ban olindi'] .'' . CLOSE_DIV;
            } else {
                echo DIV_ERROR . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
            }
        }
        
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BANN." WHERE `time_bann` > ?", array(time()));
        
        if ($all == 0) {
            echo DIV_AUT . ''. $lng['Ro`yhat bo`sh'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,10,''); 
            $sql = DB::$dbs->query("SELECT * FROM ".BANN." WHERE `time_bann` > ? LIMIT {$n->start()}, 10", array(time()));
            while($bann = $sql -> fetch()) {
                echo DIV_AUT;
                $ank = DB::$dbs->queryFetch("SELECT `user_id`, `name` FROM ".USERS." WHERE `user_id` = ?",array($bann['user_id']));
                echo '<a href="'.HOME.'/id'.$ank['user_id'].'"><b>'.$ank['name'].'</b> [id: '.$ank['user_id'].']<br />'; 
                
                $moder = DB::$dbs->queryFetch("SELECT `user_id`, `name`, `level` FROM ".USERS." WHERE `user_id` = ?",array($bann['moder']));
                echo ''. $lng['Ban bergan moderator'] .': '.user_choice($moder['user_id'], 'link').'<br />';
                echo '<b>'. $lng['Sababi'] .':</b> '.$bann['prich'].'<br />';
                echo '<b>'. $lng['Ban tugash vaqti'] .':</b> '.vrem($bann['time_bann']).'<br />';
                echo '<b>'. $lng['Ban sanasi'] .':</b> '.vrem($bann['time']).'<br />';
                echo CLOSE_DIV;
                
                echo DIV_LI;
                echo ($user['user_id'] == $bann['moder'] || $user['level'] > $moder['level'] ? '<a href="'.HOME.'/panel/bann_list/?del='.$bann['id'].'">['. $lng['Chiqarish'] .']</a>' : NULL);
                echo CLOSE_DIV;
            }
            echo $n->navi();         
        }
        break;
    
    }
} else {
    echo DIV_BLOCK . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
}

echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>