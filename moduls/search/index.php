<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');


switch ($_GET['act']) {
    
    default:
	head(''. $lng['Sayt bo`yicha izlash'] .'');

echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post">
<input name="sql" placeholder="'. $lng['Nimani izlaymiz'] .'?" style="width:95%;"/><br/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $count = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `nick` LIKE '%".$sql."%'");
    echo DIV_LI . '<a href="'.HOME.'/search/user/">'. $lng['Foydalanuvchilar'] .'</a> <span class="count">'.$count.'</span>' . CLOSE_DIV;
    
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG." WHERE `title` LIKE '%".$sql."%' OR `blog` LIKE '%".$sql."%'");
    echo DIV_LI . '<a href="'.HOME.'/search/blog/">'. $lng['Bloglar'] .'</a> <span class="count">'.$count.'</span>' . CLOSE_DIV;
    
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_THEME." WHERE `name` LIKE '%".$sql."%'");
    echo DIV_LI . '<a href="'.HOME.'/search/forumt/">'. $lng['Forumdagi mavzular'] .'</a> <span class="count">'.$count.'</span>' . CLOSE_DIV;
    
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_POST." WHERE `msg` LIKE '%".$sql."%'");
    echo DIV_LI . '<a href="'.HOME.'/search/forump/">'. $lng['Forumdagi sharhlar'] .'</a> <span class="count">'.$count.'</span>' . CLOSE_DIV;
    
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS." WHERE `name` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%'");
    echo DIV_LI . '<a href="'.HOME.'/search/group/">'. $lng['Guruhlar'] .'</a> <span class="count">'.$count.'</span>' . CLOSE_DIV;
    
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_ARTICL." WHERE `title` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%'");
    echo DIV_LI . '<a href="'.HOME.'/search/lib/">'. $lng['Kutubxonadagi maqolalar'] .'</a> <span class="count">'.$count.'</span>' . CLOSE_DIV;
    break;
    
    case 'user':
	head(''. $lng['Foydalanuvchilarni izlash'] .'');
	


echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post"><input name="sql"/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `nick` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($count,10,'&act=' . html($_GET['act']) . '&'); 
        $sql = DB::$dbs->query("SELECT * FROM ".USERS." WHERE `nick` LIKE '%".$sql."%' ORDER BY `user_id` DESC LIMIT {$n->start()}, 10");
        while($ank = $sql -> fetch()) {
            echo DIV_BLOCK . '<b>' . userLink($ank['user_id']) . '</b><br />' . $ank['name'] . ', 
			(' . (!empty($ank['age']) ? ''. $lng['Yosh'] .': ' . $ank['age'] : ''. $lng['Yosh ko`rsatilmagan'] .'') . ') <br />' . city($ank['city']) . CLOSE_DIV;            
        }    
        echo $n->navi();         
    }
    break;
    
    case 'blog':
	head(''. $lng['Blog bo`yicha izlash'] .'');

echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post"><input name="sql"/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG." WHERE `title` LIKE '%".$sql."%' OR `blog` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($count,10,'&act=' . html($_GET['act']) . '&'); 
        $sql = DB::$dbs->query("SELECT * FROM ".BLOG." WHERE `title` LIKE '%".$sql."%' OR `blog` LIKE '%".$sql."%' ORDER BY `id` DESC LIMIT {$n->start()}, 10");
        while($blog = $sql -> fetch()) {
            echo DIV_BLOCK .'' . userLink($blog['user_id']) . ' <br/>'.icon('pages.png').' <a href="'.HOME.'/blog/'.$blog['id'].'/">' . $blog['title'] . '</a> <br />';
            echo CLOSE_DIV;
        }    
        echo $n->navi();                 
    }
    break;

    case 'forumt':
	head(''. $lng['Forumdagi mavzularni izlash'] .''); 

echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post"><input name="sql"/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_THEME." WHERE `name` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($count,10,'&act=' . html($_GET['act']) . '&'); 
        $sql = DB::$dbs->query("SELECT * FROM ".FORUMS_THEME." WHERE `name` LIKE '%".$sql."%' ORDER BY `id` DESC LIMIT {$n->start()}, 10");
        while($theme = $sql -> fetch()) {
            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
            echo DIV_LI . '<a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'.$theme['name'].'</a> ['.$posts.']' . CLOSE_DIV;
        }    
        echo $n->navi();              
    }
    break;  
    
    case 'forump':
	head(''. $lng['Forumdagi sharhlarni izlash'] .''); 

echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post"><input name="sql"/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUM_POST." WHERE `msg` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($count,10,'&act=' . html($_GET['act']) . '&'); 
        $sql = DB::$dbs->query("SELECT * FROM ".FORUM_POST." WHERE `msg` LIKE '%".$sql."%' ORDER BY `id` DESC LIMIT {$n->start()}, 10");
        while($post = $sql -> fetch()) {
            echo DIV_BLOCK;
            echo '<b>' . userLink($post['user_id']) . '</b> [' . vrem($post['time']) . ']<br />';
            
            if (!empty($post['ct'])) {
                $ct = DB::$dbs->queryFetch("SELECT `msg` FROM ".FORUMS_POST." WHERE `id` = ? ", array($post['ct']));
                echo DIV_CT . '<small><b>'. $lng['Sitata'] .':</b></small><br />' . text($ct['msg']) . CLOSE_DIV; 
            }
            
            echo text($post['msg']);
            
            if (!empty($post['file'])) {
                
                $path = '../../files/forum/'.$post['file'];
                
                $size = get_size(filesize($path));
                $path_info = pathinfo($path);
    
                echo '<br /><br />'. $lng['Biriktirilgan fayl'] .': <a href="'.HOME.'/files/forum/'.$post['file'].'"><b>['. $lng['Yuklab olish'] .']</b></a> ['.$path_info['extension'].'] ['.$size.']<br />'; 
            }
            
            echo '<br /><br /><b>'. $lng['Mavzu'] .':</b><br />';
            $theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array($post['theme_id']));
            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($post['theme_id']));
            echo '<a href="'.HOME.'/forum/'.$post['forum_id'].'/'.$post['forumc_id'].'/'.$post['theme_id'].'/">'.$theme['name'].'</a> ['.$posts.']<br />';            
            echo CLOSE_DIV;
        }    
        echo $n->navi();            
    }
    break;   
    
    case 'group':
	head(''. $lng['Guruhlarni izlash'] .'');

echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post"><input name="sql"/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS." WHERE `name` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($count,10,'&act=' . html($_GET['act']) . '&'); 
        $sql = DB::$dbs->query("SELECT * FROM ".GROUPS." WHERE `name` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%' ORDER BY `id` DESC LIMIT {$n->start()}, 10");
        while($group = $sql -> fetch()) {
            echo DIV_BLOCK;
            echo '<a href="'.HOME.'/groups/'.$group['id'].'/"><b>' . $group['name'] . '</b></a> ['.$group['peoples'].' '. $lng['kishi'] .']<br />';
            echo $group['info'] . '<br />';
            echo CLOSE_DIV;
        }    
        echo $n->navi();          
    }
    break; 
    
    case 'lib':
	head(''. $lng['Kutubxonadagi maqolalarni izlash'] .''); 

echo DIV_BLOCK . '<form action="'.HOME.'/search/" method="post"><input name="sql"/>
<input type="submit" value="'. $lng['Izlash'] .'"/></form>' . CLOSE_DIV;

$sql = html($_POST['sql']);
if (empty($sql)) {
    $sql = html($_SESSION['search']);
} else {
    $_SESSION['search'] = $sql;
}

    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_ARTICL." WHERE `title` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($count,10,'&act=' . html($_GET['act']) . '&'); 
        $sql = DB::$dbs->query("SELECT * FROM ".LIB_ARTICL." WHERE `title` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%' ORDER BY `id` DESC LIMIT {$n->start()}, 10");
        while($articl = $sql -> fetch()) {
            echo DIV_LI . '<a href="'.HOME.'/lib/'.$articl['folder_id'].'/'.$articl['folderc_id'].'/'.$articl['id'].'/">'.$articl['title'].'</a>' . CLOSE_DIV;
        }    
        echo $n->navi();           
    }
    break; 
}

if (isset($_GET['act'])) {
    echo DIV_LI . '- <a href="'.HOME.'/search/">'. $lng['Orqaga qaytish'] .'</a>' . CLOSE_DIV;
}
require_once('../../core/stop.php');
?>