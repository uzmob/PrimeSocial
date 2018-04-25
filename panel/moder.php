<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

if (privilegy('moder') == FALSE) {
    header("Location: ".HOME."/panel/");
    exit();
}

switch ($select) {
    
    default:
    head(''. $lng['Moderatsiya'] .''); 
    
    $files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES_FILE." WHERE `status` = '1' ");
    echo DIV_LI . '<a href="'.HOME.'/panel/moder/usfiles/">'. $lng['Shahsiy fayllar'] .'</a> ['.$files.']' . CLOSE_DIV;
    
    break;
    
    case 'usfiles':
    head(''. $lng['Shahsiy fayllarni moderatsiya qilish'] .'');
    
    if (isset($_GET['dopusk'])) {
        DB::$dbs->query("UPDATE ".FILES_FILE." SET `status` = '0' WHERE `id` = ? ", array(abs(num($_GET['dopusk']))));
        echo DIV_MSG . ''. $lng['Fayl kiritilishiga ruhsat berildi'] .'' . CLOSE_DIV;
    }
    
    if (isset($_GET['delete'])) {
        $file = DB::$dbs->queryFetch("SELECT * FROM ".FILES_FILE." WHERE `id` = ? ", array(abs(num($_GET['delete']))));
        @unlink('../../files/usfiles/' . $file['url']);
        DB::$dbs->query("DELETE FROM ".FILES_FILE." WHERE `id` = ? ", array(abs(num($_GET['delete']))));
        echo DIV_MSG . ''. $lng['Fayl o`chirildi'] .'' . CLOSE_DIV;
    }
     
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES_FILE." WHERE `status` = '1' ");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Moderatsiyada fayllar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,10,'select=usfiles'); 
        $sql = DB::$dbs->query("SELECT * FROM ".FILES_FILE." WHERE `status` = '1' ORDER BY `id` DESC LIMIT {$n->start()}, 10 ");
        while($file = $sql -> fetch()) {
            echo DIV_LI . '<a href="'.HOME.'/files/'.$file['user_id'].'/'.$file['folder_id'].'/'.$file['id'].'/">'.$file['name'].'</a> | 
			'. $lng['Kiritdi'] .': '.userLink($file['user_id']).' | '.$file['type'].' | 
			'.get_size($file['size']).' <b><a href="?dopusk='.$file['id'].'">['. $lng['Ruhsat berish'] .']</a></b> 
			<b><a href="?delete='.$file['id'].'">['. $lng['O`chirish'] .']</a></b>' . CLOSE_DIV;
        }
        echo $n->navi();          
    }     
    break;
    
}

  

echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>