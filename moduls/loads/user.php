<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');
check_auth();

$id = abs(num($_GET['id']));
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));

if (empty($ank)) {
    header("Location: ".HOME);
}

head(''. $lng['Foydalanuvchi fayllari'] .' ' . $ank['nick']); 

$totalSize = DB::$dbs->querySingle("SELECT SUM(`size`) FROM ".LOADS_FILE." WHERE `user_id` = ?", array($ank['user_id'])); 
$totalFiles = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `user_id` = ?", array($ank['user_id'])); 

echo DIV_BLOCK
    . ''. $lng['Kiritilgan fayllar'] .': <b>' . $totalFiles . '</b> [' . get_size($totalSize) . ']'
. CLOSE_DIV;

if (empty($totalFiles)) {
    echo DIV_BLOCK . ''. $lng['Fayllar kiritilmagan'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($totalFiles,$config['write']['loads_file'],'id='.$ank['user_id']); 
    $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['loads_file']." ", array($ank['user_id']));
    while($file = $sql -> fetch()) {
		    echo DIV_LI . '<a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/">
			<img src="'.HOME.'/files/loads/files/'.$file['url'].'" style="width:100px;height:60px;"/></a><br/>';
		
            echo ''.icon('yuklama.png').' <a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a>
			<span class="count">'.get_size($file['size']).' / '.$file['type'].' ';
            echo ' '.icon('chart.png').' '.(empty($file['rating']) ? '0' : $file['rating']).'</span></div>';
    }
    echo $n->navi();    
}     
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>'; 


require_once('../../core/stop.php');
?>