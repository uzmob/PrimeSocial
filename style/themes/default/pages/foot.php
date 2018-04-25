<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

echo '<div class="footer">';
$online = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `last_time` > ? ", array((time() - 2000)));
echo '<a href="' . HOME . '/online/">' . $lng['Onlayn'] . ': ' . $online . '</a> / ' . count_users() . '';
echo '</div>';
echo '</body></html>';