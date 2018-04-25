<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Blogni boshqarish'] .''); 

if (privilegy('blog') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

if (!empty($_POST['sett'])) {
    $blog = num($_POST['blog_str']);
    $blog_comm = num($_POST['blog_comm_str']);
    $blog_comm_view = num($_POST['blog_comm_view_str']);
    
    if (empty($blog) || empty($blog_comm) || empty($blog_comm_view)) {
        echo DIV_ERROR . ''. $lng['Xech nima tanlanmagan'] .'' . CLOSE_DIV;
    } else {
        DB::$dbs->query("UPDATE ".CONFIG." SET `write_blog` = ?, `write_blog_comm` = ?, `write_blog_comm_view` = ? ", 
        array($blog, $blog_comm, $blog_comm_view));
        header("Location: ".HOME."/panel/blog/");
    }
}

echo DIV_AUT;
echo '<form action="#" method="POST">';

echo ''. $lng['Sahifadagi bloglar'] .':<br />';
echo '<select name="blog_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['blog'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['blog'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['blog'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['blog'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['blog'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['blog'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo ''. $lng['Sahifadagi sharhlar'] .':<br />';
echo '<select name="blog_comm_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['blog_comm'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['blog_comm'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['blog_comm'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['blog_comm'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['blog_comm'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['blog_comm'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo ''. $lng['So`ngi sharhlar soni'] .':<br />';
echo '<select name="blog_comm_view_str" style="width:95%;">';
echo '<option '.(3 == $config['write']['blog_comm_view'] ? 'selected="selected"' : NULL).' value="3">3</option>';
echo '<option '.(5 == $config['write']['blog_comm_view'] ? 'selected="selected"' : NULL).' value="5">5</option>';
echo '<option '.(8 == $config['write']['blog_comm_view'] ? 'selected="selected"' : NULL).' value="8">8</option>';
echo '<option '.(10 == $config['write']['blog_comm_view'] ? 'selected="selected"' : NULL).' value="10">10</option>';
echo '<option '.(15 == $config['write']['blog_comm_view'] ? 'selected="selected"' : NULL).' value="15">15</option>';
echo '<option '.(20 == $config['write']['blog_comm_view'] ? 'selected="selected"' : NULL).' value="20">20</option>';
echo '</select><br />';

echo '<input type="submit" name="sett" value="'. $lng['O`zgartirish'] .'" /></form>';
echo CLOSE_DIV;   
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>