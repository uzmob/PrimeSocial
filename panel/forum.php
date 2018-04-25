<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Forumni boshqarish'] .'');

if (privilegy('forum') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

if (!empty($_POST['sett'])) {
    $razd = num($_POST['razd_str']);
    $theme = num($_POST['theme_str']);
    $post = num($_POST['post_str']);
    
    if (empty($razd) || empty($theme) || empty($post)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_forum_post` = ?, `write_forum_theme` = ?, `write_forum_razd` = ? ", array($post, $theme, $razd));
        header("Location: ".HOME."/panel/forum/");
    }
}

echo DIV_AUT;
echo '<form action="#" method="POST">';

echo ''. $lng['Sahifadagi bo`limlar'] .':<br />';
echo '<select name="razd_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['forum_razd'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['forum_razd'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['forum_razd'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['forum_razd'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['forum_razd'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['forum_razd'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi mavzular'] .':<br />';
echo '<select name="theme_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['forum_theme'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['forum_theme'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['forum_theme'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['forum_theme'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['forum_theme'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['forum_theme'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi sharhlar'] .':<br />';
echo '<select name="post_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['forum_post'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['forum_post'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['forum_post'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['forum_post'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['forum_post'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['forum_post'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo '<input type="submit" name="sett" value="'. $lng['O`zgartirish'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>