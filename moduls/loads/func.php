<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/



function type ($type) {
    
    if ($type == 1) {
        
        $whitelist = array('.jpg', '.jpeg', '.png', '.gif', '.bmp', '.JPG', '.PNG');
        
    } elseif ($type == 2) {
        
        $whitelist = array('.avi', '.wmv', '.mov', '.mkv', '.3gp', '.flv', '.mp4');
        
    } elseif ($type == 3) {
        
        $whitelist = array('.mp3', '.wma', '.flac', '.aac', '.mmf', '.amr', '.m4a', '.m4r', '.ogg', '.mp2', '.wav');
        
    } elseif ($type == 4) {
        
        $whitelist = array('.jar');
        
    } elseif ($type == 5) {
        
        $whitelist = array('.apk', '.zip');
        
    } elseif ($type == 6) {
        
        $whitelist = array('.zip', '.rar');
        
    } elseif ($type == 7) {
        
        $whitelist = array('.ipa');
        
    } elseif ($type == 8) {
        
        $whitelist = array('.rar');
        
    } elseif ($type == 9) {
        
        $whitelist = array('.swf');
        
    } elseif ($type == 10) {
        
        $whitelist = array('.thm', '.nth', '.sis');
        
    } elseif ($type == 11) {
        
        $whitelist = array('.sis', '.sisx', '.zip', '.rar');
        
    }
    
    return $whitelist;
    
}

function type_view ($type) {
    
    if ($type == 1) {
        
        $whitelist = 'jpg, png, gif, bmp';
        
    } elseif ($type == 2) {
        
        $whitelist = 'avi, wmv, mov, mkv, 3gp, flv, mp4';
        
    } elseif ($type == 3) {
        
        $whitelist = 'mp3, wma, flac, aac, mmf, amr, amr, m4a, m4r, ogg, mp2, wav';
        
    } elseif ($type == 4) {
        
        $whitelist = 'jar';
        
    } elseif ($type == 5) {
        
        $whitelist = 'apk';
        
    } elseif ($type == 6) {
        
        $whitelist = 'zip, rar';
        
    } elseif ($type == 7) {
        
        $whitelist = 'ipa';
        
    } elseif ($type == 8) {
        
        $whitelist = 'rar';
        
    } elseif ($type == 9) {
        
        $whitelist = 'swf';
        
    } elseif ($type == 10) {
        
        $whitelist = 'thm, nth, sis';
        
    } elseif ($type == 11) {
        
        $whitelist = 'sis, sisx, zip, rar';
        
    }
    
    return $whitelist;
    
}


function translit($str) 
{
    $tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"'","э"=>"e","ю"=>"yu","я"=>"ya",
   "."=>"_"," "=>"_","?"=>"_","/"=>"_","\\"=>"_",
   "*"=>"_",":"=>"_","*"=>"_","\""=>"_","<"=>"_",
   ">"=>"_","|"=>"_"
    );
    return strtr($str,$tr);
}

?>