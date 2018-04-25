<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

switch ($select) {
    
    case 'day':
    head(''. $lng['Kunning eng zo`r fayllari'] .'');
    
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `time` > '".(time() - 86400)."'");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Yangi fayllar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,5,'select=day'); 
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `time` > '".(time() - 86400)."' ORDER BY `rating` DESC LIMIT {$n->start()}, 5");
        while($file = $sql -> fetch()) {
		
echo '<div class="lines">';
            echo ''.icon('yuklama.png').' <a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a>
			<span class="count">'.get_size($file['size']).' / '.$file['type'].' ';
            echo ' '.icon('chart.png').' '.(empty($file['rating']) ? '0' : $file['rating']).'</span></div>';
        }
        
        echo $n->navi();         
    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';
    break;

    case 'wk':
    head(''. $lng['Haftaning eng zo`r fayllari'] .'');
     
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `time` > '".(time() - (86400 * 7))."'");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Yangi fayllar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,5,'select=wk'); 
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `time` > '".(time() - (86400 * 7))."' ORDER BY `rating` DESC LIMIT {$n->start()}, 5");
        while($file = $sql -> fetch()) {
		
echo '<div class="lines">';
            echo ''.icon('yuklama.png').' <a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a>
			<span class="count">'.get_size($file['size']).' / '.$file['type'].' ';
            echo ' '.icon('chart.png').' '.(empty($file['rating']) ? '0' : $file['rating']).'</span></div>';
        }
        
        echo $n->navi();         
    }
    
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';
    break;
    
    case 'month':
    head(''. $lng['Oy mobaynidagi eng zo`r fayllar'] .'');
      
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `time` > '".(time() - (86400 * 30))."'");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Tiket'] .'Yangi fayllar yo`q' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,5,'select=month'); 
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `time` > '".(time() - (86400 * 30))."' ORDER BY `rating` DESC LIMIT {$n->start()}, 5");
        while($file = $sql -> fetch()) {
	
echo '<div class="lines">';
            echo ''.icon('yuklama.png').' <a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a>
			<span class="count">'.get_size($file['size']).' / '.$file['type'].' ';
            echo ' '.icon('chart.png').' '.(empty($file['rating']) ? '0' : $file['rating']).'</span></div>';
        }
        
        echo $n->navi();         
    }
    
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';
    break;
    
    case 'new': 
    head(''. $lng['So`ngi kiritilganlar'] .'');
     
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." ORDER BY `id` DESC"); 
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Yangi fayllar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['loads_file'],''); 
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['loads_file']."");
        while($file = $sql -> fetch()) {
	
echo '<div class="lines">';
            echo ''.icon('yuklama.png').' <a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a>
			<span class="count">'.get_size($file['size']).' / '.$file['type'].' ';
            echo ' '.icon('chart.png').' '.(empty($file['rating']) ? '0' : $file['rating']).'</span></div>';
            echo CLOSE_DIV;
        }
        echo $n->navi();    
    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';        
    break;
    
}


require_once('../../core/stop.php');
?>