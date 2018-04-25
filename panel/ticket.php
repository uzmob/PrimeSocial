<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

if (privilegy('ticket') == FALSE) {
    header("Location: ".HOME."/panel/");
    exit();
}

head(''. $lng['Tiket'] .''); 


$all = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".TOUCH_MSG." WHERE `status` = ? ", array(1));
        
if ($all == 0) {
    echo DIV_BLOCK . ''. $lng['Habarlar yo`q'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($all,5,'');
    $sql = DB::$dbs->query("SELECT * FROM ".TOUCH_MSG." WHERE `status` = ? ORDER BY `time` DESC LIMIT {$n->start()}, 5", array(1));
    while($ticket = $sql -> fetch()){
        echo DIV_BLOCK . vrem($ticket['time']) . ' / ' . vrem($ticket['time']) . '<br />
		<a href="'.HOME.'/touch/ticket/'.$ticket['touch_id'].'/"> >> </a>' . CLOSE_DIV;
    }
    echo $n->navi();
} 
    
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>