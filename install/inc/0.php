<?
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


if(isset($_GET['step']) && $_GET['step']=='1')
{
$_SESSION['i_step']++;
header("Location: index.php?$passgen&".SID);
exit;
}
?>
		<div class="block"><center>	
		<b>Prime social</b> o`rnatish sahifasiga hush kelibsiz!<br />
		</center></div>
		<div class="lines">Hozirgi versiya: Prime Social v2.2 Beta</div>
		<div class="lines">O'rnatishdan oldin, cms ning rasmiy saytidan, yangi versiyani tekshirib ko'ring!
		<a href="http://primesocial.ru">http://primesocial.ru</a></div>	
		<div class="green"> Prime Social ning rasmiy sayti 
		<a href="http://primesocial.ru">http://primesocial.ru</a><br/>
		Ushbu cms uchun modlar va dizaynlarni rasmiy saytdan yuklashingiz mumkun.<br/>
		CMS Bo'yicha savollar, takliflar yoki cms da xatolik bo'lsa<br/> unda bu haqida PrimeSocial.ru saytidagi <a href="http://primesocial.ru/forum/">Forum</a> da yozib qoldirishingiz mumkun<br/>
		Sahifalar pastidagi kopiraytni olib tashlash taqiqlanadi.<br/>
		Mualliflik huquqini hurmat qiling!</div>	

		<div class="green">Prime Social yaratuvchisi <a href="http://primesocial.ru/id1"><b>BoB</b></a></div>

		<div class="lines">Ushbu versiyadan foydalanishni davom etish uchun, quyidagi shartlarga rozi bo'lishingiz kerak:<br /> 
		<b>Litsenziya sotib olmasdan, kopiraytni olmaslikka rozimisiz?</b></div>

<?
echo "<div class='lines'>\n";
echo "<form method='get' action='index.php'>\n";
echo "<input name='step' value='".($_SESSION['i_step']+1)."' type='hidden' />\n";
echo "<input name='gen' value='$passgen' type='hidden' />\n";
echo "<input value='Ha, roziman!' type='submit' />\n";
echo "</form></div>\n";

echo "<div class='lines'>\n";
echo "<b>$_SESSION[i_step]</b> - Qadam <b>4</b> dan</div>\n";
?>