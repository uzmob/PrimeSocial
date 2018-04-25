<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/

require_once('core/start.php');

head('' . $lng['Chiqish'] . '');

unset($_SESSION['user_id']);
setcookie('email', '', time() - 30);
setcookie('password', '', time() - 30);

header("Location: " . HOME . "/index.php");

require_once('core/stop.php');