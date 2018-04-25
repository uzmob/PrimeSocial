<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('../core/start.php');

check_auth();

head('' . $lng['Ballarni aylantirish'] . '');

if (privilegy('balls') == FALSE) {
    header("Location: " . HOME . "/panel");
    exit();
}

if ($_POST) {
    $us = abs(num($_POST['user']));
    $balls = abs(num($_POST['balls']));

    if (empty($us) || empty($balls)) {
        $err .= '' . $lng['Hamma maydonchalarni to`ldiring'] . '<br />';
    }

    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `balls` FROM " . USERS . " WHERE `user_id` = ?", array($us));

    if (empty($ank)) {
        $err .= '' . $lng['Ma`lumotlar bazasida foydalanuvchi topilmadi'] . '<br />';
    }

    if (empty($err)) {
        DB::$dbs->query("UPDATE " . USERS . " SET `balls` = ? WHERE `user_id` = ?", array(($ank['balls'] + $balls), $ank['user_id']));
        echo DIV_MSG . '' . $lng['Ballar muvaffaqiyatli yangilandi'] . '' . CLOSE_DIV;
    } else {
        echo DIV_ERROR . $err . CLOSE_DIV;
    }
}

echo DIV_BLOCK;
echo '<form action="#" method="POST">';
echo '' . $lng['Foydalanuvchi'] . ': [ID]<br /><input type="text" name="user" /><br />';
echo '' . $lng['Ballar'] . ':<br /><input type="text" name="balls" /><br />';
echo '<input type="submit" value="' . $lng['Aylantirish'] . '" /></form>';
echo CLOSE_DIV;
echo '<div class="white"> - <a href="/panel/">' . $lng['Apanel'] . '</a></div>';
require_once('../core/stop.php');