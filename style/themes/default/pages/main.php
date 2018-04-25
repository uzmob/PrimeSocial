<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

if (empty($_GET['id'])) {
    $page = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_SESSION['user_id'])));
} else {
    $page = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_GET['id'])));
}

if ($user) {
    echo '<div class="tepa">';
    echo '<center><form action="' . HOME . '/search/" method="post">
<input name="sql" placeholder="' . $lng['Nimani izlaymiz'] . '?" style="width:60%;"/> 
<input type="submit" value="' . $lng['Izlash'] . '"/></form>';
    echo '</center></div>';

    $sql = html($_POST['sql']);
    if (empty($sql)) {
        $sql = html($_SESSION['search']);
    } else {
        $_SESSION['search'] = $sql;
    }


    echo '<div class="tepa">';
    echo '<a href="' . HOME . '/page">- ' . $lng['Mening sahifam'] . '</a>';
    echo '</div>';

    /* Lenta */
    $lenta = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . LENTA . " WHERE `user_id` = ? AND `status` = ? ", array($user['user_id'], 1));
    echo '<div class="tepa">';
    echo '<a href="' . HOME . '/lenta/">- ' . $lng['Tasma'] . '   ' . ($lenta > 0 ? ' <b style="font-size:12px;"> +' . $lenta . '</b>' : NULL) . '</a>';
    echo '</div>';

    echo '<div class="tepa">';
    echo '<a href="' . HOME . '/menu">- ' . $lng['Kabinet'] . ' </a>';
    echo '</div>';

    echo '<div class="tepa">';
    echo ' <a href="' . HOME . '/exit">- ' . $lng['Chiqish'] . '</a>';
    echo '</div>';

} else {
    echo '<div class="tepa"><center>';
    echo '<form action="' . HOME . '/search/" method="post">
<input name="sql" placeholder="' . $lng['Nimani izlaymiz'] . '?" style="width:60%;"/> 
<input type="submit" value="' . $lng['Izlash'] . '"/ style="background: #68AEE6;border:none;"></form>';
    echo '</center></div>';

    $sql = html($_POST['sql']);
    if (empty($sql)) {
        $sql = html($_SESSION['search']);
    } else {
        $_SESSION['search'] = $sql;
    }
    echo '<div class="tepa"><a href="' . HOME . '/auth">- ' . $lng['Kirish'] . '</a></div>';
    echo '<div class="tepa"><a href="' . HOME . '/reg">- ' . $lng['Ro`yhatdan o`tish'] . '</a></div>';
}
?>