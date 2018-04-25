<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('core/start.php');

if (isset($_GET['ref'])) {
    $id = abs(num($_GET['ref']));

    $ank = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if (!empty($ank)) {

        $_SESSION['ref'] = $ank['user_id'];

    }
}

function GetRef()
{
    global $lng;
    if (!empty($_SESSION['ref'])) {
        echo DIV_BLOCK . '' . $lng['Mening referalim'] . ': ' . user_choice(num($_SESSION['ref']), 'link') . CLOSE_DIV;
    }

}

switch ($select) {

    default:
        head('' . $lng['Ro`yhatdan o`tish.  1 - qadam'] . '');

        GetRef();

        echo '<div class="tepa">' . $lng['Ism va familiyangizni ko`rsating'] . '' . CLOSE_DIV;

        if ($_POST) {

            $surname = html($_POST['surname']);
            $name = html($_POST['name']);
            $gender = num($_POST['gender']);

            if (empty($surname) || empty($name)) {
                $err .= '' . $lng['Barcha maydonchalarni to`ldiring'] . '<br />';
            }

            if (strlen($surname) < 2) {
                $err .= '' . $lng['Juda qisqa familiya'] . '. [Min. 2]<br />';
            }

            if (strlen($name) < 2) {
                $err .= '' . $lng['Juda qisqa ism'] . '. [Min. 2]<br />';
            }

            $_SESSION['surname'] = $surname;
            $_SESSION['name'] = $name;
            $_SESSION['gender'] = $gender;

            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                header("Location: reg/2");
            }

        }

        echo '<form action="' . HOME . '/reg" method="POST">';

        echo DIV_AUT . '<b>' . $lng['Familiya'] . ':</b><br /><input type="text" name="surname" value="' . (!empty($_SESSION['surname']) ? html($_SESSION['surname']) : NULL) . '" /><br /><br />';
        echo '<b>' . $lng['Ism'] . ':</b><br /><input type="text" name="name" value="' . (!empty($_SESSION['name']) ? html($_SESSION['name']) : NULL) . '" /><br /><br />';
        echo '<input type="radio" name="gender" value="0" ' . ($_SESSION['gender'] == 0 ? 'checked="checked"' : NULL) . ' /> ' . $lng['Ayol'] . '<br /><input type="radio" name="gender" value="1" ' . ($_SESSION['gender'] == 1 ? 'checked="checked"' : NULL) . ' /> ' . $lng['Erkak'] . '<br /><br />';
        echo '<input type="submit" name="submit" value="' . $lng['Davom etish'] . '" />' . CLOSE_DIV;
        echo '</form>';

        break;

    case '2':
        if (empty($_SESSION['surname']) || empty($_SESSION['name'])) {
            header("Location: reg");
        }

        GetRef();

        head('' . $lng['Ro`yhatdan o`tish. 2 - qadam'] . '');

        echo '<div class="tepa">' . $lng['E-Mail va parolingizni kiriting'] . '' . CLOSE_DIV;

        if ($_POST) {

            $nick = html($_POST['nick']);
            $email = html($_POST['email']);
            $password = html($_POST['password']);
            $password2 = html($_POST['password2']);

            if (empty($nick) || empty($password)) {
                $err .= '' . $lng['Barcha maydonchalarni to`ldiring'] . '<br />';
            }

            if (!preg_match("/^[A-Za-z0-9]+[-_]*[A-Za-z0-9]+$/", $nick)) {
                $err .= '' . $lng['Nikni to`g`ri shaklda kiriting'] . '<br />';
            }

            if (DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `nick` = ?", array($nick)) == TRUE) {
                $err .= '' . $lng['Bunday nik bor saytda'] . '<br />';
            }

            if (strlen($password) < 6) {
                $err .= '' . $lng['Juda qisqa parol'] . '. [Min. 6]<br />';
            }

            if ($password != $password2) {
                $err .= '' . $lng['Parollar to`g`ri kelmayapti'] . '.<br />';
            }

            $_SESSION['nick'] = $nick;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;

            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                header("Location: " . HOME . "/reg/3");
            }

        }

        echo '<form action="' . HOME . '/reg/2" method="POST">';

        echo DIV_AUT . '
    <b>' . $lng['Login'] . ':</b><br />
	<input type="text" style="width:95%;" name="nick" value="' . (!empty($_SESSION['nick']) ? html($_SESSION['nick']) : NULL) . '" /><br /><br />
    <b>' . $lng['E-Mail'] . ':</b> [' . $lng['shart emas'] . ']<br />
	<input type="text" style="width:95%;" name="email" value="' . (!empty($_SESSION['email']) ? html($_SESSION['email']) : NULL) . '" /><br /><br />';
        echo '<b>' . $lng['Parol'] . ':</b><br />
	<input type="password" style="width:95%;" name="password" /><br /><br />';
        echo '<b>' . $lng['Parolni takrorlang'] . ':</b><br />
	<input type="password" style="width:95%;" name="password2" /><br /><br />';

        echo '<input type="submit" name="submit" value="' . $lng['Davom etish'] . '" />' . CLOSE_DIV;
        echo '</form>';

        break;

    case '3':
        if (empty($_SESSION['nick']) || empty($_SESSION['surname']) || empty($_SESSION['name']) || empty($_SESSION['password'])) {
            header("Location: reg");
        }

        head('' . $lng['Ro`yhatdan o`tish. 3 - qadam'] . '');

        echo '<div class="tepa">' . $lng['Yashash joyingizni tanlang'] . '' . CLOSE_DIV;

        if (isset($_GET['country'])) {
            if (DB::$dbs->querySingle("SELECT COUNT(`country_id`) FROM " . COUNTRY . " WHERE `country_id` = ?", array(num($_GET['country']))) == TRUE) {
                echo $_GET['country'];
                $_SESSION['country'] = num($_GET['country']);
                header("Location: " . HOME . "/reg/4");
            } else {
                echo DIV_ERROR . '' . $lng['Ma`lumotlar bazasida topilmadi'] . '' . CLOSE_DIV;
            }
        }

        echo DIV_AUT . '' . $lng['Davlat'] . '...' . CLOSE_DIV . DIV_AUT;
        $all = DB::$dbs->querySingle("SELECT COUNT(`country_id`) FROM " . COUNTRY . "");
        $n = new Navigator($all, 15, 'select=3');
        $sql = DB::$dbs->query("SELECT * FROM " . COUNTRY . " LIMIT {$n->start()}, 15");
        while ($country = $sql->fetch()) {
            echo ' - <a href="' . HOME . '/reg/3?country=' . $country['country_id'] . '">' . $country['name'] . '</a><br />';
        }
        echo CLOSE_DIV;
        echo $n->navi();

        break;

    case '4':
        if (empty($_SESSION['nick']) || empty($_SESSION['surname']) || empty($_SESSION['name']) || empty($_SESSION['password']) || empty($_SESSION['country'])) {
            header("Location: reg");
        }

        head('' . $lng['Ro`yhatdan o`tish. 3 - qadam'] . '');

        echo '<div class="tepa">' . $lng['Yashash joyingizni tanlang'] . '' . CLOSE_DIV;

        if (isset($_GET['region'])) {
            if (DB::$dbs->querySingle("SELECT COUNT(`region_id`) FROM " . REGION . " WHERE `region_id` = ?", array(num($_GET['region']))) == TRUE) {
                $_SESSION['region'] = num($_GET['region']);
                header("Location: " . HOME . "/reg/5");
            } else {
                echo DIV_ERROR . '' . $lng['Baza ma`lumotlarda rayon topilmadi'] . '' . CLOSE_DIV;
            }
        }
        $country = DB::$dbs->queryFetch("SELECT `name` FROM " . COUNTRY . " WHERE `country_id` = ? LIMIT 1", array(num($_SESSION['country'])));
        echo DIV_AUT . '<b>' . $country['name'] . '</b> / ' . $lng['Rayon'] . '...' . CLOSE_DIV . DIV_AUT;
        $all = DB::$dbs->querySingle("SELECT COUNT(`region_id`) FROM " . REGION . " WHERE `country_id` = ?", array(num($_SESSION['country'])));
        $n = new Navigator($all, 15, 'select=4');
        $sql = DB::$dbs->query("SELECT * FROM " . REGION . " WHERE `country_id` = ? LIMIT {$n->start()}, 15", array(num($_SESSION['country'])));
        while ($region = $sql->fetch()) {
            echo ' - <a href="' . HOME . '/reg/4?region=' . $region['region_id'] . '">' . $region['name'] . '</a><br />';
        }
        echo CLOSE_DIV;
        echo $n->navi();

        break;

    case '5':
        if (empty($_SESSION['nick']) || empty($_SESSION['surname']) || empty($_SESSION['name']) || empty($_SESSION['password']) || empty($_SESSION['country']) || empty($_SESSION['region'])) {
            header("Location: reg");
        }

        head('' . $lng['Ro`yhatdan o`tish. 3 - qadam'] . '');

        echo '<div class="tepa">' . $lng['Yashash joyingizni tanlang'] . '' . CLOSE_DIV;

        if (isset($_GET['city'])) {
            if (DB::$dbs->querySingle("SELECT COUNT(`city_id`) FROM " . CITY . " WHERE `city_id` = ?", array(num($_GET['city']))) == TRUE) {
                $_SESSION['city'] = num($_GET['city']);
                header("Location: " . HOME . "/reg/6");
            } else {
                echo DIV_ERROR . '' . $lng['Shahar baza ma`lumotida topilmadi'] . '' . CLOSE_DIV;
            }
        }

        $country = DB::$dbs->queryFetch("SELECT `name` FROM " . COUNTRY . " WHERE `country_id` = ? LIMIT 1", array(num($_SESSION['country'])));
        $region = DB::$dbs->queryFetch("SELECT `name` FROM " . REGION . " WHERE `region_id` = ? LIMIT 1", array(num($_SESSION['region'])));
        echo DIV_AUT . '<b>' . $country['name'] . '</b> / <b>' . $region['name'] . '</b> / ' . $lng['Shahar'] . '...' . CLOSE_DIV . DIV_AUT;

        $all = DB::$dbs->querySingle("SELECT COUNT(`city_id`) FROM " . CITY . " WHERE `region_id` = ?", array(num($_SESSION['region'])));
        $n = new Navigator($all, 15, 'select=5');
        $sql = DB::$dbs->query("SELECT * FROM " . CITY . " WHERE `region_id` = ? LIMIT {$n->start()}, 15", array(num($_SESSION['region'])));
        while ($city = $sql->fetch()) {
            echo ' - <a href="' . HOME . '/reg/5?city=' . $city['city_id'] . '">' . $city['name'] . '</a><br />';
        }
        echo CLOSE_DIV;
        echo $n->navi();

        break;

    case '6':
        head('' . $lng['Ro`yhatdan o`tish. 4 - qadam'] . '');

        echo '<div class="tepa">' . $lng['Ro`yhatdan o`tishni tugallash'] . ' ' . CLOSE_DIV;
        if ($_POST['final']) {
            $surname = html($_SESSION['surname']);
            $name = html($_SESSION['name']);
            $nick = html($_SESSION['nick']);
            $email = html($_SESSION['email']);
            $password = md5($_SESSION['password']);
            $gender = num($_SESSION['gender']);
            $country = num($_SESSION['country']);
            $region = num($_SESSION['region']);
            $city = num($_SESSION['city']);


            if (empty($nick)) {
                header("Location: " . HOME . "/index.php");
                exit();
            }

            $ref = (!empty($_SESSION['ref']) ? abs(num($_SESSION['ref'])) : 0);


            if (DB::$dbs->query("INSERT INTO " . USERS . " (`nick`, `email`, `password`, `surname`, `name`, `gender`, `city`, `recording_date`, `balls`, `ref`, `lng`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($nick, $email, $password, $surname, $name, $gender, $city, time(), 500, $ref, $ulng))) {

                $_SESSION['user_id'] = DB::$dbs->lastInsertId();

                unset($_SESSION['surname']);
                unset($_SESSION['name']);
                unset($_SESSION['nick']);
                unset($_SESSION['email']);
                unset($_SESSION['password']);
                unset($_SESSION['gender']);
                unset($_SESSION['country']);
                unset($_SESSION['region']);
                unset($_SESSION['city']);
                unset($_SESSION['ref']);

                /* Ro`yhatdan o`tganga habar jo`natish */
                if (!empty($sett['reg_user']) && !empty($sett['reg_msg'])) {
                    DB::$dbs->query("INSERT INTO " . DIALOG . " SET `user_id` = ?, `friend_id` = ? ", array($sett['reg_user'], abs(num($_SESSION['user_id']))));
                    $last = DB::$dbs->lastInsertId();
                    DB::$dbs->query("INSERT INTO " . DIALOG_MSG . " (`user`,`user_friend`,`msg`,`time`,`status`,`dialog_id`, `delet`) VALUE (?,?,?,?,?,?,?) ", array($sett['reg_user'], abs(num($_SESSION['user_id'])), $sett['reg_msg'], time(), 1, $last, 'no'));
                }

                /* Ro`yhatdan o`tganda sovg`a jo`natish */
                if (!empty($sett['reg_user']) && !empty($sett['reg_present'])) {
                    $gender = user_choice($sett['reg_user'], 'gender');
                    $lenta = '<a href="' . HOME . '/id' . $sett['reg_user'] . '"><b>' . user_choice($sett['reg_user'], 'nick') . '</b></a> ' . $lng['sizga'] . '  <a href="' . HOME . '/present/list/' . abs(num($_SESSION['user_id'])) . '/">' . $lng['sovg`a  jo`natdi'] . '</a>';
                    lenta($lenta, abs(num($_SESSION['user_id'])));
                    DB::$dbs->query("INSERT INTO " . PRESENTS_LIST . " SET `present_id` = ?, `user_id` = ?, `friend_id` = ?, `anonim` = ?, `comm` = ?, `time` = ? ", array($sett['reg_present'], $sett['reg_user'], abs(num($_SESSION['user_id'])), 0, '' . $lng['Saytimizga xush kelibsiz'] . '!', time()));
                }

                header("Location: " . HOME . "/page");

            } else {
                echo DIV_ERROR . '' . $lng['Ro`yhatdan o`tishda xatolik'] . '' . CLOSE_DIV;
            }

        }

        if ($_POST['restart']) {
            unset($_SESSION);
            header("Location: " . HOME . "/index.php");
        }

        echo '<div class="white">' . $lng['Tabriklaymiz. Siz saytimiz ro`yhatidan muvaffaqiyatli o`tdingiz'] . '! </div>';
        echo DIV_AUT . '<form action="' . HOME . '/reg/6" method="POST">';
        echo '<input type="submit" name="final" value="' . $lng['Ro`yhatdan o`tishni tugallash'] . '" />';
        echo '<input type="submit" name="restart" value="' . $lng['Yangidan boshlash'] . '" />';
        echo '</form>' . CLOSE_DIV;
        break;

}

require_once('core/stop.php');