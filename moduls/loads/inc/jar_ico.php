<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


include('vvv.php');
$file = '../../../files/loads/files/1365180960.jar';
$q = array("book.png","icon.png","ico.png","i.png","icono.png","Icon.png","Ico.png","I.png","Icono.png","ICON.png","ICO.png","I.png","ICONO.png","ICON.PNG","ICO.PNG","I.PNG","ICONO.PNG","icons/icon.png","icons/ico.png","icons/i.png","icons/icono.png","i","I");
	$zip = new PclZip($file);
	$ar = $zip->extract(PCLZIP_OPT_BY_NAME,$q,PCLZIP_OPT_EXTRACT_IN_OUTPUT);
	if(!empty($ar)) {
	   #print_r($ar);
    echo $zip->getFromIndex($index);
    }
    

?>