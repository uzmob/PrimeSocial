<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Prime Social / BoB / Mening Prime Social da tuzgan birinchi saytim!">
    <meta name="keywords" content="Prime Social / BoB">
    <meta name="author" content="BoB">
    <title><? echo '' . $title . ''; ?></title>
    <script type="text/javascript" src="/style/themes/<? echo '' . $styler . ''; ?>/js/jquery.js"></script>
    <script type="text/javascript" src="/style/themes/<? echo '' . $styler . ''; ?>/js/main.js"></script>
    <link rel="shortcut icon" href="/style/themes/<? echo '' . $styler . ''; ?>/favicon.ico"/>
    <link rel="stylesheet" href="/style/themes/<? echo '' . $styler . ''; ?>/css/style.css" type="text/css"/>
</head>
<?php

if (!empty($user)) {
    DB::$dbs->query("UPDATE " . USERS . " SET `location` = ? WHERE `user_id` = ? ", array($title, num($_SESSION['user_id'])));
}

if ($user) {
    /* Pochta */
    $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . DIALOG_MSG . " WHERE `user_friend` = ? AND `status` = ? ", array($user['user_id'], 1));
    /* Lenta */
    $lenta = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . LENTA . " WHERE `user_id` = ? AND `status` = ? ", array($user['user_id'], 1));

    echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
    echo '<td style="font-size:11px;">';
    echo '<a id="up" href="#" class="title">' . icon('upmenu.png', 35, 35) . '   ' . ($lenta > 0 ? ' <span>' . $lenta . '</span>' : NULL) . '</a></td>';

    echo '<td style="font-size:11px;"><center>';
    echo '<a href="/" class="title">' . icon('logo.png', 130, 35) . '</a>';
    echo '</center></td>';

    echo '<td style="font-size:11px;text-align:right;">';
    echo '<a href="' . HOME . '/mail/" class="title"> ' . icon('upmsg.png', 35, 35) . '  ' . ($all > 0 ? ' <span>' . $all . '</span>' : NULL) . ' </a>';
    echo "</td></tr></table>";

    echo '<div id="hide" style="padding:0;">';
    require_once(inc . 'style/themes/' . $styler . '/pages/main.php');
    echo '</div>';

    if ($_SERVER['PHP_SELF'] != '/index.php') {
        echo '<div class="tepa">';
        echo "<a href='/page' title='" . $lng['Sahifam'] . "'><u>" . $lng['Man'] . "</u></a> / ";
        echo '<a href="/"><u>' . $lng['Bosh sahifa'] . '</u></a> / ';
        echo ' ' . $title . '';
        echo '</div>';
    }
} else {
    echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
    echo '<td style="font-size:11px;">';
    echo '<a id="up" href="#" class="title">' . icon('upp.png', 35, 35) . '</a></td>';

    echo '<td style="font-size:11px;"><center>';
    echo '<a href="/" class="title">' . icon('logo.png', 100, 35) . '</a>';
    echo '</center></td>';

    echo '<td style="font-size:11px;text-align:right;">';
    echo '<a href="' . HOME . '/faq/" class="title"> ' . icon('iinfo.png', 35, 35) . '  </a>';
    echo "</td></tr></table>";

    echo '<div id="hide" style="padding:0;">';
    require_once(inc . 'style/themes/' . $styler . '/pages/main.php');
    echo '</div>';

    if ($_SERVER['PHP_SELF'] != '/index.php') {
        echo '<div class="tepa">';
        echo '<a href="/"><u>' . $lng['Bosh sahifa'] . '</u></a> / ';
        echo ' ' . $title . '';
        echo '</div>';
    }
}