<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('core/start.php');
head();

switch ($select) {

    default:
        require_once(inc . 'style/themes/' . $styler . '/pages/index.php');
        break;

    case 'auth':
        echo '<div class="tepa">';
        echo '' . $lng['Xush kelibsiz'] . '! ';
        echo '</div>';
        echo '<div class="grey">';
        echo '' . $lng['Kirish so`zi'] . '! ';
        echo '</div>';
        echo '<div class="white">';
        echo '<form action="/auth.php" method="POST"><span style="margin-left:4px;">' . $lng['Login'] . ':</span><br />
	            <input style="width:95%;" type="text" name="nick" /><br />
	            <span style="margin-left:4px;">' . $lng['Parol'] . ':</span><br />
	            <input style="width:95%;" type="password" name="password" /><br />
	            <input type="checkbox" name="remember" value="1" checked/> ' . $lng['Saqlash'] . '<br/>
	            <input type="submit" name="auth" value="' . $lng['Profilga kirish'] . '"/></form>';
        echo '</div>';
        break;
}

require_once('core/stop.php');
