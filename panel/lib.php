<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Kutubxonani boshqarish'] .''); 

if (privilegy('lib') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

if (!empty($_POST['sett'])) {
    $cat = num($_POST['cat']);
    $articl = num($_POST['articl']);
    $simvol = num($_POST['simvol']);
    $comm = num($_POST['comm']);
    
    if (empty($cat) || empty($articl) || empty($simvol) || empty($comm)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_lib_cat` = ?, `write_lib_pod_cat` = ?, `write_lib_simvol` = ?, `write_lib_comm` = ? ", array($cat, $arcticl, $simvol, $comm));
        header("Location: ".HOME."/panel/lib/");
    }
}


echo DIV_AUT;
echo ''. $lng['Ichki bo`limlar'] .':<br />';
echo '<form action="#" method="POST">';
echo '<select name="cat" style="width:95%;">';
echo '<option '.(5 == $config['write']['lib_cat'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['lib_cat'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['lib_cat'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['lib_cat'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['lib_cat'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['lib_cat'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi kitoblar'] .':<br />';
echo '<select name="articl" style="width:95%;">';
echo '<option '.(5 == $config['write']['lib_articl'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['lib_articl'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['lib_articl'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['lib_articl'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['lib_articl'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['lib_articl'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi belgilar'] .':<br />';
echo '<select name="simvol" style="width:95%;">';
echo '<option '.(100 == $config['write']['lib_articl_str'] ? 'selected="selected"' : NULL).' value="100">100</option>';
echo '<option '.(500 == $config['write']['lib_articl_str'] ? 'selected="selected"' : NULL).' value="500">500</option>';
echo '<option '.(750 == $config['write']['lib_articl_str'] ? 'selected="selected"' : NULL).' value="750">750</option>';
echo '<option '.(1000 == $config['write']['lib_articl_str'] ? 'selected="selected"' : NULL).' value="1000">1000</option>';
echo '<option '.(2000 == $config['write']['lib_articl_str'] ? 'selected="selected"' : NULL).' value="2000">2000</option>';
echo '<option '.(5000 == $config['write']['lib_articl_str'] ? 'selected="selected"' : NULL).' value="5000">5000</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi sharhlar'] .':<br />';
echo '<select name="comm" style="width:95%;">';
echo '<option '.(5 == $config['write']['lib_comm'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(10 == $config['write']['lib_comm'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['lib_comm'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['lib_comm'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '<option '.(30 == $config['write']['lib_comm'] ? 'selected="selected"' : NULL).' value="30">30</option>';
echo '<option '.(50 == $config['write']['lib_comm'] ? 'selected="selected"' : NULL).' value="50">50</option>';
echo '</select><br />';
echo '<input type="submit" name="sett" value="'. $lng['O`zgartirish'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>