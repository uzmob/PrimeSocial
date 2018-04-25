<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


if (function_exists('error_reporting'))@error_reporting(0);
if (function_exists('set_time_limit'))@set_time_limit(60);
if (function_exists('ini_set'))
{
ini_set('display_errors',false); 
ini_set('register_globals', false); 
ini_set('session.use_cookies', true);
ini_set('session.use_trans_sid', true);
ini_set('arg_separator.output', "&amp;"); 
}
if (ini_get('register_globals')) {
  $allowed = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
  foreach ($GLOBALS as $key => $value) {
    if (!isset($allowed[$key])) {
      unset($GLOBALS[$key]);
    }
  }
}
list($msec, $sec) = explode(chr(32), microtime());
$conf['headtime'] = $sec + $msec;
$time=&time();
$phpvervion=explode('.', phpversion());
$conf['phpversion']=$phpvervion[0];
$upload_max_filesize=ini_get('upload_max_filesize');
if (eregi('([0-9]*)([a-z]*)',$upload_max_filesize,$varrs))
{
if ($varrs[2]=='M')$upload_max_filesize=$varrs[1]*1048576;
elseif ($varrs[2]=='K')$upload_max_filesize=$varrs[1]*1024;
elseif ($varrs[2]=='G')$upload_max_filesize=$varrs[1]*1024*1048576;
}

@session_name('SESS');
@session_start();
$sess=mysql_escape_string(session_id());
if (!eregi('[A-z0-9]{32}',$sess))$sess=md5(rand(09009,999999));

echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru"><head><meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml; charset=UTF-8" />
    <meta name="name" content="Prime Social" /><meta name="description" content="Prime Social" />
    <link rel="stylesheet" href="/install/style/style.css" type="text/css"/>
	<link rel="shortcut icon" href="/install/style/favicon.ico"/>
   <title>O`rnatish | Prime Social v2.2 Beta </title></head><body>
   
   <div class="head"><img src="/install/style/logo.png" width="200px;" style="margin-bottom: -6px;"/></div>
   <div class="title">Prime Social</div>'; 
	
?>