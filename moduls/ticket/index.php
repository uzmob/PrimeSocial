<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('../../core/start.php');
ini_set('php_flag display_errors', 'on');
ini_set('php_value error_reporting', E_ALL);
check_auth();


$tochCat = array(1 => '' . $lng['Saytdagi xatolik'] . '', 2 => '' . $lng['Saytni takomillashtirish bo`yicha taklif'] . '', 3 => '' . $lng['Foydalanuvchi ustidan shikoyat'] . '', 4 => '' . $lng['Reklamaga shikoyat'] . '');

switch ($select) {

    default:
        head('' . $lng['Tiket'] . '');

        if (!empty($_POST['send'])) {
            $msg = html($_POST['msg']);
            $type = abs(num($_POST['type']));

            $err = array();
            if (empty($msg)) {
                $err[] = '' . $lng['Habar matnini yozing'] . '';
            }

            if (empty($type) || $type > 4) {
                $err[] = '' . $lng['Savol turini tanlang'] . '';
            }

            if (!empty($err)) {
                echo DIV_ERROR;
                foreach ($err AS $value) {
                    echo $value . '<br />';
                }
                echo CLOSE_DIV;
            } else {
                DB::$dbs->query("INSERT INTO " . TOUCH_USER . " (`type`, `time`, `user_id`, `status`, `prioritet`) VALUES (?, ?, ?, ?, ?)", array($type, time(), $user['user_id'], 1, time()));
                $last = DB::$dbs->lastInsertId();
                DB::$dbs->query("INSERT INTO " . TOUCH_MSG . " (`msg`, `touch_id`, `user_id`, `time`, `status`) VALUES (?, ?, ?, ?, ?)", array($msg, $last, $user['user_id'], time(), 1));

                echo DIV_BLOCK;
                echo '' . $lng['Tiket muvaffaqiyatli yaratildi. Endi faqat kutishingiz kerak'] . ' :)<br /><br />
            <a href="' . HOME . '/touch/ticket/' . $last . '/"><b>' . $lng['Tiketga o`tish'] . '</b></a>';
                echo CLOSE_DIV;
            }
        }
        echo '<div class="lines"><form action="#" method="POST">
    <textarea name="msg" style="width:95%;height:5pc;"></textarea>
    <select name="type">
    <option value="0">' . $lng['Savol turi'] . '</option>
    <option value="1">' . $lng['Saytdagi xatolik'] . '</option>
    <option value="2">' . $lng['Saytni takomillashtirish bo`yicha taklif'] . '</option>
    <option value="3">' . $lng['Foydalanuvchi ustidan shikoyat'] . '</option>
    <option value="4">' . $lng['Reklamaga shikoyat'] . '</option>
    </select>
    <input type="submit" name="send" value="' . $lng['Jo`natish'] . '"/>
    </form>
    ' . CLOSE_DIV;
        $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . TOUCH_USER . " WHERE `user_id` = ? ", array($user['user_id']));
        echo DIV_LI . '' . icon('forumlar.png') . ' <a href="' . HOME . '/touch/mytickets/">' . $lng['Mening tiketlarim'] . '</a><span class="count">' . $all . '</span>' . CLOSE_DIV;
        break;

    /* Tiketlar */
    case 'ticket':
        head('' . $lng['Tiketlar'] . '');
        $id = abs(num($_GET['id']));
        $ticket = DB::$dbs->queryFetch("SELECT * FROM " . TOUCH_USER . " WHERE `id` = ?", array($id));

        $err = array();

        if (empty($ticket)) {
            $err[] = '' . $lng['Tiket topilmadi, yoki o`chirilgan'] . '';
        }

        if ($user['user_id'] != $ticket['user_id'] && privilegy('ticket') == FALSE) {
            $err[] = '' . $lng['Kirishda xatolik'] . '';
        }

        if (!empty($err)) {
            echo DIV_ERROR;
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
            echo CLOSE_DIV;
        } else {
            if (isset($_GET['status'])) {
                if ($ticket['status'] == 1) {
                    DB::$dbs->query("UPDATE " . TOUCH_USER . " SET `status` = ? WHERE `id` = ? ", array(0, $ticket['id']));
                } else {
                    DB::$dbs->query("UPDATE " . TOUCH_USER . " SET `status` = ? WHERE `id` = ? ", array(1, $ticket['id']));
                }
            }
            echo DIV_BLOCK . '' . $lng['Tiket holati'] . ': <b>' . ($ticket['status'] == 1 ? '' . $lng['Ochish'] . '' : '' . $lng['Yopish'] . '') . '</b> | <a href="?status">' . ($ticket['status'] == 1 ? '' . $lng['Yopish'] . '' : '' . $lng['Ochish'] . '') . '</a><br />'
                . '' . $lng['Ochilgan vaqti'] . ': <b>' . vrem($ticket['time']) . '</b><br />'
                . '' . $lng['Tiketni ochgan'] . ': ' . user_choice($ticket['user_id'], 'link')
                . CLOSE_DIV;

            if ($ticket['status'] == 1) {
                if (!empty($_POST['add'])) {
                    $msg = html($_POST['msg']);

                    if (empty($msg)) {
                        echo DIV_ERROR . '' . $lng['Bo`sh habar'] . '' . CLOSE_DIV;
                    } else {
                        DB::$dbs->query("INSERT INTO " . TOUCH_MSG . " (`msg`, `touch_id`, `user_id`, `time`, `status`) VALUES (?, ?, ?, ?, ?)", array($msg, $ticket['id'], $user['user_id'], time(), 1));
                        DB::$dbs->query("UPDATE " . TOUCH_USER . " SET `prioritet` = ? WHERE `id` = ? ", array(time(), $ticket['id']));

                        if (privilegy('ticket')) {
                            $lenta = '' . $lng['Sizning habaringizga javob berdilar'] . ' | <a href="' . HOME . '/touch/ticket/' . $ticket['id'] . '/"><b>' . $lng['Tiketda'] . '</b></a> ';
                            lenta($lenta, $ticket['user_id']);
                        }
                        header("Location: ?");
                    }
                }

                echo DIV_AUT . '<form action="#" method="post">
			<textarea name="msg" style="width:95%;height:5pc;"></textarea><br />
			<input type="submit" name="add" value="' . $lng['Yozish'] . '"></form>' . CLOSE_DIV;
            } else {
                echo DIV_BLOCK . '<b>' . $lng['Tiket yopilgan'] . '!</b>' . CLOSE_DIV;
            }
            $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . TOUCH_MSG . " WHERE `touch_id` = ? ", array($ticket['id']));

            if (empty($all)) {
                echo DIV_BLOCK . '' . $lng['Habarlar yo`q'] . '' . CLOSE_DIV;
            } else {

                $n = new Navigator($all, 5, 'select=ticket&id=' . $ticket['id']);
                $sql = DB::$dbs->query("SELECT * FROM " . TOUCH_MSG . " WHERE `touch_id` = ? ORDER BY `time` DESC LIMIT {$n->start()}, 5", array($ticket['id']));

                while ($msg = $sql->fetch()) {
                    echo DIV_BLOCK . user_choice($msg['user_id'], 'link') . ' <span class="count">' . vrem($msg['time']) . '</span><br />'
                        . text($msg['msg'])
                        . CLOSE_DIV;

                    if ($msg['status'] == 1 && privilegy('ticket')) {
                        DB::$dbs->query("UPDATE " . TOUCH_MSG . " SET `status` = ? WHERE `id` = ? ", array(0, $msg['id']));
                    }
                }
                echo $n->navi();
            }
        }
        break;

    case 'mytickets':
        head('' . $lng['Mening tiketlarim'] . '');

        $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . TOUCH_USER . " WHERE `user_id` = ? ", array($user['user_id']));

        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Tiketlar yo`q'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, 5, '');
            $sql = DB::$dbs->query("SELECT * FROM " . TOUCH_USER . " WHERE `user_id` = ? ORDER BY `time` DESC LIMIT {$n->start()}, 5", array($user['user_id']));
            while ($ticket = $sql->fetch()) {
                echo DIV_BLOCK . '' . vrem($ticket['time']) . ' / <a href="' . HOME . '/touch/ticket/' . $ticket['id'] . '/"><b>' . $tochCat[$ticket[type]] . '</b></a> ' . ($ticket['status'] == 1 ? '[' . $lng['Ochish'] . ']' : '[' . $lng['Yopish'] . ']') . CLOSE_DIV;
            }
            echo $n->navi();
        }
        break;
    /* *** */

}
require_once('../../core/stop.php');