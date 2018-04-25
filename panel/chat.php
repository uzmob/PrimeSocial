<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Chatni boshqarish'] .''); 

if (privilegy('chat') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".CHAT_MSG." ");

if (isset($_GET['clean'])) {
    DB::$dbs->query("TRUNCATE ".CHAT_MSG."");
    header("Location: ".HOME."/panel/chat/");    
}

if (!empty($_GET['cleanroom'])) {
    DB::$dbs->query("DELETE FROM ".CHAT_MSG." WHERE `room_id` = ? ", array(num($_GET['cleanroom'])));
    header("Location: ".HOME."/panel/chat/");    
}

if (!empty($_GET['delroom'])) {
    if (!isset($_GET['go'])) {
        echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
		<a href="'.HOME.'/panel/chat/?delroom='.(int)$_GET['delroom'].'&go">['. $lng['O`chirish'] .']</a> 
		<a href="'.HOME.'/panel/chat/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
    } else {
        DB::$dbs->query("DELETE FROM ".CHAT_MSG." WHERE `room_id` = ? ", array(num($_GET['delroom'])));
        DB::$dbs->query("DELETE FROM ".CHAT_ROOM." WHERE `id` = ? ", array(num($_GET['delroom'])));
        header("Location: ".HOME."/panel/chat/");  
    }
}

if (!empty($_POST['str'])) {
    $str = num($_POST['str']);
    
    if (empty($str)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_room` = ? ", array($str));
        header("Location: ".HOME."/panel/chat/");
    }
}

if (!empty($_GET['editroom'])) {
    if ($_POST['edit']) {
        $room = html($_POST['room']);
        $type = num($_POST['type']);
                
        if (empty($room)) {
            echo DIV_ERROR . ''. $lng['Xona nomi ko`rsatilmadi'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("UPDATE ".CHAT_ROOM." SET `name` = ?, `type` = ? WHERE `id` = ?", array($room, $type, num($_GET['editroom'])));
            header("Location: ".HOME."/panel/chat/"); 
        }
    }
    
    $room = DB::$dbs->queryFetch("SELECT `id`, `name`, `type`, `icon` FROM ".CHAT_ROOM." WHERE `id` = ?",array(num($_GET['editroom'])));
    /* Ikonkani o`chirish */
    if (isset($_GET['icon_delete'])) {
        @unlink('../files/icons_chat/' . $room['icon']);
        DB::$dbs->query("UPDATE ".CHAT_ROOM." SET `icon` = ? WHERE `id` = ?", array('', num($_GET['editroom'])));
        header("Location: ".HOME."/panel/chat/?editroom=".$room['id']); 
    }
    echo DIV_AUT;
    echo '<form action="#" method="POST" enctype="multipart/form-data">';
    echo ''. $lng['Nomi'] .':<br /><input type="text" value="'.$room['name'].'" name="room" /><br />';
            
    echo '<select name="type">';
    echo '<option '.(0 == $room['type'] ? 'selected="selected"' : NULL).' value="0">'. $lng['Oddiy'] .'</option>';
    echo '<option '.(1 == $room['type'] ? 'selected="selected"' : NULL).' value="1">'. $lng['Bilimdon boti bilan'] .'</option>';
    echo '<option '.(2 == $room['type'] ? 'selected="selected"' : NULL).' value="2">'. $lng['Xazilkash boti bilan'] .'</option>';
    echo '</select>';
    
    if ($room['icon']) {
        echo '<b>'. $lng['Rasmcha'] .':</b><br /><img src="'.HOME.'/files/icons_chat/'.$room['icon'].'" alt="[icon]" /> 
		<a href="'.HOME.'/panel/chat/?editroom='.$room['id'].'&icon_delete">[x]</a><br />';
    }
    echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
    echo CLOSE_DIV;   
}

echo '<div class="grey">'. $lng['Chatdagi xabarlar soni'] .': <b>' . $all . '</b> 
<a href="?clean">['. $lng['Tozalash'] .']</a>' . CLOSE_DIV;

$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".CHAT_ROOM."");

if (empty($all)) {
    echo DIV_BLOCK . ''. $lng['Xonalar ochilmagan'] .'' . CLOSE_DIV;
} else {
    $sql = DB::$dbs->query("SELECT * FROM ".CHAT_ROOM." ORDER BY `id` DESC");
    while($room = $sql -> fetch()) {
        echo DIV_BLOCK;
        echo ($room['icon'] ? '<img src="'.HOME.'/files/icons_chat/'.$room['icon'].'" alt="[icon]" /> ' : NULL) . '<a href="'.HOME.'/chat/'.$room['id'].'/">'.$room['name'].'</a><br />';
        $allmsg = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".CHAT_MSG." WHERE `room_id` = ? ", array($room['id']));
        echo ''. $lng['Xabarlar'] .': <b>' . $allmsg . '</b> <a href="?cleanroom='.$room['id'].'">['. $lng['Tozalash'] .']</a> 
		<a href="?delroom='.$room['id'].'">['. $lng['O`chr'] .'.]</a> <a href="?editroom='.$room['id'].'">['. $lng['O`zg'] .'.]</a><br />';
        echo CLOSE_DIV;
    }
}   

if ($_POST['add']) {
    $room = html($_POST['room']);
    $type = num($_POST['type']);
            
    if (empty($room)) {
        echo DIV_ERROR . ''. $lng['Xona nomi ko`rsatilmadi'] .'' . CLOSE_DIV;
    } else {
        if (!empty($_FILES['file'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
            $icon_uri = time().$ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                
            if ($par[0] > 216 || $par[1] > 216) {
                $err .= ''. $lng['Foto hajmi belgilangan miqdordan ortmoqda'] .'. [Max. 16x16]<br />';
            }
                
            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= ''. $lng['Fayl formati noto`g`ri'] .'.<br />';
            }
                
            if (empty($err)) {
                copy($_FILES['file']['tmp_name'], '../files/icons_chat/'.$icon_uri);
            } else {
                echo $err;
            }
        }
        $icon_uri = ($icon_uri ? $icon_uri : '');
            
        DB::$dbs->query("INSERT INTO ".CHAT_ROOM." (`name`, `type`, `icon`) VALUES (?, ?, ?)", array($room, $type, $icon_uri));
        header("Location: ".HOME."/panel/chat/"); 
    }
}
        
echo '<div class="grey">';
echo '<form action="#" method="POST" enctype="multipart/form-data">';
echo '<b>'. $lng['Yangi xona'] .'</b>:<br />
<input type="text" name="room" style="width:95%;"/><br />';
        
echo '<select name="type" style="width:95%;">';
echo '<option value="0">'. $lng['Oddiy'] .'</option>';
echo '<option value="1">'. $lng['Bilimdon boti bilan'] .'</option>';
echo '<option value="2">'. $lng['Xazilkash boti bilan'] .'</option>';
echo '</select>
'. $lng['Rasmcha'] .' [16x16, jpg|jpeg|gif|png]:<br/>
<input type="file" name="file" style="width:95%;"/><br/>';
echo '<input type="submit" name="add" value="'. $lng['Yaratish'] .'" /></form><br/>';

echo ''. $lng['Xonadagi xabarlar soni'] .':<br />';
echo '<form action="#" method="POST">';
echo '<select name="str" style="width:95%;">';
echo '<option '.(5 == $config['write']['room'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['room'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['room'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['room'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['room'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['room'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select>';
echo '<input type="submit" name="sett" value="'. $lng['O`zgartirish'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>