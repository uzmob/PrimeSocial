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

$id = abs(num($_GET['user']));
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));

if (empty($ank)) {
    head(''. $lng['Foydalanuvchi topilmadi'] .''); 
    echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
    exit();
}

$folder = DB::$dbs->queryFetch("SELECT * FROM ".FILES." WHERE `id` = ? && `user_id` = ? ", array(abs(num($_GET['folder'])), $ank['user_id']));
    if (empty($folder)) {
    head(''. $lng['Bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 

/* **** */    
head(''. $lng['Fayl kiritish'] .''); 

if (!empty($_POST['upload'])) {
    
    $new_name = html($_POST['name']);

    $whitelist = type($folder['type']);
    $name = html($_FILES['file']['name']);
    $ext = strtolower(strrchr($name, '.')); # Fayl formati
    $size = $_FILES['file']['size'];
    
    if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $whitelist)) {
        echo ''. $lng['Fayl formati noto`g`ri'] .'.<br />';
        require_once('../../core/stop.php');
        exit();
    }
    
    /* Faylning ko`rinishdagi nomi */
    if (!empty($new_name)) {
        $view_name = $new_name;
    } else {
        $view_name = html($name);
    }

    /* Fayl nomini generatsiya qilish */
    if ($folder['type'] == 1) {
        $name_file = SITE . '_' . time().$ext;
    } else {
        $name_file = time().$ext;
    }
    
    if (!empty($err)) {
        echo DIV_BLOCK . $err . CLOSE_DIV;
    } else {
        copy($_FILES['file']['tmp_name'], '../../files/usfiles/'.$name_file);
         
        /* Ma`lumotlar bazasiga yozish */
        DB::$dbs->query("INSERT INTO ".FILES_FILE." (`folder_id`, `user_id`, `name`, `url`, `time`, `size`, `type`, `loads`, `status`) VALUES 
        (?,?,?,?,?,?,?,?,?)", array($folder['id'], $ank['user_id'], $view_name, $name_file, time(), $size, $ext, 0, 1));  
        balls_operation(10);

    #$lastid = DB::$dbs->lastInsertId();
    #header("Location: ".HOME."/files/".$ank['user_id']."/".$folder['id']."/".$lastid."/"); 
    
    echo DIV_BLOCK . ''. $lng['Fayl joylandi. Fayl faqat ma`muriyat tekshiruvidan so`ng shahsiy fayllaringizda ko`rsatiladi'] .'.' . CLOSE_DIV;   
    }

}

/* *** */

require_once('../../core/stop.php');
?>