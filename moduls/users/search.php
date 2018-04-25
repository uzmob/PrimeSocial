<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('../../core/start.php');

check_auth();

head('' . $lng['Foydalanuvchilarni izlash'] . '');

if (empty($_POST['q'])) {
    echo DIV_BLOCK . '' . $lng['So`rov bo`sh'] . '' . CLOSE_DIV;
} else {

    if (!empty($_POST['type'])) {
        $q = abs(num($_POST['q']));

        $ank = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array($q));

        if (!empty($ank)) {
            echo DIV_BLOCK . '' . $lng['Topilgan foydalanuvchilar'] . ': <b>' . userLink($ank['user_id']) . '</b>' . CLOSE_DIV;
        } else {
            echo DIV_BLOCK . '' . $lng['Foydalanuvchilar topilmadi'] . '' . CLOSE_DIV;
        }
    } else {
        echo DIV_LI . '' . $lng['Izlash natijalari'] . ':' . CLOSE_DIV;
        $q = html($_POST['q']);

        $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `nick` LIKE '%" . $q . "%' ORDER BY `user_id` DESC");

        if ($all > 0) {
            $n = new Navigator($all, 10, '');
            $sql = DB::$dbs->query("SELECT * FROM " . USERS . " WHERE `nick` LIKE '%" . $q . "%' ORDER BY `user_id` DESC LIMIT {$n->start()}, 10");

            while ($ank = $sql->fetch()) {
                echo DIV_BLOCK . '<b>' . userLink($ank['user_id']) . '</b><br />' . $ank['name'] . ', (' . (!empty($ank['age']) ? '' . $lng['Yosh'] . ': ' . $ank['age'] : '' . $lng['Yosh ko`rsatilmagan'] . '') . ') <br />
				' . city($ank['city']) . CLOSE_DIV;
            }
            echo $n->navi();
        } else {
            echo DIV_BLOCK . '' . $lng['Natijalar bo`sh'] . '' . CLOSE_DIV;
        }
    }

}

echo '<div class="grey">';
echo '<form action="' . HOME . '/users/search/" enctype="multipart/form-data" method="POST">';
echo '<input type="text" name="q" value="' . (!empty($_POST['q']) ? html($_POST['q']) : NULL) . '" /><br />
' . $lng['ID orqali izlash'] . ': <input type="checkbox" name="type" value="1" /><br />';
echo '<input type="submit" name="search" value="' . $lng['Izlash'] . '" /></form>';
echo CLOSE_DIV;
require_once('../../core/stop.php');