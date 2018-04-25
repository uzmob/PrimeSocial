<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

session_start();
ob_start();
require_once('config.php');
require_once('count.php');
require_once('class/nav.php');
require_once('class/browser.php');
require_once('functions.php');
gen_timer_start();

/* Cookie orqali kirish */
if (empty($_SESSION['user_id'])) {
    if (!empty($_COOKIE['nick']) && !empty($_COOKIE['password'])) {
        $nick = html($_COOKIE['nick']);
        $password = html($_COOKIE['password']);
        $sql = DB::$dbs->queryFetch("SELECT `user_id` FROM " . USERS . " WHERE `nick` = ? && `password` = ?", array($nick, $password));

        if ($sql) {
            $_SESSION['user_id'] = $sql['user_id'];
            header("Location: " . HOME . "/");
        }
    }
}

if (isset($_GET['select'])) {
    $select = html($_GET['select']);
} else {
    $select = NULL;
}

$ulng = $sett['default_lng'];
$clng = parse_ini_file(inc . 'core/lng/config.ini');

/* Ro`yhatdan o`tgan foydalanuvchiga */
if (isset($_SESSION['user_id'])) {

    /* Brauzer/IP ni bilib olish*/
    $browser = new Browser();
    $user_agent = $browser->getBrowser() . ' (Ver: ' . $browser->getVersion() . ')';
    $ip = getIP2();
    DB::$dbs->query("UPDATE " . USERS . " SET `browser` = ?, `ip` = ?, `last_time` = ? WHERE `user_id` = ?", array($user_agent, $ip, time(), num($_SESSION['user_id'])));
    /**/

    $user = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_SESSION['user_id'])));

    if (!file_exists(inc . 'style/themes/' . $user['style'] . '/pages/head.php')) {
        $styler = 'default';
    } else {
        $styler = $user['style'];
    }

    if (empty($user)) {
        unset($_SESSION['user_id']);
        header("Location: " . HOME . "/index.php");
    }


    if (isset($clng[$user['lng']])) $ulng = $user['lng'];
    $lng = parse_ini_file(inc . 'core/lng/' . $ulng . '/lng.ini');

    /* Banga tekshirish */
    $bann = DB::$dbs->queryFetch("SELECT * FROM " . BANN . " WHERE `user_id` = ? && `time_bann` > ?", array($user['user_id'], time()));
    if ($bann == TRUE) {
        head('' . $lng['Siz bloklangansiz'] . '!');
        echo DIV_BLOCK . '' . $lng['Siz bloklangansiz'] . '!<br /><b>' . $lng['Sababi'] . ':</b> ' . $bann['prich'] . '<br />
		<b>' . $lng['Ban tugash vaqti'] . ':</b> ' . vrem($bann['time_bann']) . CLOSE_DIV;
        require_once('core/stop.php');
        exit();
    }

    /* Qora ro`yhatga tekshirish */
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . BLACKLIST . " WHERE `ua` = ? && `ip` = ? ", array($user_agent, $ip)) == TRUE) {
        head('' . $lng['Siz bloklangansiz'] . '!');
        echo DIV_ERROR . '' . $lng['Sizning User Agent va IP qora ro`hatda. Saytga kirish taqiqlanadi.'] . '' . CLOSE_DIV;
        require_once('core/stop.php');
        exit();
    }

    /* Kunlik bonus */
    if (!empty($user) && $user['bonus_time'] < time()) {
        $balls = rand(1, 50);
        head('' . $lng['Kunlik bonus'] . '');
        echo DIV_BLOCK . '' . $lng['Salom'] . '! ' . $lng['Saytimiz ma`muriyati sizni bugun saytga tashrif qilganizdan hursand'] . '!<br />
		' . $lng['Shu sababli, bizdan sizga sovg`a'] . ' ' . $balls . ' ' . $lng['ball'] . '!
		' . $lng['Har kuni kiring va ko`proq ballga ega bo`ling'] . '! :)<br /><br />
		<a href="' . HOME . '"><b>' . $lng['Saytga kirish'] . '</b></a>' . CLOSE_DIV;
        DB::$dbs->query("UPDATE " . USERS . " SET `bonus_time` = ?, `balls` = ? WHERE `user_id` = ?", array((time() + 86400), ($user['balls'] + $balls), num($_SESSION['user_id'])));
        require_once('core/stop.php');
        exit();
        /**/
    }
} else {

    if (isset($_GET['lng'])) $_SESSION['lng'] = $_GET['lng'];
    if (isset($_SESSION['lng'], $clng[$_SESSION['lng']])) $ulng = $_SESSION['lng'];

    $lng = parse_ini_file(inc . 'core/lng/' . $ulng . '/lng.ini');

    if (!file_exists(inc . 'style/themes/' . $user['style'] . '/pages/head.php')) {
        $styler = 'default';
    } else {
        $styler = $user['style'];
    }
}