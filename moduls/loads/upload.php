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

/* **** */    
head(''. $lng['Fayl kiritish'] .''); 

if (!empty($_POST['upload'])) {
    
    $new_name = html($_POST['name']);
    $info = html($_POST['info']);
    $lang = html($_POST['lang']);
    
    /* Fayl kiritish */
    /*if (isset($_FILES['file']['name'])) {
        echo translit($_FILES['file']['name']) . '<br /><br />'; 
    }*/
    
    $whitelist = type($folder['type']);
    $name = html($_FILES['file']['name']);
    $ext = strtolower(strrchr($name, '.')); # Fayl formati
    $size = $_FILES['file']['size'];
    
    if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $whitelist)) {
        echo ''. $lng['Fayl shaklida xatolik'] .'.<br />';
        require_once('../../core/stop.php');
        exit();
    }
    
    /* Fayl nomi */
    if (!empty($new_name)) {
        $view_name = $new_name;
    } else {
        $view_name = html($name);
    }
    
    /* Musiqa bo`lsa */
    if ($folder['type'] == 3) {
        $artist = html($_POST['artist']);
        $track = html($_POST['track']);
        $album = html($_POST['album']);
    
        if (empty($artist)) {
            $err .= ''. $lng['Maydonchani to`ldiring'] .' "'. $lng['Ijrochi'] .'"<br />';
        }
        
        if (empty($track)) {
            $err .= ''. $lng['Maydonchani to`ldiring'] .' "'. $lng['Nomi'] .'"<br />';
        }
    }
    
    /* Fayl nomini generatsiya qilish */
    if ($folder['type'] == 1) {
        $name_file = SITE . '_' . time().$ext;
    } else {
        $name_file = time().$ext;
    }
    
    
    if (empty($info)) {
        $info = '';
    }
    if (empty($lang)) {
        $lang = '';
    }
    if (empty($artist)) {
        $artist = '';
    }
    if (empty($track)) {
        $track = '';
    }
    if (empty($album)) {
        $album = '';
    }
    
    if (!empty($err)) {
        echo DIV_BLOCK . $err . CLOSE_DIV;
    } else {
        copy($_FILES['file']['tmp_name'], '../../files/loads/files/'.$name_file);
         
        /* Ma`lumotlar bazasiga yozish */
        DB::$dbs->query("INSERT INTO ".LOADS_FILE." (`folder_id`, `folderc_id`, `name`, `url`, `time`, `size`, `type`, `info`, `lang`, `artist`, `track`, `album`, `loads`, `rating`, `user_id`) VALUES 
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array($folder['id'], $folderc['id'], $view_name, $name_file, time(), $size, $ext, $info, $lang, $artist, $track, $album, 0, 0, $user['user_id']));  


    $lastid = DB::$dbs->lastInsertId();
    
    if ($folder['type'] == 1) {
        img_resize('../../files/loads/files/'.$name_file, '../../files/loads/files/mini_'.$name_file, 100, 100);    
    }   
         
    /* Skrinshotlar kiritish */    
    if (isset($_FILES['screen']['name'])) {
        foreach ($_FILES['screen']['name'] as $k=>$v) {
            
            $name = $_FILES['screen']['name'][$k];
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            
            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, type(1))) {
                
            } else {
                $name_screen = md5(time() . rand(1,100)).$ext;
                copy($_FILES['screen']['tmp_name'][$k], '../../files/loads/screen/'.$name_screen);
                DB::$dbs->query("INSERT INTO ".LOADS_SCREEN." (`file_id`, `url`) VALUES (?,?)", array($lastid, $name_screen));  
            }
        }
    }
    
    /* Videodan skrinshot yaratish */
    /*
    if ($folder['type'] == 2) {
        
        $location = 'screen/'.mt_rand(1000,9999).'.jpg';

        if(!file_exists($location)){ // Agar skrinshot yaratilmagan bo`lsa, uni yaratamiz
                $mov = new ffmpeg_movie('../../files/loads/files/' . $name_file, false);
                
                $wn = $mov->GetFrameWidth();
                $hn = $mov->GetFrameHeight();
                
                // считаем кол-во кадров
                $all_frames = $mov->getFrameCount();
                // номер кадра
                $frame1 = $mov->getFrame(mt_rand(10,$all_frames));
                $frame2 = $mov->getFrame(mt_rand(10,$all_frames));
                $frame3 = $mov->getFrame(mt_rand(10,$all_frames));
                
                $gd1 = $frame1->toGDImage();
                $gd2 = $frame2->toGDImage();
                $gd3 = $frame3->toGDImage();
                
                $W = $wn; // Rasm eni
                $H = $hn; // Rasm bo`yi
                
                
                $new1 = imageCreateTrueColor($W, $H);
                imageCopyResampled($new1, $gd1, 0, 0, 0, 0, $W, $H, $wn, $hn);
                imageJPEG($new1, $location); // $location yo`li bilan saqlaymiz
                
                $new2 = imageCreateTrueColor($W, $H);
                imageCopyResampled($new2, $gd2, 0, 0, 0, 0, $W, $H, $wn, $hn);
                imageJPEG($new2, $location); // $location yo`li bilan saqlaymiz
                
                $new3 = imageCreateTrueColor($W, $H);
                imageCopyResampled($new3, $gd3, 0, 0, 0, 0, $W, $H, $wn, $hn);
                imageJPEG($new3, $location); // $location yo`li bilan saqlaymiz
            }
        }
    */
    header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$lastid."/");    
    }

}

/* *** */

require_once('../../core/stop.php');
?>