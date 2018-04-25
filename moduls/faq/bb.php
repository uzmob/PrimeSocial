<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');



head(''. $lng['BB kodlar'] .'');


echo DIV_LI . ''. $lng['Asosiy'] .'' . CLOSE_DIV;
echo DIV_BLOCK;
echo text('[b]'. $lng['Qalin matn'] .'[/b]') . ' <br/> <input type="text" value="[b]'. $lng['Qalin matn'] .'[/b]" /><br />';
echo text('[i]'. $lng['Egri matn'] .'[/i]') . ' <br/> <input type="text" value="[i]'. $lng['Egri matn'] .'[/i]" /><br />';
echo text('[u]'. $lng['Chizilgan matn'] .'[/u]') . ' <br/> <input type="text" value="[u]'. $lng['Chizilgan matn'] .'[/u]" /><br />';
echo text('[big]'. $lng['Katta matn'] .'[/big]') . ' <br/> <input type="text" value="[big]'. $lng['Katta matn'] .'[/big]" /><br />';
echo text('[small]'. $lng['Kichik matn'] .'[/small]') . ' <br/> <input type="text" value="[small]'. $lng['Kichik matn'] .'[/small]" /><br />';
echo text('[red]'. $lng['Qizil matn'] .'[/red]') . ' <br/> <input type="text" value="[red]'. $lng['Qizil matn'] .'[/red]" /><br />';
echo text('[yellow]'. $lng['Sariq matn'] .'[/yellow]') . ' <br/> <input type="text" value="[yellow]'. $lng['Sariq matn'] .'[/yellow]" /><br />';
echo text('[green]'. $lng['Yashil matn'] .'[/green]') . ' <br/> <input type="text" value="[green]'. $lng['Yashil matn'] .'[/green]" /><br />';
echo text('[blue]'. $lng['Ko`k matn'] .'[/blue]') . ' <br/> <input type="text" value="[blue]'. $lng['Ko`k matn'] .'[/blue]" /><br />';
echo CLOSE_DIV;

echo DIV_LI . ''. $lng['Qo`shimchalar'] .'' . CLOSE_DIV;
echo DIV_BLOCK;
echo text('<a href="http://primesocial.ru">'. $lng['Link'] .'</a>') . ' <br/> <input type="text" value="[url=http://primesocial.ru]'. $lng['Link'] .'[/url]" /><br />';
echo text('<div class="cit" style="margin-bottom:5px;">'. $lng['Sitata'] .'</div>') . ' <input type="text" value="[sit]'. $lng['Sitata'] .'[/sit]" /><br />';
echo text(''. $lng['Rasm'] .' <img src="/style/themes/default/favicon.ico" style="width:45px;">') . '<br/><input type="text" value="[img]http://site.uz/img.png[/img]" /><br />';
echo text('[c]'. $lng['O`rtada'] .'[/c]') . ' <br/> <input type="text" value="[c]'. $lng['Matn'] .'[/c]" /><br />';
echo text('[right]'. $lng['O`ngda'] .'[/right]') . ' <br/> <input type="text" value="[right]'. $lng['Matn'] .'[/right]" /><br />';
	echo CLOSE_DIV;
echo DIV_BLOCK;
echo text('[google]'. $lng['Google dan izlash'] .'[/google]') . ' <br/> <input type="text" value="[google]'. $lng['Google dan izlash'] .'[/google]" /><br />';
echo text('[googleimg]'. $lng['Google dan rasm izlash'] .'[/googleimg]') . ' <br/> <input type="text" value="[googleimg]'. $lng['Google dan rasm izlash'] .'[/googleimg]" /><br />';
echo CLOSE_DIV;

require_once('../../core/stop.php');
?>