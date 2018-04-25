<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


include_once 'head.php';
include_once 'inc/functions.php';

if (empty($_SESSION['i_step']))$_SESSION['i_step']=0;
$step = (empty($_GET['step'])) ? "0" : $_GET['step'];

include 'inc/'.$_SESSION['i_step'].'.php';

include_once 'foot.php';
?>