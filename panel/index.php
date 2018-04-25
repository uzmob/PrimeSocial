<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

if (empty($user['level'])) {
    header("Location: " . HOME);
}


switch ($select) {
        
    default:
head(''. $lng['Boshqaruv paneli'] .'');

echo '<div class="white"><center><b>Prime Social v2.2 Beta</b><br/>
<span style="font-size:10px;color:#999;">Bobur (BoB)</span><br/>
'.icon('tg.png').' <a href="http://t.me/primesocial"><u>t.me/primesocial</u></a></center>' . CLOSE_DIV;

    echo '<div class="lines">'.icon('primesocial.png').' <a href="http://primesocial.ru/">'. $lng['Rasman sayti'] .'</a></div>';
    echo '<div class="lines">'.icon('info.png').' <a href="http://primesocial.ru/about">'. $lng['Prime Social haqida'] .'</a>  </div>';
    echo '<div class="lines">'.icon('sozlash.png').' <a href="?select=bulimlar">'. $lng['Bo`limlar sozlamalari'] .'</a></div>';
    echo '<div class="lines">'.icon('adm.png').' <a href="?select=adm">'. $lng['Ma`muriyat uchun'] .'</a></div>';
    echo '<div class="lines">'.icon('gear.png').' <a href="?select=boshqa">'. $lng['Boshqa sozlamalar'] .'</a></div>';
    echo '<div class="lines">'.icon('tizim.png').' <a href="?select=tizim">'. $lng['Tizim'] .'</a></div>';

	
    break; 
	
	
    case 'bulimlar':
    head(''. $lng['Bo`limlar sozlamalari'] .'');
	
	    echo (privilegy('news') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/news/">'. $lng['Yangiliklar'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('chat') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/chat/">'. $lng['Chat'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('guestbook') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/guestbook/">'. $lng['Jonli efir'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('forum') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/forum/">'. $lng['Forum'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('blog') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/blog/">'. $lng['Blog'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('group') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/groups/">'. $lng['Guruhlar'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('lib') ? DIV_LI . '-  
	<a href="'.HOME.'/panel/lib/">'. $lng['Kutubxona'] .' </a>' . CLOSE_DIV : NULL);
    echo (privilegy('zc') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/zc/">'. $lng['Fayl almashinuv'] .' </a>' . CLOSE_DIV : NULL);
	 
    break; 
	
	   
    case 'adm':
    head(''. $lng['Ma`muriyat uchun'] .'');
    $ticket = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".TOUCH_MSG." WHERE `status` = ?", array(1));
    echo (privilegy('ticket') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/ticket/">'. $lng['Tiketlar'] .'</a> ' . ($ticket > 0 ? '<b>[+' . $ticket . ']</b>' : NULL) . CLOSE_DIV : NULL);
    echo (privilegy('positions') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/privilege/">'. $lng['Lavozimlar boshqaruvi'] .'</a>' . CLOSE_DIV : NULL);
    echo (privilegy('spam') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/spam/">'. $lng['Spamlarni boshqarish'] .'</a>' . CLOSE_DIV : NULL);
    $files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES_FILE." WHERE `status` = '1' ");
    $all = $files;
    $bann = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BANN." WHERE `time_bann` > ?", array(time()));
    echo (privilegy('anceta_bann') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/bann_list/">'. $lng['Ban olganlar'] .'</a> [' . $bann . ']' . CLOSE_DIV : NULL);    
    $black = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLACKLIST."");
    echo (privilegy('anceta_bann') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/black_list/">'. $lng['Qora ro`yhat'] .'</a> [' . $black . ']' . CLOSE_DIV : NULL);
    echo (privilegy('moder') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/moder/">'. $lng['Moderatsiya'] .'</a>' . ($all > 0 ? ' <b>+' . $all . '</b>' : NULL) . CLOSE_DIV : NULL);
	
    break; 
	
    case 'boshqa':
    head('Boshqa bo`limlar');
	 echo (privilegy('balls') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/balls/">'. $lng['Ballarni aylantirish'] .'</a>' . CLOSE_DIV : NULL);
    echo (privilegy('smiles') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/smiles/">'. $lng['Smayllar'] .'</a>' . CLOSE_DIV : NULL);
	
    break; 
	
    case 'tizim':
    head('Tizim');
	echo (privilegy('mysql') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/mysql/">'. $lng['MySQL so`rovlar'] .'</a>' . CLOSE_DIV : NULL);
    echo (privilegy('settings') ? DIV_LI . '- 
	<a href="'.HOME.'/panel/settings/">'. $lng['Sayt sozlamalari'] .'</a>' . CLOSE_DIV : NULL);
 
    break; 
}

require_once('../core/stop.php');
?>