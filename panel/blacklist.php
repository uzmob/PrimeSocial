<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Qora ro`yhat'] .''); 

if (privilegy('anceta_bann')) {
    switch ($select) {
            
        default:
        if ($_GET['del']) {
            $del = num($_GET['del']);
            DB::$dbs->query("DELETE FROM ".BLACKLIST." WHERE `id` = ?",array($del));
            echo DIV_MSG . ''. $lng['Muvaffaqiyatli o`chirildi'] .'' . CLOSE_DIV;
        }
        
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLACKLIST."");
        
        if ($all == 0) {
            echo DIV_AUT . ''. $lng['Ro`yhat bo`sh'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,10,''); 
            $sql = DB::$dbs->query("SELECT * FROM ".BLACKLIST." LIMIT {$n->start()}, 10");
            while($black = $sql -> fetch()) {
                echo DIV_AUT;
                echo '<b>UA:</b> ' . $black['ua'] . '<br />'; 
                echo '<b>IP:</b> ' . $black['ip'] . '<br />'; 
                echo CLOSE_DIV;
                
                echo DIV_LI;
                echo '<a href="'.HOME.'/panel/black_list/?del='.$black['id'].'">['. $lng['O`chirish'] .']</a>';
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