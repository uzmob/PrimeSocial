<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Mehmonxonani boshqarish'] .''); 

if (privilegy('guestbook') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GUESTBOOK." ");

if (isset($_GET['clean'])) {
    DB::$dbs->query("TRUNCATE ".GUESTBOOK."");
    header("Location: ".HOME."/panel/guestbook/");    
}

if (!empty($_POST['str'])) {
    $str = num($_POST['str']);
    $upload = num($_POST['upload']);
    
    if (empty($str) || empty($upload)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_guestbook` = ?, `max_upload_guestbook` = ? ", array($str, $upload));
        header("Location: ".HOME."/panel/guestbook/");
    }
}


echo DIV_AUT;
echo ''. $lng['Sahifadagi habarlar soni'] .':<br />';
echo '<form action="#" method="POST">';
echo '<select name="str" style="width:95%;">';
echo '<option '.(5 == $config['write']['guestbook'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['guestbook'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['guestbook'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['guestbook'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['guestbook'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['guestbook'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';

echo ''. $lng['Fayl kiritish uchun max. hajm'] .':<br />';
echo '<select name="upload" style="width:95%;">';
echo '<option '.(1 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="1">1</option>';
echo '<option '.(2 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="2">2</option>';
echo '<option '.(3 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(20 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(50 == $config['max_upload_guestbook'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';

echo '<input type="submit" name="sett" value="'. $lng['Saqlash'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>