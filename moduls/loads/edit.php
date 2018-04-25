<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');
require_once('../../core/class/id.php'); 
check_auth();

$folder = DB::$dbs->queryFetch("SELECT * FROM ".LOADS." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
if (empty($folder)) {
    head(''. $lng['Bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 
    
$folderc = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_CAT." WHERE `id` = ? ", array(abs(num($_GET['folderc']))));
if (empty($folderc)) {
    head(''. $lng['Ichki bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}    

$file = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_FILE." WHERE `id` = ? ", array(abs(num($_GET['file']))));
if (empty($file)) {
    head(''. $lng['Fayl topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}  

if (privilegy('zc') == FALSE) {
    head(''. $lng['Kirishda xatolik'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit();     
}

switch ($select) {
    
    case 'screen':
    head('' . $file['name'] . ' '. $lng['skrinshotlarini boshqarish'] .'');
    
    if (!empty($_GET['del'])) {
        $scr = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_SCREEN." WHERE `id` = ? ", array(abs(num($_GET['del']))));
        unlink(HOME . '/files/loads/screen/'.$scr['url']);
        DB::$dbs->query("DELETE FROM ".LOADS_SCREEN." WHERE `id` = ? ", array(abs(num($_GET['del']))));
        header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$file['id']."/edit/screen/");
    }
    
    if (!empty($_POST['upload'])) {
        if (isset($_FILES['screen']['name']) && $folder['type'] > 3) {
            foreach ($_FILES['screen']['name'] as $k=>$v) {
                
                $name = $_FILES['screen']['name'][$k];
                $ext = strtolower(strrchr($name, '.')); # Fayl formati
                
                if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, type(1))) {
                    
                } else {
                    $name_screen = md5(time() . rand(1,100)).$ext;
                    copy($_FILES['screen']['tmp_name'][$k], '../../files/loads/screen/'.$name_screen);
                    DB::$dbs->query("INSERT INTO ".LOADS_SCREEN." (`file_id`, `url`) VALUES (?,?)", array($file['id'], $name_screen));  
                }
            }
        }
        header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$file['id']."/edit/screen/");        
    }
     
    
    echo DIV_BLOCK;
    $screens = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
    
    if (!empty($screens)) {
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
        while($screen = $sql -> fetch()) {
            echo '<a href="'.HOME.'/files/loads/screen/'.$screen['url'].'"><img src="'.HOME.'/files/loads/screen/'.$screen['url'].'" wight="80" height="80" /></a> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/screen/?del='.$screen['id'].'">[x]</a><br />';
        }
    } else {
        echo ''. $lng['Skrinshotlar hali joylanmagan'] .'';
    }
    echo CLOSE_DIV; 
    
    echo DIV_AUT;
    echo '<form action="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/screen/" enctype="multipart/form-data" method="POST">';
    echo '<b>'. $lng['Screen kiritish'] .':</b> ['. $lng['Multitanlash'] .']<br /><input name="screen[]" type="file" multiple="true" /><br />';
    echo '<input type="submit" name="upload" value="'. $lng['Yuklash'] .'" />';  
    echo '</form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/"><b>'.$file['name'].'</b></a><br/>'; 
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';  
	break;
    
    case 'edit':
    head(''. $lng['Tahrirlash'] .': ' . $file['name']);
    
    if (!empty($_POST['edit'])) {
        $name = html($_POST['name']);
        $info = html($_POST['info']);
        $lang = html($_POST['lang']);
        
        if (empty($name)) {
            echo DIV_ERROR . ''. $lng['Nomni to`ldiring'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("UPDATE ".LOADS_FILE." SET `name` = ?, `lang` = ?, `info` = ? WHERE `id` = ? ", array($name, $lang, $info, $file['id']));
            echo DIV_MSG . ''. $lng['O`zgaririshlar qabul qilindi'] .'' . CLOSE_DIV;
        }
    }
     
    
    echo DIV_AUT; 
    echo '<form action="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/edit/" enctype="multipart/form-data" method="POST">';
    echo ''. $lng['Fayl nomi'] .':<br /><input type="text" name="name" value="'.$file['name'].'" style="width:96%;"/><br /><br />';
    echo ''. $lng['Ta`rif'] .':<br /><textarea name="info" style="width:96%;height:7pc;"/>'.$file['info'].'</textarea><br /><br />';
    echo ''. $lng['Til'] .':<br /><input type="text" name="lang" value="'.$file['lang'].'"  style="width:96%;"/><br /><br />';    
    echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" />';  
    echo '</form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/"><b>'.$file['name'].'</b></a><br/>'; 
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';  
	break;
    
    case 'ver':
    head(''. $lng['Fayl versiyalarini boshqarish'] .': ' . $file['name']);  

    if (!empty($_GET['del'])) {
        $file_dop = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_FILE_DOP." WHERE `id` = ? ", array(abs(num($_GET['del']))));
        unlink(HOME . '/files/loads/files/'.$file_dop['url']);
        DB::$dbs->query("DELETE FROM ".LOADS_FILE_DOP." WHERE `id` = ? ", array(abs(num($_GET['del']))));
        header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$file_dop['id']."/edit/ver/");
    }
    
    if (!empty($_GET['edit'])) {

        $file_dop = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_FILE_DOP." WHERE `id` = ? ", array(abs(num($_GET['edit']))));
                
        if (!empty($_POST['edit'])) {
            $name = html($_POST['name']);
            $lang = html($_POST['lang']);
            
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Fayl nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".LOADS_FILE_DOP." SET `name` = ?, `lang` = ? WHERE `id` = ? ", array($name, $lang, $file_dop['id']));
                echo DIV_MSG . ''. $lng['O`zgartirishlar qabul qilindi'] .'' . CLOSE_DIV;                
            }
        }
        
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Fayl nomi'] .':<br /><input type="text" name="name" value="'.$file_dop['name'].'" style="width:96%;"/><br /><br />';
        echo ''. $lng['Til'] .':<br /><input type="text" name="lang" value="'.$file_dop['lang'].'" style="width:96%;"/><br /><br />';    
        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
        echo CLOSE_DIV;   
    }
    
    if (!empty($_POST['upload'])) {
        
        $file_name = html($_POST['name']);
        $file_lang = html($_POST['lang']);
        
        if (!empty($_FILES['file'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            $size = $_FILES['file']['size']; # Fayl hajmi
            $file1 = time().$ext;
            
            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
                $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
            }
            
            if (empty($file_name)) {
                $file_name = html($_FILES['file']['name']);
            }
            
            if (empty($err)) {
                copy($_FILES['file']['tmp_name'], '../../files/loads/files/'.$file1);
                DB::$dbs->query("INSERT INTO ".LOADS_FILE_DOP." (`folder_id`, `folderc_id`, `file_id`, `name`, `url`, `time`, `size`, `lang`, `type`) VALUES 
                (?,?,?,?,?,?,?,?,?)", array($folder['id'], $folderc['id'], $file['id'], $file_name, $file1, time(), $size, $file_lang, $ext));  
                header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$file['id']."/edit/ver/");
            } else {
                echo $err;
            }
        }
                
    }
    
    echo DIV_BLOCK;
    $files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE_DOP." WHERE `file_id` = ? ", array($file['id']));
    
    if (!empty($files)) {
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE_DOP." WHERE `file_id` = ? ", array($file['id']));
        while($dop = $sql -> fetch()) {
            echo '<a href="'.HOME.'/files/loads/files/'.$dop['url'].'">'.$dop['name'].'</a> ['. $lng['Til'] .': '.$dop['lang'].' / '. $lng['Hajmi'] .': '.get_size($dop['size']).' 
			/ '.$dop['type'].'] <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/ver/?del='.$dop['id'].'">[x]</a> 
			<a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/ver/?edit='.$dop['id'].'">[edit]</a><br />';
        }
    } else {
        echo ''. $lng['Qo`shimcha fayllar kiritilmagan'] .'';
    }
    echo CLOSE_DIV; 
    
    echo DIV_AUT;
    echo '<form action="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/ver/" enctype="multipart/form-data" method="POST">';

    echo ''. $lng['Fayl nomi'] .':<br /><input type="text" name="name" style="width:96%;"/><br />';
    echo ''. $lng['Til'] .':<br /><input type="text" name="lang" style="width:96%;"/><br />';
    echo ''. $lng['Fayl'] .':<br /><input name="file" type="file" /><br />';
    echo '<input type="submit" name="upload" value="'. $lng['Joylash'] .'" />';  
    echo '</form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/"><b>'.$file['name'].'</b></a><br/>'; 
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';  
	break;
    
    case 'delete':
    unlink(HOME . '/files/loads/files/'.$file['url']);
    
    $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE_DOP." WHERE `file_id` = ? ", array($file['id']));
    while($dop = $sql -> fetch()) {
        unlink('../../files/loads/files/'.$dop['url']);
    }
        
    DB::$dbs->query("DELETE FROM ".LOADS_FILE_DOP." WHERE `file_id` = ? ", array($file['id']));
    DB::$dbs->query("DELETE FROM ".LOADS_RATING." WHERE `file_id` = ? ", array($file['id'])); 
    DB::$dbs->query("DELETE FROM ".LOADS_COMM." WHERE `file_id` = ? ", array($file['id'])); 
    DB::$dbs->query("DELETE FROM ".LOADS_FILE." WHERE `id` = ? ", array($file['id']));  
    
    header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/");  
    break;
    
}    

require_once('../../core/stop.php');
?>