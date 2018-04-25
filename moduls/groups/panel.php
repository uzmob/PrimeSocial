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
$group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));
    
if (empty($group)) {
    head(''. $lng['Guruh topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if ($group['user_id'] != $user['user_id'] && privilegy('group') == FALSE) {
    head(''. $lng['Kirishda xatolik'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}
    
switch ($select) {
    
    default:
    head(''. $lng['Guruhni boshqarish'] .'');
	
        echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/">
	'.icon('qoida.png').' '.$group['name'].'
	</a></div>';
		  
    echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/panel/logo/">
	'.icon('rasm.png').' '. $lng['Guruh logotipi'] .'
	</a></div>';
	echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/panel/cover/">
	'.icon('rasm2.png').' '. $lng['Guruh muqovasi'] .'
	</a></div>';
    echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/panel/info/">
	'.icon('sozlash.png').' '. $lng['Asosiy ma`lumot'] .'
	</a></div>';
    echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/panel/private/">
	'.icon('privat.png').' '. $lng['Shahsiylik sozlamalari'] .'
	</a></div>';
    echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/">
	'.icon('users.png').' '. $lng['Ishtrokchilar'] .'
	</a></div>';
    echo '<div class="touch"><a href="'.HOME.'/groups/'.$group['id'].'/panel/admin/">
	'.icon('adm.png').' '. $lng['Ma`muriyat'] .'
	</a></div>';
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'logo':
    head(''. $lng['Guruh logotipi'] .'');
    
    if (isset($_GET['del'])) {
        unlink('../../files/groups/'.$group['logo']);
        unlink('../../files/groups/mini_'.$group['logo']);
        DB::$dbs->query("UPDATE ".GROUPS." SET `logo` = ? WHERE `id` = ?", array('', $group['id']));
        header("Location: ".HOME."/groups/".$group['id']."/panel/logo/");
    }
    
    if (!empty($_POST['upload'])) {
        $name = $_FILES['file']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name, '.')); # Fayl formati
        $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
        $size = $_FILES['file']['size']; # Fayl hajmi
        $time = time();
        $file = $time.$ext;
        $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
            
        if ($size > (1048576 * $config['max_upload_group'])) {
            $err .= ''. $lng['Fayl hajmi belgilangan miqdordan oshmoqda'] .'. [Max. '.$config['max_upload_group'].'Mb.]<br />';
        }
            
        if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
            $err .= ''. $lng['Fayl formati noto`g`ri'] .'.<br />';
        }
        
        if (empty($err)) {
            @unlink('../../files/groups/'.$group['logo']);
            @unlink('../../files/groups/mini_'.$group['logo']);
            
            copy($_FILES['file']['tmp_name'], '../../files/groups/'.$file); # Original tarzda yuklaymiz
            img_resize('../../files/groups/'.$file, '../../files/groups/mini_'.$file, $config['mini_logo_par'][0], $config['mini_logo_par'][1]); # Mini
            DB::$dbs->query("UPDATE ".GROUPS." SET `logo` = ? WHERE `id` = ? ", array($file, $group['id']));
            
            header("Location: ".HOME."/groups/".$group['id']."/panel/logo/");
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;;
        }
    }
    
    echo DIV_BLOCK . (empty($group['logo']) ? '<img src="' . HOME . '/style/img/nogroup.png" style="width:150px;"/>' : '<img src="' . HOME . '/files/groups/mini_'.$group['logo'] . '" />') . CLOSE_DIV;
    echo (!empty($group['logo']) ? DIV_BLOCK . '<a href="?del">'. $lng['O`chirish'] .'</a>' . CLOSE_DIV : NULL);
    
    echo DIV_AUT;
    echo '<form action="#" method="POST" enctype="multipart/form-data">';
    echo '<b>'. $lng['Logotip'] .':</b> [max. '.$config['max_upload_group'].'mb., jpg, gif, png]<br /><input type="file" name="file"/><br /><br />';
    echo '<input type="submit" name="upload" value="'. $lng['Yuklash'] .'" /></form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
     break;
	
    case 'cover':
    head(''. $lng['Guruh muqovasi'] .'');
    
    if (isset($_GET['del'])) {
        unlink('../../files/groups/cover/'.$group['cover']);
        DB::$dbs->query("UPDATE ".GROUPS." SET `cover` = ? WHERE `id` = ?", array('', $group['id']));
        header("Location: ".HOME."/groups/".$group['id']."/panel/cover/");
    }
    
    if (!empty($_POST['upload'])) {
        $name = $_FILES['file']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name, '.')); # Fayl formati
        $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
        $size = $_FILES['file']['size']; # Fayl hajmi
        $time = time();
        $file = $time.$ext;
        $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
            
        if ($size > (1048576 * $config['max_upload_group'])) {
            $err .= ''. $lng['Fayl hajmi belgilangan miqdordan oshmoqda'] .'. [Max. '.$config['max_upload_group'].'Mb.]<br />';
        }
            
        if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
            $err .= ''. $lng['Fayl formati noto`g`ri'] .'.<br />';
        }
        
        if (empty($err)) {
            @unlink('../../files/groups/cover/'.$group['cover']);
            
            copy($_FILES['file']['tmp_name'], '../../files/groups/cover/'.$file); # Original tarzda yuklaymiz
            DB::$dbs->query("UPDATE ".GROUPS." SET `cover` = ? WHERE `id` = ? ", array($file, $group['id']));
            
            header("Location: ".HOME."/groups/".$group['id']."/panel/cover/");
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;;
        }
    }
    
    echo ''. (empty($group['cover']) ? '' : 
	'<div class="white"><img src="' . HOME . '/files/groups/cover/'.$group['cover'] . '" style="width:150px;"/></div>') . '';
    echo (!empty($group['cover']) ? DIV_BLOCK . '<a href="?del">'. $lng['O`chirish'] .'</a>' . CLOSE_DIV : NULL);
    
    echo DIV_AUT;
    echo '<form action="#" method="POST" enctype="multipart/form-data">';
    echo '<b>'. $lng['Muqova'] .':</b> [max. '.$config['max_upload_group'].'mb., jpg, gif, png]<br /><input type="file" name="file"/><br /><br />';
    echo '<input type="submit" name="upload" value="'. $lng['Yuklash'] .'" /></form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    case 'info':
    head(''. $lng['Asosiy ma`lumotlar'] .'');
    
    if (!empty($_POST['edit'])) {
        $name2 = html($_POST['name']);
        $info = html($_POST['info']);
        
        if (empty($name2)) {
            $err .= ''. $lng['Guruh nomini to`ldiring'] .'<br />';
        }
        
        if (empty($info)) {
            $err .= ''. $lng['Guruh ta`rifini to`ldiring'] .'<br />';
        }
        
        if (empty($err)) {
            DB::$dbs->query("UPDATE ".GROUPS." SET `name` = ?, `info` = ? WHERE `id` = ? ", array($name2, $info, $group['id']));
            header("Location: ".HOME."/groups/".$group['id']."/panel/info/");
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;
        }
    }
    

    echo DIV_AUT;
    echo '<form action="#" method="POST">';
    echo '<b>'. $lng['Nomi'] .':</b> [max. 100]<br />
	<input type="text" name="name" value="'.$group['name'].'" style="width:95%;"/><br /><br />';
    echo '<b>'. $lng['Ta`rif'] .':</b> [max. 250]<br />
	<textarea name="info" style="width:95%;height:5pc;">'.$group['info'].'</textarea><br /><br />';
    echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'private':
    head(''. $lng['Shahsiylik sozlamalari'] .'');
    
    if (!empty($_POST['sett'])) {
        $private_forum = num($_POST['private_forum']);
        DB::$dbs->query("UPDATE ".GROUPS." SET `private_forum` = ? WHERE `id` = ? ", array($private_forum, $group['id']));
        header("Location: ".HOME."/groups/".$group['id']."/panel/private/");
    }


    echo DIV_AUT;
    echo '<b>'. $lng['Forumga kirishi mumkun'] .':</b><br />';
    echo '<form action="#" method="POST">';
    echo '<select name="private_forum" style="width:95%;">';
    echo '<option '.(0 == $group['private_forum'] ? 'selected="selected"' : NULL).' value="0">'. $lng['Hamma'] .'</option>';
    echo '<option '.(1 == $group['private_forum'] ? 'selected="selected"' : NULL).' value="1">'. $lng['Faqat ishtrokchilar'] .'</option>';
    echo '</select>';
    echo '<input type="submit" name="sett" value="'. $lng['Saqlash'] .'" /></form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'peoples':
    head(''. $lng['Ishtrokchilar'] .'');
    $n = new Navigator($all,10,'select=peoples&id='.$group['id']);
    $sql = DB::$dbs->query("SELECT * FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 10", array($group['id']));
    while($ank = $sql -> fetch()) {
        echo DIV_LI . userLink($ank['user_id']) . ' <b>' . group_level($ank['level']) . '</b> 
		<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/'.$ank['user_id'].'/update/">'.icon('pen2.png').'</a> 
		<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/'.$ank['user_id'].'/delete/">'.icon('minus2.png').'</a>' . CLOSE_DIV;
    }  
    echo $n->navi();
     
    echo DIV_AUT;
    echo '<form action="'.HOME.'/groups/'.$group['id'].'/panel/peoples/search/" method="POST">';
    echo '<b>ID</b> '. $lng['yoki'] .' <b>Login</b>:<br />';
    echo '<input type="text" name="user"  style="width:95%;"/><br />
    <input type="checkbox" name="type" value="1" /> '. $lng['ID orqali izlash'] .'<br />
    <input type="submit" name="search" value="'. $lng['Izlash'] .'"/><br />';
    echo '</form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'update':
    head(''. $lng['Ishtrokchini tahrirlash'] .'');
    
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_PEOPLES." WHERE `user_id` = ? && `group_id` = ? ", array(abs(num($_GET['user'])), $group['id']));
    
    if ($ank['user_id'] == $user['user_id']) {
        echo DIV_ERROR . ''. $lng['mumkunmas'] .'' . CLOSE_DIV;
    } else {
        if (empty($ank)) {
            echo DIV_ERROR . ''. $lng['Foydalanuvchi topilmadi'] .'' . CLOSE_DIV;
        } else {
            if (!empty($_POST['level'])) {
                $level = num($_POST['level']);
                DB::$dbs->query("UPDATE ".GROUPS_PEOPLES." SET `level` = ? WHERE `user_id` = ? && `group_id` = ? ", array($level, $ank['user_id'], $group['id']));
                header("Location: ".HOME."/groups/".$group['id']."/panel/peoples/");
            }
            
            echo DIV_AUT;
            echo '<b>'. $lng['Daraja'] .':</b><br />';
            echo '<form action="#" method="POST">';
            echo '<select name="level">';
            echo '<option '.(0 == $ank['level'] ? 'selected="selected"' : NULL).' value="0">'. $lng['Ishtrokchi'] .'</option>';
            echo '<option '.(1 == $ank['level'] ? 'selected="selected"' : NULL).' value="1">'. $lng['Moderator'] .'</option>';
            echo '</select><br /><br />';
            echo '<input type="submit" name="update" value="'. $lng['Saqlash'] .'" /></form>' . CLOSE_DIV;
        }
    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'delete':
    head(''. $lng['Ishtrokchini o`chirish'] .'');
    
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_PEOPLES." WHERE `user_id` = ? && `group_id` = ? ", array(abs(num($_GET['user'])), $group['id']));
    
    if ($ank['user_id'] == $user['user_id']) {
        echo DIV_ERROR . ''. $lng['mumkunmas'] .'' . CLOSE_DIV;
    } else {
        if (empty($ank)) {
            echo DIV_ERROR . ''. $lng['Foydalanuvchi topilmadi'] .'' . CLOSE_DIV;
        } else {
            if (!isset($_GET['go'])) {
                echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
				<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/'.$ank['user_id'].'/delete/?go">['. $lng['O`chirish'] .']</a> 
				<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
            } else {
                DB::$dbs->query("DELETE FROM ".GROUPS_PEOPLES." WHERE `user_id` = ? && `group_id` = ? ", array(abs(num($_GET['user'])), $group['id']));
                header("Location: ".HOME."/groups/".$group['id']."/panel/peoples/"); 
            }  
        }
    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'search':
    head(''. $lng['Ishtrokchilarni izlash'] .'');
    
    $sql = html($_POST['user']);
    $type = abs(num($_POST['type']));
    
    if (empty($sql)) {
        header("Location: ".HOME."/groups/".$group['id']."/panel/peoples/");
    }
    
    if (!empty($type)) {
        $sql = abs(num($sql));
        $all = DB::$dbs->queryFetch("SELECT `level` FROM ".GROUPS_PEOPLES." WHERE `user_id` = ? && `group_id` = ? ", array($sql, $group['id']));
        
        if (!empty($all)) {
            echo DIV_BLOCK . '<b>'. $lng['Izlash natijalari'] .':</b><br />' . userLink($sql) . '  [<b>' . group_level($all['level']) . '</b>]' . CLOSE_DIV;
        } else {
            echo DIV_BLOCK . '<b>'. $lng['Izlash natijalari'] .':</b><br />'. $lng['Ishtrokchi topilmadi'] .'' . CLOSE_DIV;
        }
    }
    
    
    echo DIV_LI . '<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/">'. $lng['So`rovni takrorlash'] .'</a>' . CLOSE_DIV;   
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;

    case 'admin':
    head(''. $lng['Ma`muriyat'] .'');
    $n = new Navigator($all,10,'select=peoples&id='.$group['id']);
    $sql = DB::$dbs->query("SELECT * FROM ".GROUPS_PEOPLES." WHERE `group_id` = ? && `level` > 0 ORDER BY `id` DESC LIMIT {$n->start()}, 10", array($group['id']));
    while($ank = $sql -> fetch()) {
        echo DIV_LI . userLink($ank['user_id']) . ' <b>' . group_level($ank['level']) . '</b> 
		<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/'.$ank['user_id'].'/update/">'.icon('pen2.png').'</a> 
		<a href="'.HOME.'/groups/'.$group['id'].'/panel/peoples/'.$ank['user_id'].'/delete/">'.icon('minus2.png').'</a>' . CLOSE_DIV;
    }  
    echo $n->navi();
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/panel/">'. $lng['Guruhni boshqarish'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
}


require_once('../../core/stop.php');
?>