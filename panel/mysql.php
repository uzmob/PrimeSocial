<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['MySQL so`rovlar'] .''); 

if (privilegy('mysql') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

if ($_POST['sql']) {
    if (DB::$dbs->query($_POST['mysql'])) {
        DIV_MSG . ''. $lng['So`rov muvaffaqiyatli amalga oshirildi'] .'' . CLOSE_DIV;
    } else {
        DIV_ERROR . ''. $lng[''] .'!' . CLOSE_DIV;
    }
}
echo DIV_AUT;
echo '<form action="#" method="POST">';
echo '<textarea name="mysql" style="width:95%;height:8pc;"></textarea><br />';
echo '<input type="submit" name="sql" value="'. $lng['Jo`natish'] .'"/>';
echo '</form>';
echo CLOSE_DIV;     
  
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>