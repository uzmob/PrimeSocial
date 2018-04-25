<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();
    
head(''. $lng['Mavzu izlash'] .'');
        

$q = html($_POST['q']);

$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_THEME." WHERE `name` LIKE '%".$q."%'");

echo DIV_LI . ''. $lng['Topilgan o`xshashliklar'] .': <b>' . $all . '</b>' . CLOSE_DIV;

$n = new Navigator($all,5,''); 
$sql = DB::$dbs->query("SELECT * FROM".FORUM_THEME." WHERE `name` LIKE '%".$q."%' LIMIT {$n->start()}, 5");
while($theme = $sql -> fetch()) {
    
    $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
    
    echo DIV_LI . ''.icon('pages.png').'  <a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'.$theme['name'].'</a> ['.$posts.']' . CLOSE_DIV;   
}

echo $n->navi();     
require_once('../../core/stop.php');
?>