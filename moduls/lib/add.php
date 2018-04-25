<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$folder = abs(num($_GET['folder']));
$folderc = abs(num($_GET['folderc']));
$text = html($_POST['text']);
$info = html($_POST['info']);

if (!empty($_POST) && privilegy('lib') == TRUE) {
    
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".LIB." WHERE `id` = ? ", array($folder));
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
		echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
		require_once('../../core/stop.php');
        exit(); 
    }
    
    $folderc = DB::$dbs->queryFetch("SELECT * FROM ".LIB_CAT." WHERE `id` = ? ", array($folderc));
    if (empty($folderc)) {
        head(''. $lng['Ichki bo`lim topilmadi'] .'');
		echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
		require_once('../../core/stop.php');
        exit(); 
    } 
    
    if (!empty($_FILES['file']['tmp_name'])) {
        $name = $_FILES['file']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name, '.')); # Fayl formati
        $par = getimagesize($_FILES['file']['tmp_name']); # Rasm hajmi
        $size = $_FILES['file']['size']; # Fayl hajmi
        $time = time();
        $file = $time.$ext;
        $whitelist = array('.txt'); # Mumkun bo`lgan format
        
        if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $whitelist)) {
            $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
        } 
        
        if (empty($err)) {
            copy($_FILES['file']['tmp_name'], 'temp/'.$file);
            $text = file_get_contents('temp/'.$file);
        }
    }

    if (!empty($_FILES['screen']['tmp_name'])) {
        $name_screen = $_FILES['screen']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name_screen, '.')); # Fayl formati
        $size = $_FILES['screen']['size']; # Fayl hajmi
        $time = time();
        $screen = $time.$ext;
        $whitelist = array('.jpg', '.jpeg', '.png', '.gif'); # Mumkun bo`lgan format
        
        if (preg_match('/.php/i', $name_screen) || preg_match('/.pl/i', $name_screen) || $name_screen == '.htaccess' || !in_array($ext, $whitelist)) {
            $err .= ''. $lng['Skrinshot shaklida xatolik'] .'.<br />';
        } 
        
        if (empty($err)) {
            copy($_FILES['screen']['tmp_name'], '../../files/lib/screen/'.$screen);
            img_resize('../../files/lib/screen/'.$screen, '../../files/lib/screen/mini_'.$screen, $config['mini_lib_par'][0], $config['mini_lib_par'][1]); # Mini
        }
    } 
    
    $name = html($_POST['name']);
       
    if (empty($text)) {
        $err .= ''. $lng['Maqola matni yozilmagan'] .'<br />';
    }
    
    if (empty($name)) {
        $err .= ''. $lng['Maqola sarlavhasi yozilmagan'] .'<br />';
    }
    
    if (mb_check_encoding($text, 'UTF-8')) {
    
    } elseif (mb_check_encoding($text, 'windows-1251')) {
        $text = iconv("windows-1251", "UTF-8", $text);
    } elseif (mb_check_encoding($text, 'KOI8-R')) {
        $text = iconv("KOI8-R", "UTF-8", $text);
    } else {
        $err .= ''. $lng['Matn kodirovkasida xatolik'] .'<br />';
    }
    
    if (empty($screen)) {
        $screen = '';
    }
    
    $file = time();
    $files = fopen("../../files/lib/text/".$file.".txt", 'w+');
    flock($files, LOCK_EX);
    fputs($files, $text);
    flock($files, LOCK_UN);
    fclose($files);
                
    if (empty($err)) {
        DB::$dbs->query("INSERT INTO ".LIB_ARTICL." (`folder_id`, `folderc_id`, `title`, `text`, `info`, `screen`, `time`) VALUES 
        (?,?,?,?,?,?,?)", array($folder['id'], $folderc['id'], $name, $file.'.txt', $info, $screen, time()));    
        header("Location: ".HOME."/lib/".$folder['id']."/".$folderc['id']."/");
    } else {
        
        head(''. $lng['Xatolik'] .'!');
        echo DIV_BLOCK . $err . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
        
    }
}


?>