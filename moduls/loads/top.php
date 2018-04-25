<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

switch ($select) {
    
    case 'pictures':
    head(''. $lng['TOP Rasmlar'] .''); 
    
    if (!empty($_GET['sort'])) {
        if ($_GET['sort'] == 'loads') {
            $_SESSION['sort'] = 'loads';
        } else {
            $_SESSION['sort'] = 'rating';
        }
    }
    
    echo DIV_BLOCK;
    echo '<b>'. $lng['Saralash'] .':</b> ' . ( empty($_SESSION['sort']) || $_SESSION['sort'] == 'loads' ? '<b>'. $lng['Yuklashlar soni'] .'</b>' : '<a href="?sort=loads">'. $lng['Yuklashlar soni'] .'</a>') . ' ' . ($_SESSION['sort'] == 'rating' ? '<b>'. $lng['Reytingi'] .'</b>' : '<a href="?sort=rating">'. $lng['Reytingi'] .'</a>'); 
    echo CLOSE_DIV;

    /* Rasm ko`rsatilishi */
    $array = array(30, 60, 100);
    
    if (!empty($_GET['prev'])) {
        $prev1 = $_GET['prev'];
        
        if ($prev1 == $array[0]) {
            unset($_SESSION['prev']);
        } elseif ($prev1 == $array[1]) {
            $_SESSION['prev'] = 1;
        } elseif ($prev1 == $array[2]) {
            $_SESSION['prev'] = 2;
        } else {
            $_SESSION['prev'] = 'no';
        }
    }
    
    if (empty($_SESSION['prev'])) {
        $prev = 'wight="'.$array[0].'" height="'.$array[0].'"';            
    } elseif ($_SESSION['prev'] == 1) {
        $prev = 'wight="'.$array[1].'" height="'.$array[1].'"';   
    } elseif ($_SESSION['prev'] == 2) {
        $prev = 'wight="'.$array[2].'" height="'.$array[2].'"';   
    } else {
        $prev = NULL; 
    }

    echo DIV_BLOCK;
    echo ''.icon('screan.png').'  ' . (empty($_SESSION['prev']) ? '<b>['.$array[0].'x'.$array[0].']</b>' : '<a href="?prev='.$array[0].'">['.$array[0].'x'.$array[0].']</a>') . ' 
    ' . ($_SESSION['prev'] == 1 ? '<b>['.$array[1].'x'.$array[1].']</b>' : '<a href="?prev='.$array[1].'">['.$array[1].'x'.$array[1].']</a>') . ' ' .
    ($_SESSION['prev'] == 2 ? '<b>['.$array[2].'x'.$array[2].']</b>' : '<a href="?prev='.$array[2].'">['.$array[2].'x'.$array[2].']</a>') . ' ' . 
    ($_SESSION['prev'] == 'no' ? '<b>[x]</b>' : '<a href="?prev=no">[x]</a>');
    echo CLOSE_DIV;
    /* *** */
        
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `type` = '.jpg' || `type` = '.jpeg' || `type` = '.gif' || `type` = '.png' || `type` = '.bmp'");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Rasmlar kiritilmagan'] .'' . CLOSE_DIV;
    } else {
        if (empty($_SESSION['sort']) || $_SESSION['sort'] == 'loads') {
            $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `type` = '.jpg' || `type` = '.jpeg' || `type` = '.gif' || `type` = '.png' || `type` = '.bmp' ORDER BY `loads` DESC LIMIT 10 ");
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `type` = '.jpg' || `type` = '.jpeg' || `type` = '.gif' || `type` = '.png' || `type` = '.bmp' ORDER BY `rating` DESC LIMIT 10 ");
        }
        
        while($file = $sql -> fetch()) {
            
            echo DIV_AUT . '<a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a> ['.(empty($_SESSION['sort']) || $_SESSION['sort'] == 'loads' ? $file['loads'] . ' '. $lng['Yuklashlar'] .' ' : ''. $lng['reyting'] .': ' . $file['rating']).']' . CLOSE_DIV;  
            echo DIV_LI;   
            if (empty($_SESSION['prev']) || $_SESSION['prev'] == 1 || $_SESSION['prev'] == 2 ) {
                echo '<a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/"><img src="'.HOME.'/files/loads/files/mini_'.$file['url'].'" '.$prev.'/></a>';
            }
            echo CLOSE_DIV;
        
        }        
    }     
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>'; 
    break;

    case 'appl':
    head(''. $lng['TOP Dasturlar'] .'');  
    
    if (!empty($_GET['sort'])) {
        if ($_GET['sort'] == 'loads') {
            $_SESSION['sort'] = 'loads';
        } else {
            $_SESSION['sort'] = 'rating';
        }
    }
    
    echo DIV_BLOCK;
    echo '<b>'. $lng['Saralash'] .':</b> ' . ( empty($_SESSION['sort']) || $_SESSION['sort'] == 'loads' ? '<b>'. $lng['Yuklashlar soni'] .'</b>' : '<a href="?sort=loads">'. $lng['Yuklashlar soni'] .'</a>') . ' ' . ($_SESSION['sort'] == 'rating' ? '<b>'. $lng['Reytingi'] .'</b>' : '<a href="?sort=rating">'. $lng['Reytingi'] .'</a>'); 
    echo CLOSE_DIV;

    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `type` = '.sisx' || `type` = '.sis' || `type` = '.jar' || `type` = '.apk' || `type` = '.zip' || `type` = '.rar' || `type` = '.ipa'");
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Fayllar kiritilmagan'] .'' . CLOSE_DIV;
    } else {
        if (empty($_SESSION['sort']) || $_SESSION['sort'] == 'loads') {
            $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `type` = '.sisx' || `type` = '.sis' || `type` = '.jar' || `type` = '.apk' || `type` = '.zip' || `type` = '.rar' || `type` = '.ipa' ORDER BY `loads` DESC LIMIT 10 ");
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `type` = '.sisx' || `type` = '.sis' || `type` = '.jar' || `type` = '.apk' || `type` = '.zip' || `type` = '.rar' || `type` = '.ipa' ORDER BY `rating` DESC LIMIT 10 ");
        }
        
        while($file = $sql -> fetch()) {
            
            echo DIV_LI . '<a href="'.HOME.'/loads/'.$file['folder_id'].'/'.$file['folderc_id'].'/'.$file['id'].'/">'.$file['name'].'</a> ['.(empty($_SESSION['sort']) || $_SESSION['sort'] == 'loads' ? $file['loads'] . ' yuklashlar' : 'Reyting: ' . $file['rating']).']' . CLOSE_DIV;  

        }        
    }     
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>'; 

    break;
    
}


require_once('../../core/stop.php');
?>