<?php
/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/
require_once('../core/start.php');

check_auth();

if (privilegy('settings') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

head(''. $lng['Sayt sozlamalari'] .'');

if (!empty($_POST['send'])) {
    
    if(isset($clng[$_POST['lng']])) $sett['default_lng'] = $_POST['lng'];
    $msg = html($_POST['regMsg']);
    $present = abs(num($_POST['regPresent']));
    $us = abs(num($_POST['regUser']));
    
    DB::$dbs->query("UPDATE ".CONFIG." SET `default_lng` = ?, `reg_msg` = ?, `reg_present` = ?, `reg_user` = ?", array($sett['default_lng'], $msg, $present, $us));
    header("Location: ?");
    
}

echo DIV_BLOCK;
echo '
<form action="#" method="POST">
<b>'. $lng['Saytning odatiy tili'] .':</b><br />';
foreach($clng as $key => $value) echo '<input type="radio" name="lng" value="'. $key .'"'. ($key == $sett['default_lng'] ? ' checked' : NULL) .'/> <img src="/core/lng/'. $key .'/img.png" alt="'. $key .'" /> '. $value .'<br />';
echo '<br /><b>'. $lng['Ro`yhatdan o`tgandan so`ng habar'] .':</b><br />
<textarea name="regMsg" style="width:95%;height:5pc;">'.$sett['reg_msg'].'</textarea><br /><br />
<b>'. $lng['Sovg`a ID raqami'] .':</b><br />
<input type="text" name="regPresent" value="'.$sett['reg_present'].'" style="width:95%;"/><br />
<b>'. $lng['Jo`natuvchi ID raqami'] .':</b><br />
<input type="text" name="regUser" value="'.$sett['reg_user'].'" style="width:95%;"/><br />
<input type="submit" name="send" value="'. $lng['Saqlash'] .'" />
</form>
';
echo CLOSE_DIV;

echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';  
require_once('../core/stop.php');
?>