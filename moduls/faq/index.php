<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');



head(''. $lng['Ma`lumotlar'] .'');


echo DIV_LI . ''.icon('qoida.png').' <a href="'.HOME.'/qoidalar">'. $lng['Qoidalar'] .'</a>' . CLOSE_DIV;
echo DIV_LI . ''.icon('smile.png').' <a href="'.HOME.'/smiles">'. $lng['Smayllar'] .'</a>' . CLOSE_DIV;
echo DIV_LI . ''.icon('aharf.png').' <a href="'.HOME.'/bb">'. $lng['BB kodlar'] .'</a>' . CLOSE_DIV;
echo DIV_LI . ''.icon('pig.png').' <a href="'.HOME.'/balls">'. $lng['Ballar ishlang'] .'</a>' . CLOSE_DIV;

echo '<div class="lines">';
$all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `level` > ?", array(0));
echo ''.icon('adm.png').' <a href="'.HOME.'/admin/" > '. $lng['Sayt ma`muriyati'] .' <span class="count">'.$all.'</span></a>';
echo '</div>';

echo '<div class="white">';
echo ''. $lng['Izlayotgan ma`lumotingizni topmadingizmi'] .'? 
 <a href="'.HOME.'/touch/" ><b>'. $lng['Tiket'] .'</b></a> '. $lng['sizga yordam'] .'! '.icon('aqlli.png').'  ';
echo '</div>';

require_once('../../core/stop.php');
?>