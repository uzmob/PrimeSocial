<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');


switch ($select) {
    
    default:
    head(''. $lng['Forum'] .'');
    

    if ($_POST['add'] && privilegy('forum')) {
        $name_f = html($_POST['name']);
            
        if (empty($name_f)) {
            echo DIV_ERROR . ''. $lng['Forum nomini kiriting'] .'' . CLOSE_DIV;
        } else {
            if (!empty($_FILES['file'])) {
                $name = $_FILES['file']['name']; # Fayl nomi
                $ext = strtolower(strrchr($name, '.')); # Fayl shakli
                $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
                $icon_uri = time().$ext;
                $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                    
                if ($par[0] > 256 || $par[1] > 256) {
                    $err .= ''. $lng['Rasm hajmi belgilangan miqdordan oshmoqda'] .'. [Max. 16x16]<br />';
                }
                    
                if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                    $err .= ''. $lng['Fayl formatida xatolik'] .'.<br />';
                }
                    
                if (empty($err)) {
                    copy($_FILES['file']['tmp_name'], '../../files/icons_forum/'.$icon_uri);
                } else {
                    echo $err;
                }
            }
            $icon_uri = ($icon_uri ? $icon_uri : '');
            DB::$dbs->query("INSERT INTO ".FORUMS." (`name`,`icon`) VALUES (?,?)", array($name_f,$icon_uri));
            header("Location: ".HOME."/forums/"); 
        }
    }
    

    echo '<div class="white">';
    echo '- <a href="'.HOME.'/forum/new_posts/">'. $lng['Yangi sharhlar'] .'</a> / ';
    echo ' <a href="'.HOME.'/forum/new_themes/">'. $lng['Yangi mavzular'] .'</a><br />';
    echo '- ';
    if ($user) {
    echo '<a href="'.HOME.'/forum/my_posts/">'. $lng['Mening sharhlarim'] .'</a> / ';
    }
    echo ' <a href="'.HOME.'/forum/activ_themes/">'. $lng['Faol mavzular'] .'</a><br />';

    echo '- <a href="?select=search_themes">'. $lng['Mavzu izlash'] .'</a>';
    echo '</div>';
	
	

	
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS."");
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Forumlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".FORUMS."");
        while($forum = $sql -> fetch()) {
            
            $themes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_THEME." WHERE `forum_id` = ? ", array($forum['id']));
            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `forum_id` = ? ", array($forum['id']));
            
			
			

echo '<div class="white">';
echo '<a href="'.HOME.'/forum/'.$forum['id'].'/"><div class="grey" style="margin-left: 5px;margin-right: 5px;border-radius:4px 4px 0 0;"> &nbsp; ';

    if ($forum['icon']) {
	 echo ' <img src="/files/icons_forum/'. ($forum['icon']).'"> ';
	} else {
	 echo ' '.icon('forumlar.png').'  ';
	}

echo ''.$forum['name'].' <span class="count">'.$themes.'/'.$posts.'</span></div></a>';
echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-left: 5px;margin-right: 5px;">';
echo '<div class="white" style="font-size:13px;">';
			
			        echo ''.(!empty($forum['ustav']) ? 
                        SubstrMaus(text($forum['ustav']), 300)
                        :
                        ''. $lng['Bo`limga ta`rif va qoidalar to`ldirilmagan'] .'');
          
			
			if (privilegy('forum')) {
            echo ' <a href="'.HOME.'/forum/ustav/'.$forum['id'].'/" class="mini">'. $lng['Edit'] .'</a>';
        }
            echo '</div>';
			
			/* So`ngi faol mavzu */
            $theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `forum_id` = ? ORDER BY `activ` DESC LIMIT 1", array($forum['id']));
            if (!empty($theme)) {
                $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
                $post = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_POST." WHERE `theme_id` = ? ORDER BY `time` DESC LIMIT 1", array($theme['id']));
                
                $page = ceil(($posts / $config['write']['forum_post']));
                echo ' &nbsp;  &nbsp; '.icon('pages.png').' <a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'.$theme['name'].'</a> ['.$posts.'] <a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/?p='.$page.'">[>>]</a>
                <br/> &nbsp;  &nbsp; '.user_choice($theme['user_id'], 'link').' / '.user_choice($post['user_id'], 'link').'';
                
            } else {
                echo '';
            }
			
echo '</div></div>';
		
        }
    }
        if (privilegy('forum')) {
        echo '<div class="white">';
        echo '<form action="#" method="POST" enctype="multipart/form-data">';
        echo ''. $lng['Yangi forum'] .':<br /><input type="text" name="name" /><br />';
        echo ''. $lng['Icon'] .' [16x16, jpg|jpeg|gif|png]:<br/><input type="file" name="file" /><br/>';
        echo '<input type="submit" name="add" value="'. $lng['Yangi forum'] .'" /></form>';
        echo CLOSE_DIV; 
    } 

    

    break;
    
    case 'search_themes':
    head(''. $lng['Mavzu izlash'] .'');
	
    echo '<div class="white"><br/>';
    echo '<form action="'.HOME.'/forum/search/" method="POST">';
    echo '<input type="text" name="q" style="width:50%;"/> <input type="submit" name="search" value="'. $lng['Mavzu izlash'] .'" /><br />';
    echo '</form><br/></div>';

    break;
    
    case 'ustav':
    $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array(abs(num($_GET['forum']))));
    
    if (empty($forum)) {
        head(''. $lng['Forum topilmadi'] .'');
        
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
              
    } else {
        
        head(''. $lng['Bo`lim qoidasi'] .': ' . $forum['name']);
        
        

        if (isset($_GET['ustav']) && privilegy('forum')) {
            if ($_POST['send']) {
                $ustav = html($_POST['ustav']);
                DB::$dbs->query("UPDATE ".FORUMS." SET `ustav` = ? WHERE `id` = ? ", array($ustav, $forum['id']));
                header("Location: ".HOME."/forum/ustav/".$forum['id']."/"); 
            }
            
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Bo`lim qoidasi'] .':<br /><textarea name="ustav" style="width:95%;">'.$forum['ustav'].'</textarea><br />';
            echo '<input type="submit" name="send" value="'. $lng['O`zgartirish'] .'" /></form>';
			bbsmile(); 
            echo CLOSE_DIV;             
        }
                
        
        echo ' '. DIV_BLOCK . (!empty($forum['ustav']) ? 
                        text($forum['ustav'])
                        :
                        ''. $lng['Qoidalar to`ldirilmagan'] .'')
        . CLOSE_DIV;
        if (privilegy('forum')) {
            echo DIV_LI . '<a href="?ustav">'. $lng['Qoidalarni tahrirlash'] .'</a>' . CLOSE_DIV;
        }
        echo DIV_LI . '&laquo; <a href="/forum">'. $lng['Forum'] .'</a>' . CLOSE_DIV;
        
    }
       
    break;
    
    case 'forum':
    $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array(abs(num($_GET['forum']))));
    
    if (empty($forum)) {
        head(''. $lng['Forum topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
              
    } else {
        
        head(''. $lng['Forum'] .': ' . $forum['name']);
        
        
        
			
        if (isset($_GET['ustav']) && privilegy('forum')) {
            if ($_POST['send']) {
                $ustav = html($_POST['ustav']);
                DB::$dbs->query("UPDATE ".FORUMS." SET `ustav` = ? WHERE `id` = ? ", array($ustav, $forum['id']));
                header("Location: ".HOME."/forum/".$forum['id']."/?ustav"); 
            }
            
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Forum qoidasi'] .':<br /><textarea name="ustav">'.$forum['ustav'].'</textarea><br />';
            echo '<input type="submit" name="send" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;             
        }
        
        if (isset($_GET['del']) && privilegy('forum')) {
            if (!isset($_GET['go'])) {
                echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
				<a href="?del&go">['. $lng['O`chirish'] .']</a> 
				<a href="'.HOME.'/forum/'.$forum['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
            } else {
                DB::$dbs->query("DELETE FROM ".FORUMS_CAT." WHERE `forum_id` = ? ", array($forum['id']));
                DB::$dbs->query("DELETE FROM ".FORUMS." WHERE `id` = ? ", array($forum['id']));
                header("Location: ".HOME."/forums/"); 
            }    
        }

        if (isset($_GET['edit']) && privilegy('forum')) {
            if ($_POST['edit']) {
                $name = html($_POST['name']);
                
                if (empty($name)) {
                    echo DIV_ERROR . ''. $lng['Forum nomini kiriting'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".FORUMS." SET `name` = ? WHERE `id` = ? ", array($name, $forum['id']));
                    header("Location: ".HOME."/forum/".$forum['id']."/"); 
                }
            }
			

			
            
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Forumni tahrirlash'] .':<br /><input type="text" value="'.$forum['name'].'" name="name" />';
            echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;             
        }
                
			      if ($_POST['add'] && privilegy('forum')) {
            $name_f = html($_POST['name']);
                
            if (empty($name_f)) {
                echo DIV_ERROR . ''. $lng['Podforum nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                if (!empty($_FILES['file'])) {
                $name = $_FILES['file']['name']; # Fayl nomi
                $ext = strtolower(strrchr($name, '.')); # Fayl shakli
                $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
                $icon_uri = time().$ext;
                $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                    
                if ($par[0] > 256 || $par[1] > 256) {
                    $err .= ''. $lng['Rasm hajmi belgilangan miqdordan oshmoqda'] .'. [Max. 16x16]<br />';
                }
                        
                    if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                        $err .= ''. $lng['Fayl formatida xatolik'] .'.<br />';
                    }
                        
                    if (empty($err)) {
                        copy($_FILES['file']['tmp_name'], '../../files/icons_forum/'.$icon_uri);
                    } else {
                        echo $err;
                    }
                }
                $icon_uri = ($icon_uri ? $icon_uri : '');
                DB::$dbs->query("INSERT INTO ".FORUMS_CAT." (`forum_id`, `name`, `icon`) VALUES (?, ?, ?)", array($forum['id'], $name_f, $icon_uri));
                header("Location: ".HOME."/forum/".$forum['id']."/"); 
            }
        }

			 
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_CAT." WHERE `forum_id` = ?", array($forum['id']));
            
        if ($all == 0) {
            echo DIV_AUT . ''. $lng['Podforumlar yo`q'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,$config['write']['forum_razd'],'forum='.$forum['id'].'&select=forum'); 
            $sql = DB::$dbs->query("SELECT * FROM ".FORUMS_CAT." WHERE `forum_id` = ? LIMIT {$n->start()}, ".$config['write']['forum_razd']." ", array($forum['id']));
            while($forumc = $sql -> fetch()) {
                
                $themes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_THEME." WHERE `forumc_id` = ? ", array($forumc['id']));
                $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `forumc_id` = ? ", array($forumc['id']));
    

	

    echo '<div class="touch"><a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/">';
	if ($forumc['icon']) {
	 echo ' <img src="/files/icons_forum/'. ($forumc['icon']).'"> ';
	} else {
	 echo ' '.icon('bulim.png').'  ';
	}
	
                echo ' '.$forumc['name'].'
				 <span class="count">'.$themes.'/'.$posts.'</span></a>' . CLOSE_DIV;
				 
echo '</div></div>';

            }
            echo $n->navi(); 
        }        
    }


    if (privilegy('forum')) {   
        echo DIV_AUT;
        echo '<form action="#" method="POST" enctype="multipart/form-data">';
        echo ''. $lng['Yangi podforum'] .':<br /><input type="text" name="name" /><br />';
        echo ''. $lng['Icon'] .' [16x16, jpg|jpeg|gif|png]:<br/><input type="file" name="file" /><br/>';
        echo '<input type="submit" name="add" value="'. $lng['Yangi podforum'] .'" /></form>';
        echo CLOSE_DIV; 
        
            echo '<div class="white">&laquo; <a href="/forum">'. $lng['Forum'] .'</a></div>';
			
        if (!empty($forum)) {
            echo DIV_BLOCK;
            echo '- <a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
            echo '- <a href="?del">'. $lng['O`chirish'] .'</a><br />';
            echo '- <a href="?ustav">'. $lng['Qoidalarni tahrirlash'] .'</a><br />';
            echo CLOSE_DIV;  
        }
    }
                   	        echo '<div class="sts"><center><b>' . $forum['name'].'</b></center> ';
				        echo ''.(!empty($forum['ustav']) ? 
                        SubstrMaus(text($forum['ustav']), 500)
                        :
                        ''. $lng['Bo`limga ta`rif va qoidalar to`ldirilmagan'] .'');
            echo '<a href="'.HOME.'/forum/ustav/'.$forum['id'].'/" style="float:right;"><u>'. $lng['Qoidalar'] .'</u></a><br/><br/></div>';
			
    break;
    
    case 'themes':
    $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array(abs(num($_GET['forum']))));
    
    if (empty($forum)) {
        head(''. $lng['Forum topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
        
    $forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['forumc']))));
    
    if (empty($forumc)) {
        head(''. $lng['Podforum topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
    
    head(''. $lng['Podforum mavzulari'] .': ' . $forumc['name']);
        
    
	
    if (isset($_GET['del'])) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a> 
			<a href="'.HOME.'/forum/'.$forum['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            DB::$dbs->query("DELETE FROM ".FORUMS_CAT." WHERE `id` = ? ", array($forumc['id']));
            header("Location: ".HOME."/forum/".$forum['id']."/"); 
        }    
    }

    if (isset($_GET['edit'])) {
        if ($_POST['edit']) {
            $name = html($_POST['name']);
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Podforum nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".FORUMS_CAT." SET `name` = ? WHERE `id` = ? ", array($name, $forumc['id']));
                header("Location: ".HOME."/forum/".$forum['id']."/".$forumc['id']."/"); 
            }
        }
            
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Podforum nomini tahrirlash'] .':<br /><input type="text" value="'.$forumc['name'].'" name="name" />';
        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
        echo CLOSE_DIV;             
    }
    
    if ($user) {            
    echo DIV_AUT . '<form action="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/new_theme/" method="POST">
	<input type="submit" name="new_theme" value="'. $lng['Yangi mavzu'] .'" /></form>' . CLOSE_DIV;
    }
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_THEME." WHERE `forumc_id` = ?", array($forumc['id']));
            
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Mavzular ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['forum_theme'],'select=themes&forum='.$forum['id'].'&forumc='.$forumc['id']); 
        $sql = DB::$dbs->query("SELECT * FROM ".FORUM_THEME." WHERE `forumc_id` = ? ORDER BY `activ` AND `fix` DESC LIMIT {$n->start()}, ".$config['write']['forum_theme']." ", array($forumc['id']));
        while($theme = $sql -> fetch()) {

            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
            $page = ceil(($posts / $config['write']['forum_post']));                
            echo DIV_LI . (!empty($theme['fix']) ? '<b>#</b> ' : NULL) . ''.icon('pages.png').' <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/">'.$theme['name'].'</a> 
			<a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/?p='.$page.'"> >> </a>
			 <span class="count">'.$posts.'</span>' . CLOSE_DIV;
			
        }
        echo $n->navi();
    }   
     
    echo '<div class="white">&laquo; <a href="'.HOME.'/forum/'.$forum['id'].'/">'. $forum['name'] .'</a></div>';        
	
    if (privilegy('forum')) {    
        echo DIV_BLOCK;
        echo '<a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
        echo '<a href="?del">'. $lng['O`chirish'] .'</a><br />';
        echo CLOSE_DIV;  
    }

    break;
    
    case 'theme':
    $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array(abs(num($_GET['forum']))));
    
    if (empty($forum)) {
        head(''. $lng['Forum topimadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
        
    $forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['forumc']))));
    
    if (empty($forumc)) {
        head(''. $lng['Podforum topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
    
    $theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array(abs(num($_GET['theme']))));
    
    if (empty($theme)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
    
    DB::$dbs->query("DELETE FROM ".FORUMS_NEW_POST." WHERE `theme_id` = ? && `user_id` = ? ", array($theme['id'], $user['user_id']));
    
    if ($theme['status'] == 1) {
        head(''. $lng['Mavzu'] .': ' . $theme['name'] . ' '. $lng['muhokama qilish uchun yopilgan'] .'!');
          
    } else {
        head(' ' . $theme['name']);
        
    }
    
    if (isset($_GET['del']) && privilegy('forum_moder')) {
         if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a> 
			<a href="'.HOME.'/forum/'.$forum['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".FORUM_POST." WHERE `theme_id` = ? ", array($theme['id']));
            while($post = $sql -> fetch()) {
                unlink('../../files/forum/'.$post['file']);
            }
            DB::$dbs->query("DELETE FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
            DB::$dbs->query("DELETE FROM ".FORUMS_THEME." WHERE `id` = ? ", array($theme['id']));
            header("Location: ".HOME."/forum/".$forum['id']."/".$forumc['id']."/"); 
        }          
    }
       

	
	    if ($theme['user_id'] == $user['user_id']) {    
        echo '<div class="white"> <u>' . $theme['name'] . '</u> ' . ($theme['status'] == 1 ? '| 
		<font color="red"><b>'. $lng['Yopilgan'] .'!</b></font>' : NULL) . '  ';
		
		
        echo '<br/><a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/closed_run/">
		<b>'.($theme['status'] == 0 ? ''. $lng['Yopish'] .'' : ''. $lng['Ochish'] .'').'</b></a> / ';
        
        if ($theme['status'] == 0) {
            if (privilegy('forum_moder')) echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/fix/">
			'.($theme['fix'] == 0 ? ''. $lng['Mahkamlash'] .'' : ''. $lng['Bo`shatish'] .'').'</a> / ';
            echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/edit/">'. $lng['Tahrirlash'] .'</a> / ';
            if (privilegy('forum_moder')) echo '<a href="?del">'. $lng['O`chirish'] .'</a> / ';
            if (privilegy('forum_moder')) echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/transfer/">'. $lng['Ko`chirish'] .'</a> / ';
echo '<a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'. $lng['Yangilash'] .'</a>';
        }
        echo '</div>'; 
    } elseif (privilegy('forum_moder') || $theme['user_id'] == $user['user_id']) {    
        echo '<div class="white"> <u>' . $theme['name'] . '</u> ' . ($theme['status'] == 1 ? '| <font color="red"><b>'. $lng['Yopilgan'] .'!</b></font>' : NULL) . '<br/> ';
        echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/closed_run/"><b>
		'.($theme['status'] == 0 ? ''. $lng['Yopish'] .'' : ''. $lng['Ochish'] .'').'</b></a> / ';
        if ($theme['status'] == 0) {
            if (privilegy('forum_moder')) echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/fix/">
			'.($theme['fix'] == 0 ? ''. $lng['Mahkamlash'] .'' : ''. $lng['Bo`shatish'] .'').'</a> / ';
            echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/edit/">'. $lng['Tahrirlash'] .'</a> / ';
            echo '<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/transfer/">'. $lng['Ko`chirish'] .'</a> / ';
            if (privilegy('forum_moder')) {
                echo '<a href="?del">'. $lng['O`chirish'] .'</a> / ';
            }
echo '<a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'. $lng['Yangilash'] .'</a>';
        }
        echo '</div>';  
    }
	
    if (!empty($theme['vote'])) {
        echo '<div class="white"><b>' . $theme['vote'] . '</b><br />';
        
        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_VOTE." WHERE `theme_id` = ? && `user_id` = ? ", array($theme['id'], $user['user_id'])) == FALSE && $theme['status'] == 0) {
            echo '<form action="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/vote/" method="POST">
			<select name="variant" style="width:95%;">';
            for ($i = 1; $i <= 10; ++$i) {
                echo (!empty($theme['vote_'.$i]) ? '<option value="'.$i.'"">'.$theme['vote_'.$i].'</option>' : NULL);
            }
            echo '</select> <input type="submit" name="myvote" value="'. $lng['Ovoz berish'] .'" /></form>';
        } else {
            for ($i = 1; $i <= 10; ++$i) {
                $votes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_VOTE." WHERE `theme_id` = ? && `variant` = ? ", array($theme['id'], $i));
                echo (!empty($theme['vote_'.$i]) ? '<b>' . $i . '.</b> ' . $theme['vote_'.$i].' ['.$votes.' '. $lng['kishi'] .']<br />' : NULL);
            }            
        }
    echo CLOSE_DIV;
    }
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_POST." WHERE `theme_id` = ?", array($theme['id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['forum_post'],'select=theme&forum='.$forum['id'].'&forumc='.$forumc['id'].'&theme='.$theme['id']);
        $sql = DB::$dbs->query("SELECT * FROM ".FORUM_POST." WHERE `theme_id` = ? LIMIT {$n->start()}, ".$config['write']['forum_post']."", array($theme['id']));
        while($post = $sql -> fetch()) {


		
		
echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 0 ;"><center>';
echo '' . avatar($post['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius: 0 6px 0 0 ;">';
$fsharh = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `user_id` = ?", array($post['user_id']));
echo '' . userLink($post['user_id']) . ' <span style="font-size:12px;font-weight:bold;">(' . $fsharh . ')</span>';
if ($user) {
echo '<span style="float:right;">' . ($user['user_id'] != $post['user_id'] && $theme['status'] == 0 ? '
<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/?post='.$post['id'].'">'.icon('sharh.png').'</a> 
<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/?ctpost='.$post['id'].'">'.icon('oko.png').'</a>  
' : NULL) . ( (privilegy('forum_moder') || $post['user_id'] == $user['user_id']) && $theme['status'] == 0 ? '
<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/delete/'.$post['id'].'/">'.icon('minus2.png').'</a> 
<a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/edit/'.$post['id'].'/">'.icon('pen2.png').'</a> ' : NULL) . '</span>';
}
echo '<br/><span style="font-size:11px;">' . vrem($post['time']) . '</span><br/>';
echo '</center></td></tr></table>';
echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-bottom: 5px;margin-left: 2px;margin-right: 2px;">';

            
            
            if (!empty($post['file'])) {
                
                $path = '../../files/forum/'.$post['file'];
                
                $size = get_size(filesize($path));
                $path_info = pathinfo($path);
				
          echo '<a href="'.HOME.'/files/forum/'.$post['file'].'">
			<img src="'.HOME.'/files/forum/'.$post['file'].'"  style="width:50%;"  />
			</a> &nbsp; ';
      
                echo '<br />'.icon('yuklama.png').' <a href="'.HOME.'/files/forum/'.$post['file'].'">
				<b>['. $lng['Yuklab olish'] .']</b></a> ['.$path_info['extension'].'] ['.$size.']<br /><br />'; 
            }
        
		
            if (!empty($post['ct'])) {
                $ct = DB::$dbs->queryFetch("SELECT `msg` FROM ".FORUMS_POST." WHERE `id` = ? ", array($post['ct']));
                echo DIV_CT . '<small><b>'. $lng['Sitata'] .':</b></small><br />' . text($ct['msg']) . CLOSE_DIV; 
            }
            
            echo text($post['msg']).'  ';
			
	//// Status	
	$status = DB::$dbs->queryFetch("SELECT * FROM ".STATUS." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1",array($post['user_id']));
    if ($post['user_id'] != $user['user_id']) {
        if (!empty($status)) {
		echo '<div class="white" style="font-size:11px;">';
            echo '-  ' . SubstrMaus(text($status['status']), 50) . '</div>';
        }
    } else {
        echo '<div class="white" style="font-size:11px;">';
		echo '- ' . (!empty($status['status']) ? SubstrMaus(text($status['status']), 50) : '') . ' ';
		echo ' </div>';
    
    }
	//// Status	
	
	   

echo '</div></div>';
	
        }
        echo $n->navi();        
    }
  

	
	/////////
	
if (!empty($_GET['post'])) {
    $post = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_POST." WHERE `id` = ? ", array(abs(num($_GET['post']))));
    
    if ($post) {
        echo DIV_BLOCK;
        echo '<b>'. $lng['Sharhga javob'] .':</b><br />';
        echo '<b>' . userLink($post['user_id']) . '</b> [' . vrem($post['time']) . ']<br />';
        echo SubstrMaus(text($post['msg']), 100);
        echo CLOSE_DIV;
    }
}

if (!empty($_GET['ctpost'])) {
    $post2 = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_POST." WHERE `id` = ? ", array(abs(num($_GET['ctpost']))));
    
    if ($post2) {
        echo DIV_BLOCK;
        echo '<b>'. $lng['Sharga sitata keltirish'] .':</b><br />';
        echo '<b>' . userLink($post2['user_id']) . '</b> [' . vrem($post2['time']) . ']<br />';
        echo SubstrMaus(text($post2['msg']), 100);
        echo CLOSE_DIV;
    }
}

if ($_POST['add']) {

    $msg = html($_POST['msg']);
    
    if (empty($msg)) {
        $err .= ''. $lng['Habarni kiriting'] .'.<br />';
    }

    if (!empty($_FILES['file'])) {
        $name = $_FILES['file']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name, '.')); # Fayl formati
        $size = $_FILES['file']['size']; # Fayl hajmi
        $time = time();
        $file = $time.$ext;

        if ($size > (1048576 * $config['max_upload_forum'])) {
            $err .= ''. $lng['Fayl hajmi belgilangan miqdordan oshmoqda'] .'. [Max. '.$config['max_upload_forum'].'Mb.]<br />';
        }
            
        if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
            $err .= ''. $lng['Fayl formatida xatolik'] .'.<br />';
        }
    }
            
    if (empty($err)) {
        $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
        $page = ceil(($posts / $config['write']['forum_post']));    
        
        if (!empty($ext)) {
            copy($_FILES['file']['tmp_name'], '../../files/forum/'.$time.$ext);
        }
        
        $file = (empty($ext) ? 0 : $file);
        
        if (!empty($post)) {
            $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ?",array($post['user_id']));
            $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
                 
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizning habaringizga mavzuda javob berdi'] .'  <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/?p='.$page.'"><b>'.$theme['name'].'</b></a>';
            lenta($lenta, $ank['user_id']);
            
            $var = TRUE;
        }
        
        if (!empty($post2)) {
            $ct = $post2['id'];
        } else {
            $ct = NULL;
        }

        if (empty($var) && !empty($theme['uvedom'])) {
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizning mavzungizda habar qoldirdi'] .' <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/?p='.$page.'"><b>'.$theme['name'].'</b></a> ';
            lenta($lenta, $theme['user_id']);
        }    
        //new_posts($theme['id']);
        
        DB::$dbs->query("INSERT INTO ".FORUM_POST." (`forum_id`, `forumc_id`, `theme_id`, `msg`, `user_id`, `time`, `file`, `ct`) VALUES 
        (?,?,?,?,?,?,?,?)", array($forum['id'], $forumc['id'], $theme['id'], $msg, $user['user_id'], time(), $file, $ct));
        DB::$dbs->query("UPDATE ".FORUMS_THEME." SET `activ` = ? WHERE `id` = ? ", array(time(), $theme['id']));
        balls_operation(2);
        header("Location: ".HOME."/forum/".$forum['id']."/".$forumc['id']."/".$theme['id']."/?p=".$page);
    
    } else {
        echo DIV_ERROR . $err . CLOSE_DIV;
    }
    	
}
if ($user) {
echo '<div class="white">';
echo '<form action="#" enctype="multipart/form-data" method="POST">
<textarea name="msg" style="width:95%;height:5pc;" placeholder="'. $lng['Habar yozing'] .'"></textarea><br />';
///echo '<b>'. $lng['Fayl biriktirish'] .':</b> [max. '.$config['max_upload_forum'].'mb.]<br />';
echo '<input type="file" name="file"/><br />';
echo '<input type="submit" name="add" value="'. $lng['Javob berish'] .'" />';
echo '<span class="count" style="font-size:12px;"> &laquo; <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/">'. $lng['Orqaga'] .'</a> &laquo; <a href="'.HOME.'/forum/">'. $lng['Forum'] .'</a></span><br/>';
echo '</form>';
echo '</div>';
	////////  	
}


    
    break;
    
    case 'rating':
    $id = abs(intval($_GET['id']));
    $post = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ?",array($id));
    
    if (empty($post)) {
        head(''. $lng['Yangiliklar topilmadi'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
    }   
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_RATING." WHERE `theme_id` = ? && `user_id` = ? ", array($post['id'], $user['user_id'])) == TRUE) {
        head(''. $lng['Siz ovoz bergansiz'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();        
    }
    
    
    if ($_GET['type'] == 'like') {
        DB::$dbs->query("INSERT INTO ".FORUM_RATING." (`theme_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($post['id'], $user['user_id'], 'like'));
        DB::$dbs->query("UPDATE ".FORUMS." SET `rating` = ? WHERE `id` = ? ", array(($post['rating'] + 1), $post['id']));
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['ga sizning sharhingiz yoqdi'] .' | <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/?p='.$page.'"><b>'.$theme['name'].'</b></a>';
        lenta($lenta, $post['user_id']);
    } else {
        DB::$dbs->query("INSERT INTO ".FORUM_RATING." (`theme_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($post['id'], $user['user_id'], 'not_like'));
        DB::$dbs->query("UPDATE ".FORUMS." SET `rating` = ? WHERE `id` = ? ", array(($post['rating'] - 1), $post['id']));
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['ga sizning sharhingiz yoqmadi'] .' | <a href="'.HOME.'/forum/'.$forum['id'].'/'.$forumc['id'].'/'.$theme['id'].'/?p='.$page.'"><b>'.$theme['name'].'</b></a>';
        lenta($lenta, $post['user_id']);
    }
	
    
    header("Location: ".HOME."/forum/".$forum['id']."/".$forumc['id']."/".$theme['id']."/?p=".$page."/");
    break;
}


require_once('../../core/stop.php');
?>