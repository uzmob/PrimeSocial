<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('core/start.php');

head('' . $lng['Kirish'] . '');

if ($_POST) {
    $nick = html($_POST['nick']);
    $password = md5($_POST['password']);

    if (empty($nick) || empty($password)) {
        echo DIV_AUT . '' . $lng['Kirishda xatolik'] . '' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->queryFetch("SELECT `user_id` FROM " . USERS . " WHERE `nick` = ? && `password` = ?", array($nick, $password));

        if ($sql) {
            $_SESSION['user_id'] = $sql['user_id'];

            if (!empty($_POST['remember'])) {
                setcookie("nick", $nick, time() + 9999999);
                setcookie("password", $password, time() + 9999999);
            }

            header("Location: " . HOME . "/page");
        } else {
            echo '<div class="white">' . icon('loading.gif', 20, 20) . '<br/> ' . $lng['Kirishda xatolik'] . '</div>';
        }
    }
}

require_once('core/stop.php');