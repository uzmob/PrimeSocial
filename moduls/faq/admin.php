<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');


head(''. $lng['Ma`muriyat'] .'');

$all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `level` > ?", array(0));

if (empty($all)) {
    echo DIV_LI . ''. $lng['Ma`muriyat yo`q'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($all,10,''); 
    $sql = DB::$dbs->query("SELECT `user_id`, `level` FROM ".USERS." WHERE `level` > ? ORDER BY `level` DESC LIMIT {$n->start()}, 10", array(0));

    while($ank = $sql -> fetch()) {
	
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td class="lines" width="5%">';
echo '' . avatar($ank['user_id'],50,50) . '';
echo '</td>';
echo '<td class="lines" width="95%">';
echo ' '. user_choice($ank['user_id'], 'link') . ' ';

echo '<span class="mini">'.vrem($ank['last_time']).'</span><br/>';  
echo '<span style="font-size:11px;color:#4587BE;border: 1px solid #68AEE6;border-radius:4px;padding:3px;">
'.level($ank['level']).'
</span>';
echo '</td></tr></table>';         
    }    
    echo $n->navi();     
}


require_once('../../core/stop.php');
?>