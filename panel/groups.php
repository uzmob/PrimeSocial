<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Guruhlarni boshqarish'] .''); 

if (privilegy('group') == FALSE) {
    header("Location: ".HOME."/panel/");
    exit();
}

if (!empty($_POST['sett'])) {
    $group = num($_POST['group_str']);
    $topic = num($_POST['topic_str']);
    $msg = num($_POST['topic_msg']);
    
    if (empty($group) || empty($topic) || empty($msg)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_group` = ?, `write_group_topic` = ?, `write_group_msg` = ? ", array($group, $topic, $msg));
        header("Location: ".HOME."/panel/groups/");
    }
}

echo DIV_AUT;
echo '<form action="#" method="POST">';

echo ''. $lng['Sahifadagi guruhlar'] .':<br />';
echo '<select name="group_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['groups'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['groups'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['groups'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['groups'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['groups'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['groups'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo ''. $lng['Muhokama bo`limidagi mavzular'] .':<br />';
echo '<select name="topic_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['groups_topic_msg'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['groups_topic_msg'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['groups_topic_msg'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['groups_topic_msg'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['groups_topic_msg'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['groups_topic_msg'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo ''. $lng['Muhokama bo`limidagi sharhlar'] .':<br />';
echo '<select name="topic_msg" style="width:95%;">';
echo '<option '.(3 == $config['write']['groups_topic'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['groups_topic'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['groups_topic'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['groups_topic'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['groups_topic'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['groups_topic'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo '<input type="submit" name="sett" value="'. $lng['O`zgartirish'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>