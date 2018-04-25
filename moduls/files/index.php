<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

$id = abs(num($_GET['user']));
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));



if (empty($ank)) {
    head(''. $lng['Foydalanuvchi topilmadi'] .'');
    echo DIV_BLOCK . ''. $lng['Foydalanuvchi topilmadi'] .'' . CLOSE_DIV;  
    exit();
}

switch ($select) {
    
    default:
    head(''. $lng['Shahsiy fayllar'] .': ' . $ank['nick']);
    
	/* Shahsiylik */
if ($ank['user_id'] != $user['user_id']) {
    if ($ank['private_usfiles'] == 1) {
        $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE ((`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?)) && status = ? LIMIT 1",array($user['user_id'], $ank['user_id'], $user['user_id'], $ank['user_id'], 1));
        if (!$sql) {
            echo '<div class="sts"><center><b>' . $ank['nick'] . '</b> '. $lng['shahsiy fayllarini faqat uning do`stlari ko`ra olishadi'] .'</center></div>';
            require_once('../../core/stop.php');
            exit();
        }
    } else if ($ank['private_usfiles'] == 2) {
        echo '<div class="sts"><center><b>' . $ank['nick'] . '</b> '. $lng['shahsiy fayllarini hech kim ko`ra olmaydi'] .'</center></div>';
        require_once('../../core/stop.php');
        exit();
    }
}
/* */ 
	
	
    if ($_POST['add'] && $ank['user_id'] == $user['user_id']) {
        $name = html($_POST['name']);
        $type = abs(num($_POST['type']));
        
        if (empty($name)) {
            $err .= ''. $lng['Bo`lim nomini yozing'] .'<br />';
        }
        
        if (empty($type)) {
            $err .= ''. $lng['Bo`lim turini tanlang'] .'<br />';
        }
        
        if (!empty($err)) {
            echo DIV_ERROR . $err . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".FILES." (`name`, `type`, `user_id`) VALUES (?, ?, ?)", array($name, $type, $user['user_id']));
            header("Location: ".HOME."/files/".$user['user_id']."/"); 
        }
    }
	

    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES." WHERE `user_id` = ? ", array($ank['user_id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Bo`limlar ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".FILES." WHERE `user_id` = ? ORDER BY `id` DESC", array($ank['user_id']));
        while($folder = $sql -> fetch()) {
            echo DIV_LI . ''.icon('folder.gif').' <a href="'.HOME.'/files/'.$ank['user_id'].'/'.$folder['id'].'/">'.$folder['name'].'</a>' . CLOSE_DIV;
        }        
    }

    if ($ank['user_id'] == $user['user_id']) {
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<b>'. $lng['Yangi bo`lim'] .':</b><br /><input type="text" name="name" /><br />';
        echo ''. $lng['Turi'] .':<br /><select name="type">';
        echo '<option value="1">'. $lng['Rasm'] .'</option>';
        echo '<option value="2">'. $lng['Video'] .'</option>';
        echo '<option value="3">'. $lng['Musiqa'] .'</option>';
        echo '<option value="4">'. $lng['Java dasturlar'] .'</option>';
        echo '<option value="5">'. $lng['Android'] .'</option>';
        echo '<option value="6">'. $lng['Windows Mobile'] .'</option>';
        echo '<option value="7">'. $lng['iPhone'] .'</option>';
        echo '<option value="8">'. $lng['Bada'] .'</option>';
        echo '<option value="9">'. $lng['Flash'] .'</option>';
        echo '<option value="10">'. $lng['Mavzular'] .'</option>';
        echo '<option value="11">'. $lng['Symbian'] .'</option>';
        echo '</select><br />';
        echo '<input type="submit" name="add" value="'. $lng['Yaratish'] .'" /></form>';
        echo CLOSE_DIV; 
    }     
    break;
    
    case 'folder':
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".FILES." WHERE `id` = ? && `user_id` = ? ", array(abs(num($_GET['folder'])), $ank['user_id']));
    
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Foydalanuvchi topilmadi'] .'' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }  
    
    head('' . $ank['nick'] . ' | ' . $folder['name']);
    
    if (isset($_GET['del']) && $ank['user_id'] == $user['user_id']) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> <a href="?del&go">['. $lng['O`chirish'] .']</a> 
			<a href="'.HOME.'/files/'.$ank['user_id'].'/'.$folder['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".FILES_FILE." WHERE `folder_id` = ? ", array($folder['id']));
            while($file = $sql -> fetch()) {
                unlink('../../files/usfiles/' . $file['url']);
            }
            DB::$dbs->query("DELETE FROM ".FILES_FILE." WHERE `folder_id` = ? ", array($folder['id']));
            DB::$dbs->query("DELETE FROM ".FILES." WHERE `id` = ? ", array($folder['id']));
            header("Location: ".HOME."/files/".$ank['user_id']."/"); 
        }    
    }
    
   if (isset($_GET['edit']) && $ank['user_id'] == $user['user_id']) {

        if ($_POST['edit']) {
            $name = html($_POST['name']);
            $type = abs(num($_POST['type']));
                
            if (empty($name) || empty($type)) {
                echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".FILES." SET `name` = ?, `type` = ? WHERE `id` = ? ", array($name, $type, $folder['id']));
                header("Location: ".HOME."/files/".$ank['user_id']."/".$folder['id']."/"); 
            }
        }
            
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Bo`limni tahrirlash'] .':<br /><input type="text" value="'.$folder['name'].'" name="name" /><br />';
        
        echo ''. $lng['Turi'] .':<br /><select name="type">';
        echo '<option '.(1 == $folder['type'] ? 'selected="selected"' : NULL).' value="1">'. $lng['Rasmlar'] .'</option>';
        echo '<option '.(2 == $folder['type'] ? 'selected="selected"' : NULL).' value="2">'. $lng['Video'] .'</option>';
        echo '<option '.(3 == $folder['type'] ? 'selected="selected"' : NULL).' value="3">'. $lng['Musiqa'] .'</option>';
        echo '<option '.(4 == $folder['type'] ? 'selected="selected"' : NULL).' value="4">'. $lng['Java dasturlar'] .'</option>';
        echo '<option '.(5 == $folder['type'] ? 'selected="selected"' : NULL).' value="5">'. $lng['Android'] .'</option>';
        echo '<option '.(6 == $folder['type'] ? 'selected="selected"' : NULL).' value="6">'. $lng['Windows Mobile'] .'</option>';
        echo '<option '.(7 == $folder['type'] ? 'selected="selected"' : NULL).' value="7">'. $lng['iPhone'] .'</option>';
        echo '<option '.(8 == $folder['type'] ? 'selected="selected"' : NULL).' value="8">'. $lng['Bada'] .'</option>';
        echo '<option '.(9 == $folder['type'] ? 'selected="selected"' : NULL).' value="9">'. $lng['Flash'] .'</option>';
        echo '<option '.(10 == $folder['type'] ? 'selected="selected"' : NULL).' value="10">'. $lng['Mavzular'] .'</option>';
        echo '<option '.(11 == $folder['type'] ? 'selected="selected"' : NULL).' value="11">'. $lng['Symbian'] .'</option>';
        echo '</select><br /><br />';

        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirsh'] .'" /></form>';
        echo CLOSE_DIV;             
    }
    
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES_FILE." WHERE `folder_id` = ? && `user_id` = ? && `status` = '0' ", array($folder['id'], $ank['user_id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Fayllar kiritilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['files_file'],'folder='.$folder['id'].'&user='.$ank['user_id'].'&select=folder'); 
        $sql = DB::$dbs->query("SELECT * FROM ".FILES_FILE." WHERE `folder_id` = ? && `user_id` = ? && `status` = '0' ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['files_file']." ", array($folder['id'], $ank['user_id']));
        while($file = $sql -> fetch()) {
            echo DIV_LI . '<a href="'.HOME.'/files/'.$ank['user_id'].'/'.$folder['id'].'/'.$file['id'].'/">'.$file['name'].'</a>' . CLOSE_DIV;
        }
        echo $n->navi();          
    }    
    if ($ank['user_id'] == $user['user_id']) {
        echo DIV_AUT;
        echo '<form action="'.HOME.'/files/'.$ank['user_id'].'/'.$folder['id'].'/upload/" enctype="multipart/form-data" method="POST">';
        echo '<b>'. $lng['Fayl kiritish'] .':</b> ['.$folder['name'].']<br /><input type="file" name="file"/><br /><br />';
        echo '<b>'. $lng['Mumkun formatlar'] .':</b><br/>';
        echo type_view($folder['type']);
        
        echo '<br /><br />';
        
        if ($folder['type'] == 3) {
            echo ''. $lng['Ijrochi'] .':<br /><input type="text" name="artist" /><br /><br />';
            echo ''. $lng['Nomi'] .':<br /><input type="text" name="track" /><br /><br />';
            echo ''. $lng['Albom'] .':<br /><input type="text" name="album" /><br /><br />';
        } else {
            echo ''. $lng['Fayl nomi'] .':<br /><input type="text" name="name" /><br /><br />';
        }
        
        if ($folder['type'] > 1 && $folder['type'] != 3) {
            
            if ($folder['type'] == 4 || $folder['type'] == 5 || $folder['type'] == 6 || $folder['type'] == 7 || $folder['type'] == 8 || $folder['type'] == 11) {
                echo ''. $lng['Ta`rif'] .':<br /><textarea name="info" /></textarea><br /><br />';
                echo ''. $lng['Til'] .':<br /><input type="text" name="lang" /><br /><br />';
            }
            echo '<b>'. $lng['Skrinshotlar'] .':</b> <br />
            <input name="screen[]" type="file" multiple="true" />';
        }
        
        echo '<input type="submit" name="upload" value="'. $lng['Yuklash'] .'" />';
        echo '</form>';
        echo CLOSE_DIV;
    }
        
    if ($ank['user_id'] == $user['user_id']) {
        echo DIV_BLOCK;
        echo '<a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
        echo '<a href="?del">'. $lng['O`chirish'] .'</a><br />';
        echo CLOSE_DIV; 
    }
            
    break;
    
}


require_once('../../core/stop.php');
?>