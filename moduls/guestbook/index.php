<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

head(''. $lng['Jonli efir'] .'');
///check_auth();

if ( (isset($_GET['del']) && check($_GET['del'])) || (isset($_GET['edit']) && check($_GET['edit']))) {
    
    if ($_GET['del']) {
        $post = DB::$dbs->queryFetch("SELECT * FROM ".GUESTBOOK." WHERE `id` = ? ",array(num($_GET['del'])));

        if (!empty($post['file'])) {
            $path = '../../files/guestbook/'.$post['file'];
            unlink($path);
        }
                
        DB::$dbs->query("DELETE FROM ".GUESTBOOK." WHERE `id` = ?", array(num($_GET['del'])));
        header("Location: ".HOME."/guestbook/"); 
    }
    
    if ($_GET['edit']) {
        $post = DB::$dbs->queryFetch("SELECT * FROM ".GUESTBOOK." WHERE `id` = ? ",array(num($_GET['edit'])));
        
        if ($_POST['edit']) {
            $msg = html($_POST['msg']);
            
            if (empty($msg)) {
                $err = ''. $lng['Bo`sh habar'] .'<br />';
            }
            
            if (!empty($err)) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".GUESTBOOK." SET `msg` = ? WHERE `id` = ?", array($msg, $post['id']));
                header("Location: ".HOME."/guestbook/"); 
            }
        }
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<b>'. $lng['Tahrirlash'] .':</b><br /><textarea name="msg">'.$post['msg'].'</textarea><br />';
        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'"/>';
        echo ' <a href="/guestbook/">'. $lng['Bekor qilish'] .'</a></form> ';
        echo CLOSE_DIV;          
    }
}

if($user){
echo DIV_AUT;
if (!empty($_GET['otv'])) {
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
    if (!empty($ank) && $ank['user_id'] != $user['id']) {
        echo ' <b>' . $ank['nick'] . '</b> '. $lng['ga habar'] .'<br />';
    } else {
        echo '<b>'. $lng['Habar'] .':</b><br />';
    }
}


echo '<form action="'.(isset($_GET['otv']) ? '?otv='.(int)$_GET['otv'] : NULL).'" enctype="multipart/form-data" method="POST">';
echo '<textarea name="msg" style="width:95%;height:5pc;"></textarea><br />';
echo ''.icon('fy.png',14,16).' <b>'. $lng['Fayl biriktirish'] .':</b> [max. '.$config['max_upload_guestbook'].'mb.]<br /><input type="file" name="file"/><br />';
echo '<input type="submit" name="add" value="'. $lng['Yozish'] .'"/>';
bbsmile();
echo '</form>';
echo CLOSE_DIV;   

}else{
}

function check($id) {
    
    global $user;
    $post = DB::$dbs->queryFetch("SELECT * FROM ".GUESTBOOK." WHERE `id` = ? ",array($id));
    
    if (privilegy('guestbook_moder')) {
        return TRUE;
    }
    
    if ($post['user_id'] == $user['user_id']) {
        $sql = DB::$dbs->queryFetch("SELECT * FROM ".GUESTBOOK." WHERE `id` = ? ",array(++$id));
        if ($sql) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}


if ($_POST['add']) {
    $msg = html($_POST['msg']);

    if (!empty($_FILES['file'])) {
        $name = $_FILES['file']['name']; # Fayl nomi
        $ext = strtolower(strrchr($name, '.')); # Fayl formati
        $par = getimagesize($_FILES['file']['tmp_name']); # Fayl shakli
        $size = $_FILES['file']['size']; # Fayl hajmi
        $time = time();
        $file = $time.$ext;
        
        if ($size > (1048576 * $config['max_upload_guestbook'])) {
            $err .= ''. $lng['Foto hajmi belgilangan miqdordan oshmoqda'] .'. [Max. '.$config['max_upload_guestbook'].'Mb.]<br />';
        }
        
        if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
            $err .= ''. $lng['Mumkun bo`lmagan format'] .'.<br />';
        }
            
    }
    
    if (empty($msg)) {
        $err = ''. $lng['Bo`sh habar'] .'<br />';
    }
        
    if (!empty($_GET['otv']) && $_GET['otv'] != $user['user_id']) {
        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
        if (!empty($ank)) {
            $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
        }
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizning habaringizga javob berdi'] .' | <a href="'.HOME.'/guestbook/"><b>'. $lng['Jonli efir'] .'</b></a>';
        lenta($lenta, $ank['user_id']);
    }            
    
    if (!empty($err)) {
        echo DIV_ERROR . $err . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".USERS." SET `guestbook_post` = ? WHERE `user_id` = ? ", array((++$user['guestbook_post']), $user['user_id']));
        if ($ext) {
            copy($_FILES['file']['tmp_name'], '../../files/guestbook/'.$file);
            DB::$dbs->query("INSERT INTO ".GUESTBOOK." (`user_id`, `file`, `time`, `msg`) VALUES (?, ?, ?, ?)", array($user['user_id'], $file, time(), $msg));
        } else {
            DB::$dbs->query("INSERT INTO ".GUESTBOOK." (`user_id`, `file`, `time`, `msg`) VALUES (?, ?, ?, ?)", array($user['user_id'], 0, time(), $msg));
        }
        
        balls_operation(1);
        header("Location: ".HOME."/guestbook/"); 
    }
                             
    }

if (privilegy('guestbook_moder') && !empty($_POST['post_delete'])) {
    foreach ($_POST as $name => $value) {
        DB::$dbs->query("DELETE FROM ".GUESTBOOK." WHERE `id` = ?", array($name));
    } 
    header("Location: ".HOME."/guestbook/");        
}

if (privilegy('guestbook_moder') && !empty($_POST['clean'])) {
    $sql = DB::$dbs->query("SELECT * FROM ".GUESTBOOK."");
    while($post = $sql -> fetch()) {
        if (!empty($post['file'])) {
            $path = '../../files/guestbook/'.$post['file'];
            unlink($path);
        }
    }
    DB::$dbs->query("TRUNCATE ".GUESTBOOK.""); 
    header("Location: ".HOME."/guestbook/");     
}        
$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GUESTBOOK."");

if (empty($all)) {
    echo DIV_BLOCK . ''. $lng['Habarlar yo`q'] .'' . CLOSE_DIV;
} else {
    echo '<form action="#" method="POST">';
    $n = new Navigator($all,$config['write']['guestbook'],''); 
    $sql = DB::$dbs->query("SELECT * FROM ".GUESTBOOK." ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['guestbook']."");
    
    while($post = $sql -> fetch()) {

echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 0 ;"><center>';
echo '' . avatar($post['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius: 0 6px 0 0 ;">';
if($user){
        $ank = DB::$dbs->queryFetch("SELECT `nick` FROM ".USERS." WHERE `user_id` = ?",array($post['user_id']));
        $jesharh = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GUESTBOOK." WHERE `user_id` = ?", array($post['user_id']));
        
        echo (privilegy('guestbook_moder') ? '<input type="checkbox" name="'.$post['id'].'" /> ' : NULL);
        echo ' ' . user_choice($post['user_id'], 'link') . ($post['user_id'] != $user['user_id'] ? '
		<a href="?otv='.$post['user_id'].'"> ['. $lng['Javob berish'] .']</a> ' : NULL) . (check($post['id']) ? ' 
		<span style="font-size:12px;font-weight:bold;">(' . $jesharh . ')</span>
		<span  style="float:right;"> <a href="'.HOME.'/guestbook/?edit='.$post['id'].'">'.icon('pen2.png').'</a> <a href="'.HOME.'/guestbook/?del='.$post['id'].'">'.icon('minus2.png').'</a> </span>' : null) . ' 
		<br />';	
}else{
}
        echo '<span style="font-size:12px;color:#999;">' . vrem($post['time']) . '</span>';
		echo '</td></tr></table>';
echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-bottom: 5px;margin-left: 2px;margin-right: 2px;">';

		echo ''  . text($post['msg']) . '<br />'; 
        
        if (!empty($post['file'])) {
            
            $path = '../../files/guestbook/'.$post['file'];
            
            $size = get_size(filesize($path));
            $path_info = pathinfo($path);

            echo '<br />'. $lng['Biriktirilgan fayl'] .': <a href="'.HOME.'/files/guestbook/'.$post['file'].'"><b>['. $lng['Yuklab olish'] .']</b></a> ['.$path_info['extension'].'] ['.$size.']<br />'; 
        }
        
        echo CLOSE_DIV;  
		echo ' </div>'; 
    }
    echo $n->navi();
    echo (privilegy('guestbook_moder') ? DIV_LI . '<input type="submit" name="post_delete" value="'. $lng['Belgilangan sharhlarni o`chirish'] .'"/> <input type="submit" name="clean" value="'. $lng['Jonli efirni tozalash'] .'"/></form>' . CLOSE_DIV : NULL);
}
  
require_once('../../core/stop.php');
?>