<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


if (!file_exists(inc . '/style/themes/' . $user['style'] . '/pages/foot.php')) {
    $styler = 'default';
} else {
    $styler = $user['style'];
}
require_once(inc . '/style/themes/' . $styler . '/pages/foot.php');
copyright();
if (!$user) {

    echo '<br /><span style="float:right;padding-right:10px;"><form action="" method="get"><select name="lng" onchange="this.form.submit()">';

    foreach ($clng as $key => $value) echo '<option value="' . $key . '"' . ($key == $ulng ? ' selected' : NULL) . '>' . $value . '</option>';

    echo '</select></form></span>';
}