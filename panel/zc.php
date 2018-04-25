<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Yuklamalarni boshqarish'] .''); 

if (privilegy('zc') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

if (!empty($_POST['sett'])) {
    $cat = num($_POST['cat']);
    $files = num($_POST['files']);
    $comm = num($_POST['comm']);
    
    if (empty($cat) || empty($files) || empty($comm)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_load_cat` = ?, `write_load_files` = ?, `write_load_comm` = ? ", array($cat, $files, $comm));
        header("Location: ".HOME."/panel/zc/");
    }
}


echo DIV_AUT;
echo ''. $lng['Ichki bo`limlar'] .':<br />';
echo '<form action="#" method="POST">';
echo '<select name="cat" style="width:95%;">';
echo '<option '.(5 == $config['write']['loads_cat'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['loads_cat'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['loads_cat'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['loads_cat'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['loads_cat'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['loads_cat'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi fayllar'] .':<br />';
echo '<select name="files" style="width:95%;">';
echo '<option '.(5 == $config['write']['loads_file'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['loads_file'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['loads_file'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['loads_file'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['loads_file'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['loads_file'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi sharhlar'] .':<br />';
echo '<select name="comm" style="width:95%;">';
echo '<option '.(5 == $config['write']['loads_comm'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['loads_comm'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['loads_comm'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['loads_comm'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['loads_comm'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['loads_comm'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';
echo '<input type="submit" name="sett" value="'. $lng['O`zgartirish'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>