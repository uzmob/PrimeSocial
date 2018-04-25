<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

head(''. $lng['Fayllarni izlash'] .''); 

echo '<div class="lines">';
echo '<form action="'.HOME.'/loads/search/" enctype="multipart/form-data" method="POST">';
echo '<input type="text" name="q" placeholder="'. $lng['Fayl nomi'] .'" style="width:96%;"/>';
echo '<input type="submit" name="search" value="'. $lng['Izlash'] .'" /></form>';
echo CLOSE_DIV;

if (!empty($_POST['search'])) {    
    $q = html($_POST['q']);
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `name` LIKE '%".$q."%' || `info` LIKE '%".$q."%'");
    
    echo DIV_LI . ''. $lng['Topilgan o`xshashliklar'] .': <b>' . $all . '</b>' . CLOSE_DIV;
    
    $n = new Navigator($all,5,''); 
    $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `name` LIKE '%".$q."%' || `info` LIKE '%".$q."%' LIMIT {$n->start()}, 5");
    while($file = $sql -> fetch()) {
        echo DIV_LI . '<a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a>' . CLOSE_DIV;   
    }
    
    echo $n->navi();
} 
     
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>'; 
require_once('../../core/stop.php');
?>