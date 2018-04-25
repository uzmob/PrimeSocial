<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


echo "<div class='lines'>\n";
list ($php_ver1,$php_ver2,$php_ver3)=explode('.', strtok(strtok(phpversion(),'-'),' '), 3);

if ($php_ver1==5)
{
echo "PHP Versiyasi: $php_ver1.$php_ver2.$php_ver3 (OK)<br />\n";
}
else
{
echo "PHP Versiyasi: $php_ver1.$php_ver2.$php_ver3<br />\n";
$err[]="PHP Versiyasini testlash $php_ver1.$php_ver2.$php_ver3 bo`lmadi";
}

if (ini_get('session.use_trans_sid')==true)
{
echo "session.use_trans_sid: OK<br />\n";
}
else
{
echo "session.use_trans_sid: Нет<br />\n";
$err[]='COOKIE ni qo`llaymadigan brauzerlarda sessiyalar yo`qoladi';
$err[]='Korendagi .htaccess fayliga ushbu matnni <b>php_value session.use_trans_sid 1</b> qo`shing';
}


if (ini_get('magic_quotes_gpc')==0)
{
echo "magic_quotes_gpc: 0 (OK)<br />\n";
}
else
{
echo "magic_quotes_gpc: Yoqilgan<br />\n";
$err[]='Kavicheklarni ekranlashtirish yoqilgan';
$err[]='Korendagi .htaccess fayliga ushbu matnni <b>php_value magic_quotes_gpc 0</b> kiriting';
}

if (ini_get('arg_separator.output')=='&amp;')
{
echo "arg_separator.output: &amp;amp; (OK)<br />\n";
}
else
{
echo "arg_separator.output: ".output_text(ini_get('arg_separator.output'))."<br />\n";
$err[]='xml xatoliki kelib chiqishi mumkun';
$err[]='Korendagi .htaccess fayliga ushbu matnni <b>php_value arg_separator.output &amp;amp;</b> kiriting';
}

if (function_exists('iconv'))
{
echo "Iconv: OK<br />\n";
}
else
{
echo "Iconv: Yo`q<br />\n";
$err[]='Iconv siz ishlab bo`lmaydi';
}

if (class_exists('ffmpeg_movie'))
{
echo "FFmpeg: OK<br />\n";
}
else
{
echo "FFmpeg: Yo`q<br />\n";
echo "* FFmpeg siz avtomatik tarzda videolarga skrinshot yaratish mumkun emas<br />\n";
}

if (ini_get('register_globals')==false)
{
echo "register_globals off: OK<br />\n";
}
else
{
echo "register_globals on: !!!<br />\n";
$err[]='register_globals yoqilgan. Havfsizlik buzilgan';
}

if (function_exists('mcrypt_cbc'))
{
echo "Shifrlash COOKIE: OK<br />\n";
}
else
{
echo "Shifrlash COOKIE: yo`q<br />\n";
echo "* mcrypt ochiq emas<br />\n";
}



echo "</div>\n";

?>