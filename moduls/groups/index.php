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
    
    default:
    head(''. $lng['Guruhlar'] .'');
    
      
	
	if (empty($_GET['id'])) {
    $page = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array(num($_SESSION['user_id'])));
} else {
    $page = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array(num($_GET['id'])));
}


if ($user) {
    echo '<div class="grey">';
	
	    if (check() == FALSE) {
        echo '[+] <a style="font-size:13px;" href="'.HOME.'/groups/new/">'. $lng['Guruh yaratish'] .'</a>';
    } else {
        echo '<a style="font-size:13px;" href="'.HOME.'/groups/'.my().'/">'. $lng['Mening guruhim'] .'</a>';
    }
	
	echo ' / <a href="/group/'.$page['user_id'].'/" style="font-size:13px;"> '. $lng['A`zo guruhlarim'] .'</a><br/>'; 
    echo '</div>';
}
	
echo '<div class="lines">';
echo ''.icon('search.png').' <a href="'.HOME.'/search/group/">'. $lng['Izlash'] .'</a>'; 
echo '</div>';
	
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS."");
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Guruhlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".GROUPS." ORDER BY `id` DESC ");
        while($group = $sql -> fetch()) {
$peoples = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? ", array($group['id']));
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">';
echo '' . (empty($group['logo']) ? '<img src="' . HOME . '/style/img/nogroup.png" style="width:45px;height:45px;border-radius:55%;"/>' : '<img src="' . HOME . '/files/groups/'.$group['logo'] . '"  style="width:45px;height:45px;border-radius:55%;"/>') . '';
   
echo '</td>';

echo '<td class="lines" style="vertical-align:top;" width="95%" >';
           echo '<a href="'.HOME.'/groups/'.$group['id'].'/"><b>' . $group['name'] . '</b></a> 
		   
<span style="float:right;font-size:12px;color:#757575;">'.$peoples.' '.icon('group.png',12,11).' </span></br>';
    echo '<span style="font-size:13px;color:#757575;">';
	 echo SubstrMaus(text($group['info']), 60);
	 echo '</span><br /> ';
echo '</td></tr></table>';
 
        }
    }
    

    
       
    break;
    
    case 'view':
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        head(''. $lng['Guruh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
head(' ' . $group['name']);

    echo '' . (empty($group['cover']) ? '<img src="' . HOME . '/style/img/nogroupcover.png" style="margin-bottom:-60px;width:100%;height:226px;"/>' : 
	'<img src="' . HOME . '/files/groups/cover/'.$group['cover'] . '" style="margin-bottom:-60px;width:100%;height:226px;"/>') . '';
    
echo '<div class="white"><center>';
     echo ''.(empty($group['logo']) ? '<img src="' . HOME . '/style/img/nogroup.png" style="width:100px;border-radius:55%;"/>' : 
	'<img src="' . HOME . '/files/groups/'.$group['logo'] . '"  style="width:100px;height:100px;border-radius:55%;"/>') .'';
    

    echo '<br/><b>' . $group['name'] . '</b><br /><span class="mini">';
	 echo SubstrMaus(text($group['info']), 100);
echo '</span></center></div>';



    
    echo DIV_LI . ''.icon('info.png').' <a href="'.HOME.'/groups/'.$group['id'].'/info/">'. $lng['Ma`lumotlar'] .'</a> ' . CLOSE_DIV; 
   
    if ($group['private_forum'] == 1) {
        if (check_private($group['id']) == TRUE) {
            $topics = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_TOPIC." WHERE `group_id` = ? ", array($group['id']));
            $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_POST." WHERE `group_id` = ? ", array($group['id']));
            echo DIV_LI . ''.icon('forumlar.png').' <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> 
			<span class="count">'.$topics.'/'.$posts.'</span>' . CLOSE_DIV; 
        }    
    } else {
        $topics = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_TOPIC." WHERE `group_id` = ? ", array($group['id']));
        $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_POST." WHERE `group_id` = ? ", array($group['id']));
        echo DIV_LI . ''.icon('forumlar.png').' <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> 
		<span class="count">'.$topics.'/'.$posts.'</span>' . CLOSE_DIV;         
    }
    

    $peoples = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? ", array($group['id']));
    echo DIV_LI . ''.icon('users.png').' <a href="'.HOME.'/groups/'.$group['id'].'/peoples/">'. $lng['Ishtrokchilar'] .'</a> 
	<span class="count">'.$peoples.'</span>' . CLOSE_DIV; 

 
if ($user) {
    if ($group['user_id'] != $user['user_id']) {
        echo DIV_BLOCK;	
        $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `user_id` = ? ", array($group['id'], $user['user_id']));
        if ($sql == FALSE) {
            echo '<form action="'.HOME.'/groups/'.$group['id'].'/join/" method="POST"><input type="submit" value="'. $lng['A`zo bo`lish'] .'" /></form>';
        } else {
            echo '<form action="'.HOME.'/groups/'.$group['id'].'/leave/" method="POST"><input type="submit" value="'. $lng['Tark etish'] .'" /></form>';
        }
    echo CLOSE_DIV;
    }
}
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';

    require_once('../../core/stop.php'); 
    break;
    
    case 'admin':
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        head(''. $lng['Guruh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    } 
    
    head(''. $lng['Ma`muriyat'] .'');
            
    
    $n = new Navigator($all,10,'id='.$group['id']);
    $sql = DB::$dbs->query("SELECT * FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `level` > ? ORDER BY `id` DESC LIMIT {$n->start()}, 10", array($group['id'], 0));
    while($ank = $sql -> fetch()) {
	echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">
' . avatar($ank['user_id'],50,50) . '';
echo '</td>';

echo '<td class="lines" style="vertical-align:top;" width="95%" >
' . userLink($ank['user_id']) . ' <br/><b>' . group_level($ank['level']) . '</b>';
echo '</td></tr></table>';
    }

echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
	
    echo $n->navi();
    break;
    
    case 'peoples':
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        head(''. $lng['Guruh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    } 
    
    head(''. $lng['Ishtrokchilar'] .'');
    
    
    $n = new Navigator($all,10,'id='.$group['id']);
    $sql = DB::$dbs->query("SELECT * FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 10", array($group['id']));
    while($ank = $sql -> fetch()) {
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">
' . avatar($ank['user_id'],50,50) . '';
echo '</td>';

echo '<td class="lines" style="vertical-align:top;" width="95%" >
' . userLink($ank['user_id']) . ' <br/><b>' . group_level($ank['level']) . '</b>';
echo '</td></tr></table>';

    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';  
    echo $n->navi();
    break;
    
    case 'join':
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        head(''. $lng['Guruh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }    

    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `user_id` = ? ", array($group['id'], $user['user_id']));
    if ($sql == FALSE) {
        DB::$dbs->query("INSERT INTO ".GROUPS_PEOPLES." (`group_id`, `user_id`, `level`) VALUES (?, ?, ?)", array($group['id'], $user['user_id'], 0));           
        header("Location: ".HOME."/groups/".$group['id']."/");
    } else {
        head(''. $lng['Siz ushbu guruhga a`zo bo`lgansiz'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit();         
    }
    break;

    case 'leave':
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        head(''. $lng['Guruh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    } 
    
    if ($group['user_id'] == $user['user_id']) {
        head(''. $lng['Diqqat'] .'!');
            
        echo '<div class="white">'. $lng['Siz guruhni tark etolmaysiz chunki siz ushbu guruhni yaratuvchisiz'] .'</div>'; 
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }    
    
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `user_id` = ? ", array($group['id'], $user['user_id']));
    if ($sql == TRUE) {
        DB::$dbs->query("DELETE FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `user_id` = ? ", array($group['id'], $user['user_id']));           
        header("Location: ".HOME."/groups/".$group['id']."/");
    } else {
        head(''. $lng['Siz ushbu guruh ishtrokchisi emassiz'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit();         
    }
    break;
        
    case 'new':
    head(''. $lng['Guruh yaratish'] .'');
    check_auth();
    if (!empty($_POST['add'])) {
        
        $name2 = html($_POST['name']);
        $info = html($_POST['info']);
        
        if (!empty($_FILES['file']['name'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
            $size = $_FILES['file']['size']; # Fayl hajmi
            $time = time();
            $file = $time.$ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
            
            if ($size > (1048576 * $config['max_upload_group'])) {
                $err .= ''. $lng['Foto hajmi belgilangan miqdordan oshmoqda'] .'. [Max. '.$config['max_upload_group'].'Mb.]<br />';
            }
            
            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
            }
                
        }
        
        if (empty($name2)) {
            $err .= ''. $lng['Guruh nomini to`ldiring'] .'<br />';
        }
        
        if (empty($info)) {
            $err .= ''. $lng['Guruh ta`rifini to`ldiring'] .'<br />';
        }
        
        if (empty($err)) {
            if (empty($ext)) {
                $file = 0;
            }
            
            copy($_FILES['file']['tmp_name'], '../../files/groups/'.$file); # Original tarzda yuklaymiz
            img_resize('../../files/groups/'.$file, '../../files/groups/mini_'.$file, $config['mini_logo_par'][0], $config['mini_logo_par'][1]); # Mini
            DB::$dbs->query("INSERT INTO ".GROUPS." (`name`, `info`, `logo`, `user_id`, `time`, `peoples`) VALUES (?, ?, ?, ?, ?, ?)", array($name2, $info, $file, $user['user_id'], time(), 1));
            $lastid = DB::$dbs->lastInsertId();
            DB::$dbs->query("INSERT INTO ".GROUPS_PEOPLES." (`group_id`, `user_id`, `level`) VALUES (?, ?, ?)", array($lastid, $user['user_id'], 2));
            header("Location: ".HOME."/groups/".$lastid."/panel/");
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;
        }
        
    }
       
    
    echo DIV_AUT;
    echo '<form action="#" method="POST" enctype="multipart/form-data">';
    echo '<b>'. $lng['Nomi'] .':</b> [max. 100]<br />
	<input type="text" name="name" style="width:95%;"/><br /><br />';
    echo '<b>'. $lng['Ta`rif'] .':</b> [max. 250]<br />
	<textarea name="info" style="width:95%;height:5pc;"></textarea><br /><br />';
    echo '<b>'. $lng['Logotip'] .':</b> [max. '.$config['max_upload_group'].'mb., jpg, gif, png]<br />
	<input type="file" name="file" style="width:95%;"/><br /><br />';
    echo '<input type="submit" name="add" value="'. $lng['Yaratish'] .'" /></form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
        
    case 'info':
    head(''. $lng['Ma`lumotlar'] .'');
    $id = abs(num($_GET['id']));
    $group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
    if (empty($group)) {
        head(''. $lng['Guruh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    } 
	
echo '<div class="grey" style="font-size:12px;">';
echo '<b>' . $group['name'] . '</b><br />
'. $lng['ID Raqami'] .': ' . $group['id'] . '<br/>';
echo '</div>';




echo '<div class="white">';
echo '<span style="float:right;margin-top: -40px;">';
     echo ''.(empty($group['logo']) ? '<img src="' . HOME . '/style/img/nogroup.png" style="border:4px solid #fff;width:60px;border-radius:55%;"/>' : 
	'<img src="' . HOME . '/files/groups/'.$group['logo'] . '"  style="border:4px solid #fff;width:60px;height:60px;border-radius:55%;"/>') .'';
    
echo '</span>';
echo '</div>';

echo '<div class="white">';
    echo '<span class="mini">';
	 echo SubstrMaus(text($group['info']), 100);
echo '</span></div>';
	
	
    $admins = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `level` > ? ", array($group['id'], 0));
    echo DIV_LI . ''.icon('adm.png').' <a href="'.HOME.'/groups/'.$group['id'].'/admin/">'. $lng['Ma`muriyat'] .'</a> 
	<span class="count">'.$admins.'</span>' . CLOSE_DIV; 
	echo ($group['user_id'] == $user['user_id'] ? DIV_LI . ''.icon('tizim.png').' <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a>' . CLOSE_DIV : NULL);
   
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
}


require_once('../../core/stop.php');
?>