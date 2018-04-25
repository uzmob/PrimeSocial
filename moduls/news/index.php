<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

    head(''. $lng['Sayt yangiliklari'] .'');
	
echo (privilegy('news') ? DIV_LI . '<a href="'.HOME.'/panel/news/"><b>'. $lng['Yangiliklarni boshqarish'] .'</b></a>' . CLOSE_DIV : NULL);
/* Yangiliklar */
$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS."");
if (empty($all)) {
    echo DIV_BLOCK . ''. $lng['Yangiliklar yo`q'] .'' . CLOSE_DIV;
} else {
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr><td class="white" width="5%">';
echo ''.icon('RSS-60.png',40,40).'';
echo '</td>';

echo '<td class="white" style="vertical-align:top;" width="95%" >';
    $n = new Navigator($all,$config['write']['news'],''); 
    $sql = DB::$dbs->query("SELECT * FROM ".NEWS." ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['news']."");
    
    while($new = $sql -> fetch()) {
        echo '<a href="'.HOME.'/news/comm/'.$new['id'].'/"><b>' . $new['title'] . '</b></a><br/>';
		 echo '<font style="font-size:13px;">'. text($new['afisha']) . '</font><br/>';
		
    }
echo '</td></tr></table>';
    $new = DB::$dbs->queryFetch("SELECT * FROM ".NEWS." ORDER BY `id` DESC LIMIT 1");
    $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_COMM." WHERE `new_id` = ?", array($new['id']));
echo '<div class="lines">'.icon('comm.png',16,16).' <span class="mini">'.$comm.'&nbsp;  &nbsp; &raquo;  &nbsp; '. $lng['Yangiliklar'] .'</span>  
<span class="count">' . vrem($new['time']) . '</span>';
echo '</a>';
echo '</div>';

echo $n->navi();
}

   

require_once('../../core/stop.php');
?>