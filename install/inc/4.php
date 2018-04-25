<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


if (isset($_POST['reg']))
{
$db=mysql_connect($_SESSION['host'], $_SESSION['user'],$_SESSION['pass']);
mysql_select_db($_SESSION['db'],$db);
mysql_query('set charset utf8'); 
mysql_query('SET names utf8'); 
mysql_query('set character_set_client="utf8"'); 
mysql_query('set character_set_connection="utf8"'); 
mysql_query('set character_set_result="utf8"');

$tmp_set['title']=strtoupper($_SERVER['HTTP_HOST']).' - Bosh sahifa';
$tmp_set['mysql_host']=$_SESSION['host'];
$tmp_set['mysql_user']=$_SESSION['user'];
$tmp_set['mysql_pass']=$_SESSION['pass'];
$tmp_set['mysql_db_name']=$_SESSION['db'];

 $dbfile = "<?php\r\n".
"define ('DBHOST', 'localhost');\r\n".
"define ('DBPORT', '3306');\r\n".
"define ('DBNAME', '$_SESSION[db]');\r\n".
"define ('DBUSER', '$_SESSION[user]');\r\n".
"define ('DBPASS', '$_SESSION[pass]'); \r\n".
"define('HOME', 'http://$_SERVER[HTTP_HOST]');";

    if (!file_put_contents('../core/db.php', $dbfile)) {
     echo 'ERROR: Can not write db.php</body></html>';
     exit;
      }		
if (empty($err))
{
$nick = mysql_real_escape_string($_POST[nick]);
$password = md5(mysql_real_escape_string($_POST[password]));
mysql_query("INSERT INTO `users` SET `nick` = '$nick', `password` = '$password', `gender` = '1', `level` = '4'");
            $_SESSION['user_id'] = $sql['user_id'];
                setcookie("nick", $nick, time()+9999999);
                setcookie("password", $password, time()+9999999);
		
 echo 'Prime Social muvaffaqiyatli o`rnatildi. install papkasini o`chiring<br/>
 <a href="/">Saytga</a> o`tish';
 exit;
		
}
}

if (isset($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>\n";
}

}
echo "<div class='white'><form action='index.php?$passgen' method='post'>\n";
echo "Login (3-16 belgi):<br />\n<input type='text' name='nick'".((isset($nick))?" value='".$nick."'":" value='ADMIN'")." maxlength='16' /><br />\n";
echo "Parol (6-16 belgi):<br />\n<input type='password'".((isset($password))?" value='".$password."'":null)." name='password' maxlength='16' /><br />\n";
echo "* Oddiy parollar xakerlarni hayotini yahshilaydi<br />\n";
echo "* Hamma maydonchalar to`ldirilishi shart!<br />\n";
echo "<input type='submit' name='reg' value='Ro`yhatdan o`tish' /><br />\n";
echo "</form></div>\n";
echo "<div class='lines'>\n";
echo "<b>$_SESSION[i_step]</b> - Qadam <b>4</b> dan</div>\n";
?>