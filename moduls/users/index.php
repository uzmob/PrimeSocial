<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('../../core/start.php');

switch ($select) {
    default:
        head('' . $lng['Foydalanuvchilar'] . '');

        if (!empty($_GET['sort'])) {
            if ($_GET['sort'] == 'man') {
                $_SESSION['sort'] = 'man';
            } else {
                $_SESSION['sort'] = 'woman';
            }
        }


        echo DIV_BLOCK;
        echo '<a href="?select=24">' . $lng['Yangilar'] . '</a> / ';
        echo (empty($_SESSION['sort']) || $_SESSION['sort'] == 'man' ? '<b> ' . $lng['Yigitlar'] . ' </b> / ' : '<a href="?sort=man"> ' . $lng['Yigitlar'] . ' </a> / ') . ' ' . ($_SESSION['sort'] == 'woman' ? '<b> ' . $lng['Qizlar'] . '</b>' : '<a href="?sort=woman">' . $lng['Qizlar'] . '</a>');
        echo CLOSE_DIV;

        if (empty($_SESSION['sort']) || $_SESSION['sort'] == 'man') {
            $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `gender` = '1' ORDER BY `user_id` DESC");
            $gender = array('' . $lng['Yigitlar'] . '', '' . $lng['Yigitlar2'] . '');
        } else {
            $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `gender` = '0' ORDER BY `user_id` DESC");
            $gender = array('' . $lng['Qizlar'] . '', '' . $lng['Qizlar2'] . '');
        }

        if (empty($all)) {
            echo DIV_BLOCK . $gender[1] . ' ' . $lng['yo`q'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, 10, '');
            if (empty($_SESSION['sort']) || $_SESSION['sort'] == 'man') {
                $sql = DB::$dbs->query("SELECT * FROM " . USERS . " WHERE `gender` = '1' ORDER BY `user_id` DESC LIMIT {$n->start()}, 10");
            } else {
                $sql = DB::$dbs->query("SELECT * FROM " . USERS . " WHERE `gender` = '0' ORDER BY `user_id` DESC LIMIT {$n->start()}, 10");
            }

            while ($ank = $sql->fetch()) {
                echo DIV_BLOCK . '<b>' . userLink($ank['user_id']) . '</b><br />
		' . $ank['name'] . ', (' . (!empty($ank['age']) ? '' . $lng['Yosh'] . ': ' . $ank['age'] : '' . $lng['Yosh ko`rsatilmagan'] . '') . ') <br />' . city($ank['city']) . CLOSE_DIV;
            }
            echo $n->navi();
        }

        echo '<div class="white">';
        echo '<form action="' . HOME . '/users/search/" enctype="multipart/form-data" method="POST">';
        echo '<input type="text" name="q"  placeholder="' . $lng['Foydalanuvchilarni izlash'] . '" /> <input type="submit" name="search" value="' . $lng['Izlash'] . '" /><br />';
        echo ' ' . $lng['ID orqali izlash'] . ': <input type="checkbox" name="type" value="1" /></form>';
        echo CLOSE_DIV;

        break;

    case '24':

        head('' . $lng['Yangi foydalanuvchilar'] . ' ');

        $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `recording_date` > '" . (time() - 86400) . "'");

        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Yangilar yo`q'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, 10, '');
            $sql = DB::$dbs->query("SELECT `user_id`, `age`, `name`, `ref`, `city` FROM " . USERS . " WHERE `recording_date` > '" . (time() - 86400) . "' ORDER BY `recording_date` DESC LIMIT {$n->start()}, 10");

            while ($ank = $sql->fetch()) {

                if (!empty($ank['ref'])) {
                    $ref = DB::$dbs->queryFetch("SELECT `user_id` FROM " . USERS . " WHERE `user_id` = ?", array($ank['ref']));
                }
                echo DIV_BLOCK . '<b>' . userLink($ank['user_id']) . '</b><br />' . $ank['name'] . ', (' . (!empty($ank['age']) ? '' . $lng['Yosh'] . ': ' . $ank['age'] : '' . $lng['Yosh ko`rsatilmagan'] . '') . ') <br />' . city($ank['city'])
                    . (!empty($ref) ? '<br /><b>' . $lng['Taklif qildi'] . ': ' . userLink($ref['user_id']) : NULL)
                    . CLOSE_DIV;
                $ref = 0;
            }
            echo $n->navi();
        }
}
require_once('../../core/stop.php');