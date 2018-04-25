<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();


switch ($_GET['select']) { 
    
    default:
    head();
    break;
    
    case 'new_themes':
    head(''. $lng['Yangi mavzular'] .'');

    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_THEME."");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Mavzular hali ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['forum_post'],'select=new_themes');
        $sql = DB::$dbs->query("SELECT * FROM ".FORUMS_THEME." ORDER BY `time` DESC LIMIT {$n->start()}, ".$config['write']['forum_post']."");
        while($theme = $sql -> fetch()) {
            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
            echo DIV_LI . ''.icon('pages.png').' <a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'.$theme['name'].'</a> <span class="count">'.$posts.'</span>' . CLOSE_DIV;
        }
        echo $n->navi();        
    }
          
    break;

    case 'activ_themes':
    head(''. $lng['Faol mavzular'] .''); 

    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_THEME."");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Mavzular hali ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['forum_post'],'select=new_themes');
        $sql = DB::$dbs->query("SELECT * FROM ".FORUMS_THEME." ORDER BY `activ` DESC LIMIT {$n->start()}, ".$config['write']['forum_post']."");
        while($theme = $sql -> fetch()) {
            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
            echo DIV_LI . ''.icon('pages.png').' <a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'.$theme['name'].'</a> <span class="count">'.$posts.'</span>' . CLOSE_DIV;
        }
        echo $n->navi();        
    }
          
    break;
        
    case 'new_posts':
    head(''. $lng['Yangi sharhlar'] .''); 

    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_POST."");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['forum_post'],'select=new_posts');
        $sql = DB::$dbs->query("SELECT * FROM ".FORUM_POST." ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['forum_post']."");
        while($post = $sql -> fetch()) {
            echo DIV_BLOCK;
            echo '<b>' . userLink($post['user_id']) . '</b> [' . vrem($post['time']) . '] 
			' . ($user['user_id'] != $post['user_id'] && $theme['status'] == 0 ? '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/new_post/?post='.$post['id'].'">'.icon('sharh.png').'</a> <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/new_post/?ctpost='.$post['id'].'">'.icon('oko.png').'</a>' : NULL) . ( (privilegy('forum_moder') || $post['user_id'] == $user['user_id']) && $theme['status'] == 0 ? '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/delete/'.$post['id'].'/">'.icon('minus2.png').'</a> <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/edit/'.$post['id'].'/">'.icon('pen2.png').'</a>' : NULL) . '<br />';
            
            if (!empty($post['ct'])) {
                $ct = DB::$dbs->queryFetch("SELECT `msg` FROM ".FORUMS_POST." WHERE `id` = ? ", array($post['ct']));
                echo DIV_CT . '<small><b>'. $lng['Sitata'] .':</b></small><br />' . text($ct['msg']) . CLOSE_DIV; 
            }
            
            echo text($post['msg']);
            
            if (!empty($post['file'])) {
                
                $path = '../../files/forum/'.$post['file'];
                
                $size = get_size(filesize($path));
                $path_info = pathinfo($path);
    
                echo '<br /><br />'.icon('yuklama.png').' <a href="'.HOME.'/files/forum/'.$post['file'].'">
				<b>['. $lng['Yuklab olish'] .']</b></a> ['.$path_info['extension'].'] ['.$size.']<br />'; 
            }
            
            echo '<br />'.icon('pages.png').' <a href="'.HOME.'/forum/'.$post['forum_id'].'/'.$post['forumc_id'].'/'.$post['theme_id'].'/">
			'. $lng['Mavzuga o`tish'] .'</a>';
            echo CLOSE_DIV;
       
        }
        echo $n->navi();        
    }
        
    break;
    
    case 'my_posts':
    head(''. $lng['Mening sharhlarim'] .''); 
    

    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_POST." WHERE `user_id` = ?", array($user['user_id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['forum_post'],'select=new_posts');
        $sql = DB::$dbs->query("SELECT * FROM ".FORUM_POST." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['forum_post']."", array($user['user_id']));
        while($post = $sql -> fetch()) {
            echo DIV_BLOCK;
            echo '<b>' . userLink($post['user_id']) . '</b> [' . vrem($post['time']) . '] 
			' . ($user['user_id'] != $post['user_id'] && $theme['status'] == 0 ? '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/new_post/?post='.$post['id'].'">'.icon('sharh.png').'</a> <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/new_post/?ctpost='.$post['id'].'">'.icon('oko.png').'</a>' : NULL) . ( (privilegy('forum_moder') || $post['user_id'] == $user['user_id']) && $theme['status'] == 0 ? '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/delete/'.$post['id'].'/">'.icon('minus2.png').'</a> <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/edit/'.$post['id'].'/">'.icon('pen2.png').'</a>' : NULL) . '<br />';
            
            if (!empty($post['ct'])) {
                $ct = DB::$dbs->queryFetch("SELECT `msg` FROM ".FORUMS_POST." WHERE `id` = ? ", array($post['ct']));
                echo DIV_CT . '<small><b>'. $lng['Sitata'] .':</b></small><br />' . text($ct['msg']) . CLOSE_DIV; 
            }
            
            echo text($post['msg']);
            
            if (!empty($post['file'])) {
                
                $path = '../../files/forum/'.$post['file'];
                
                $size = get_size(filesize($path));
                $path_info = pathinfo($path);
    
                echo '<br /><br />'.icon('yuklama.png').' <a href="'.HOME.'/files/forum/'.$post['file'].'">
				<b>['. $lng['Yuklab olish'] .']</b></a> ['.$path_info['extension'].'] ['.$size.']<br />'; 
            }
            
            echo '<br />'.icon('pages.png').' <a href="'.HOME.'/forum/'.$post['forum_id'].'/'.$post['forumc_id'].'/'.$post['theme_id'].'/">
			'. $lng['Mavzuga o`tish'] .'</a>';
            echo CLOSE_DIV;
       
        }
        echo $n->navi();        
    }
         
    break;
    
}
      

require_once('../../core/stop.php');
?>