<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


function permissions($filez){
return decoct(@fileperms("$filez")) % 1000;
}

function test_chmod($df,$chmod)
{
global $err,$user;
if (isset($user) && $user['level']==10)
$show_df=ereg_replace('^'.H, $_SERVER["DOCUMENT_ROOT"].'/', $df);
else $show_df=$df;


@list($f_chmod1,$f_chmod2,$f_chmod3)=str_split(permissions($df));
list($n_chmod1,$n_chmod2,$n_chmod3)=str_split($chmod);

if ($f_chmod1<$n_chmod1 || $f_chmod2<$n_chmod2 || $f_chmod3<$n_chmod3)
{
$err[]="CHMOD ni o`rnating $n_chmod1$n_chmod2$n_chmod3 на $show_df";
echo "<div class='off'>$show_df : [$f_chmod1$f_chmod2$f_chmod3] - >$n_chmod1$n_chmod2$n_chmod3</div>\n";
}
else
{
echo "<div class='on'>$show_df ($n_chmod1$n_chmod2$n_chmod3) : $f_chmod1$f_chmod2$f_chmod3 (OK)</div>\n";
}
}

if (file_exists($_SERVER[DOCUMENT_ROOT].'install/'))test_chmod(H.'install/',777);
test_chmod($_SERVER[DOCUMENT_ROOT].'/files/album/',777);
test_chmod($_SERVER[DOCUMENT_ROOT].'/files/forum/',777);
test_chmod($_SERVER[DOCUMENT_ROOT].'/files/groups/',777);
test_chmod($_SERVER[DOCUMENT_ROOT].'/files/loads/',777);
?>