<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
head(''. $lng['Hozir saytda'] .'');

/* VIP / Start */
    $vip = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `vip` > ? ", array(time()));
    if (!empty($vip)) {
        
        $ank = DB::$dbs->queryFetch("SELECT `photo`, `user_id` FROM ".USERS." WHERE `vip` > ? AND `last_time` > ? ORDER BY RAND() DESC LIMIT 1", array(time(), (time()-2000)));
        if (!empty($ank)) {
		echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="white" width="5%" style="width:10%;background: #4587BE;">';
echo '' . avatar($ank['user_id'],50,50) . '';
echo '</td>';

echo '<td class="tepa" width="95%" >';
echo user_choice($ank['user_id'], 'link') . '<br /><span class="mini"><a href="'.HOME.'/shop/vip/" style="color:white;">+ '. $lng['Ulanish'] .'</a></span>';
echo '</td></tr></table>';
        }
        
    }
/* VIP / End */

$all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `last_time` > ? ORDER BY `last_time` DESC", array((time() - 2000)));

if (empty($all)) {
    echo DIV_LI . ''. $lng['Saytda hech kim yo`q'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($all,10,''); 
    $sql = DB::$dbs->query("SELECT * FROM ".USERS." WHERE `last_time` > ? ORDER BY `last_time` DESC LIMIT {$n->start()}, 10", array((time() - 2000)));

    while($ank = $sql -> fetch()) {
	
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td class="lines" width="5%">';
echo '' . avatar($ank['user_id'],50,50) . '';
echo '</td>';
echo '<td class="lines" width="95%">';
echo ' '. user_choice($ank['user_id'], 'link') . ' ';
        echo '<span class="mini">'.vrem($ank['last_time']).'</span>';  
		
		if ($ank['user_id'] != $user['user_id']) {
        echo '<span style="float:right;"><a href="'.HOME.'/mail/'.$ank['user_id'].'/"> '.icon('umail.png',30,30).' </a></span>';  
		}
		echo '<br/> ';
		
        echo '' . city($ank['city']) .'';
    
	
	   
echo '</td></tr></table>';  
	//// Status	
	$status = DB::$dbs->queryFetch("SELECT * FROM ".STATUS." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1",array($ank['user_id']));
    if ($ank['user_id'] != $user['user_id']) {
        if (!empty($status)) {
		echo '<div class="sts" style="font-size:12px;">';
            echo '' . SubstrMaus(text($status['status']), 100) . '</div>';
        }
    } else {
		echo '' . (!empty($status['status']) ? '<div class="sts" style="font-size:12px;">'.SubstrMaus(text($status['status']), 100).' </div>' : '') . ' ';
    }
	//// Status	        
    }    
    echo $n->navi();     
}


require_once('../../core/stop.php');
?>