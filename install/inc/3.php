<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/



if (isset($_SESSION['mysql_ok']) && $_SESSION['mysql_ok']==true)
{
if(isset($_GET['step']) && $_GET['step']=='4')
{
$_SESSION['i_step']++;
header("Location: index.php?$passgen&".SID);
exit;
}
}
elseif (isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['db']))
{
if(!($db=@mysql_connect($_POST['host'], $_POST['user'],$_POST['pass'])))
{
$err[]='Serverga ulanib bo`lmayapti '.$_POST['host'];
}
elseif(!@mysql_select_db($_POST['db'],$db))
{
$err[]='Bazaning nomini tekshirib ko`ring';
}
else
{
$set['mysql_db_name']=$_SESSION['db']=$_POST['db'];
$set['mysql_host']=$_SESSION['host']=$_POST['host'];
$set['mysql_user']=$_SESSION['user']=$_POST['user'];
$set['mysql_pass']=$_SESSION['pass']=$_POST['pass'];

mysql_query('set charset utf8'); 
mysql_query('SET names utf8'); 
mysql_query('set character_set_client="utf8"'); 
mysql_query('set character_set_connection="utf8"'); 
mysql_query('set character_set_result="utf8"');

$buf = file_get_contents('./inc/t.sql');
$a = 0;

while ($b = strpos($buf,';',$a+1)){
 $i++;
 $a = substr($buf,$a+1,$b-$a);
 mysql_query($a);
 $a = $b;
 }
echo "Yuklangan tablitsalar soni:".$i;
if($i > 0){
$_SESSION['mysql_ok']=true;
}
}
}

if (isset($_SESSION['mysql_ok']) && $_SESSION['mysql_ok']==true)
{
echo "<div class='msg'>Ma`lumotlar bazasiga muvaffaqiyatli ulandi</div>\n";

if (isset($msg))
{
foreach ($msg as $key=>$value) {
echo "<div class='msg'>$value</div>\n";
}
}
if (isset($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>\n";
}
}
echo "<hr />\n";
echo "<form method=\"get\" action=\"index.php\">\n";
echo "<input name=\"step\" value=\"".($_SESSION['i_step']+1)."\" type=\"hidden\" />\n";
echo "<input value=\"".(isset($err)?'Nimadir yetishmayapti':'Davom etish')."\" type=\"submit\"".(isset($err)?' disabled="disabled"':null)." />\n";
echo "</form>\n";
}
else
{
if (isset($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>\n";
}
}
echo "<div class='white'><form method=\"post\" action=\"index.php?$passgen\">\n";
echo "DBHOST:<br />\n";
echo "<input name=\"host\" value=\"$set[mysql_host]\" type=\"text\" /><br />\n";
echo "DBUSER:<br />\n";
echo "<input name=\"user\" value=\"$set[mysql_user]\" type=\"text\" /><br />\n";
echo "DBPASS:<br />\n";
echo "<input name=\"pass\" value=\"$set[mysql_pass]\" type=\"text\" /><br />\n";
echo "DBNAME:<br />\n";
echo "<input name=\"db\" value=\"$set[mysql_db_name]\" type=\"text\" /><br />\n";
if (isset($db_not_null))
echo "<label><input type='checkbox' checked='checked' name='rename' value='1' /> Avvaldan bor bo`lgan tablitsalarni nomini o`zgartirish<br /></label>\n";
echo "<input value=\"Davom etish\" type=\"submit\" />\n";
echo "</form></div>\n";
}


echo "<div class='lines'>\n";
echo "<b>$_SESSION[i_step]</b> - Qadam <b>4</b> dan</div>\n";
?>