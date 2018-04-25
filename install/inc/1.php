<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


include_once 'testing.php';

if (isset($err))
{
if (is_array($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>\n";
}
}
else
echo "<div class='err'>$err</div>\n";
}
else
if(isset($_GET['step']) && $_GET['step']=='2')
{
$_SESSION['i_step']++;
header("Location: index.php?$passgen&".SID);
exit;
}

echo "<div class='lines'>\n";
echo "<form method=\"get\" action=\"index.php\">\n";
echo "<input name='gen' value='$passgen' type='hidden' />\n";
echo "<input name=\"step\" value=\"".($_SESSION['i_step']+1)."\" type=\"hidden\" />\n";
echo "<input value=\"".(isset($err)?'Nimadir yetishmayapti':'Davom etish')."\" type=\"submit\"".(isset($err)?' disabled="disabled"':null)." />\n";
echo "</form></div>\n";
echo "<div class='lines'>\n";
echo "<b>$_SESSION[i_step]</b> - Qadam <b>4</b> dan</div>\n";
?>