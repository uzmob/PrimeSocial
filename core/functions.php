<?php

/*
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      Archi | http://primesocial.ru/about
*/


function icon($msg, $w = "16", $h = "16", $align = "middle")
{
    global $user;
    $root_them = !empty($user) ? $user['style'] : 'default';
    return "<img src='/style/themes/{$root_them}/ico/{$msg}' width='{$w}' height='{$h}' align='{$align}' alt='Prime Social' style='vertical-align:middle;'/>";
}

function head($title = 'Prime Social v2.2 Beta')
{
    global $user, $lng;
    if (!file_exists(inc . 'style/themes/' . $user['style'] . '/pages/head.php')) {
        $styler = 'default';
    } else {
        $styler = $user['style'];
    }
    require_once(inc . 'style/themes/' . $styler . '/pages/head.php');

    if (!empty($user)) {
        DB::$dbs->query("UPDATE " . USERS . " SET `location` = ? WHERE `user_id` = ? ", array($title, num($_SESSION['user_id'])));
    }


}


function lenta($text, $id)
{

    global $user;

    if ($user['user_id'] != $id) {
        DB::$dbs->query("INSERT INTO " . LENTA . " (`user_id`, `time`, `text`, `status`) VALUES (?, ?, ?, ?)", array($id, time(), $text, 1));
    }
}

function balls_operation($balls)
{

    global $user;

    DB::$dbs->query("UPDATE " . USERS . " SET `balls` = ? WHERE `user_id` = ? ", array(($user['balls'] + $balls), $user['user_id']));

}

function rating_operation($rating)
{

    global $user;

    DB::$dbs->query("UPDATE " . USERS . " SET `rating` = ? WHERE `user_id` = ? ", array(($user['rating'] + $rating), $user['user_id']));

}

function num($int)
{

    return intval($int);
}

function html($str)
{

    return trim(strtr(htmlspecialchars(stripcslashes($str), ENT_QUOTES, 'UTF-8'), array('$' => '$', '%' => '%', '_' => '_')));

}

function color_nick($id)
{

    global $color, $gradient_1, $gradient_2;

    $ank = DB::$dbs->queryFetch("SELECT `nick`, `color_nick` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if (empty($ank['color_nick'])) {
        $nick = $ank['nick'];
    } else {
        $data = explode(":", $ank['color_nick']);

        if ($data[0] == 'color') {
            $nick = '<font color="' . $color[$data[1]] . '">' . $ank['nick'] . '</font>';
        } else {
            $nick = GradientLetter($ank['nick'], $gradient_1[$data[1]], $gradient_2[$data[1]]);
        }
    }

    return '<font style="font-size: 13px;">' . $nick . '</font>';
}

function user_choice($id, $choice)
{

    if ($choice == 'name') {
        $result = DB::$dbs->queryFetch("SELECT `name` FROM " . USERS . " WHERE `user_id` = ?", array(num($id)));
        return $result['name'];
    }

    if ($choice == 'nick') {
        $result = DB::$dbs->queryFetch("SELECT `nick` FROM " . USERS . " WHERE `user_id` = ?", array(num($id)));
        return $result['nick'];
    }

    if ($choice == 'gender') {
        $result = DB::$dbs->queryFetch("SELECT `gender` FROM " . USERS . " WHERE `user_id` = ?", array(num($id)));
        return $result['gender'];
    }

    if ($choice == 'link') {
        $result = DB::$dbs->queryFetch("SELECT `user_id`, `nick`, `gender` FROM " . USERS . " WHERE `user_id` = ?", array(num($id)));
        return onlineIcon($result['user_id']) . ' <a href="' . HOME . '/id' . $result['user_id'] . '">' . color_nick($result['user_id']) . '</a>';
    }
}

function check_auth()
{
    if (empty($_SESSION['user_id'])) {
        header("Location: " . HOME . "/auth");
    }
    return false;
}

function gender($gender)
{
    global $lng;
    if ($gender == 0) {
        return '' . $lng['Ayol'] . '';
    } else {
        return '' . $lng['Erkak'] . '';
    }
}

function getIP2()
{
    if (isset($_ENV["HTTP_VIA"]) && $_ENV["HTTP_VIA"]) {
        if ($_ENV["HTTP_X_FORWARDED_FOR"] != "unknown") {
            $ip = $_ENV["HTTP_X_FORWARDED_FOR"];
            if ($ip == "") {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    return $ip;
}

function city($id)
{

    $city = DB::$dbs->queryFetch("SELECT * FROM " . CITY . " WHERE `city_id` = ? ", array($id));
    $region = DB::$dbs->queryFetch("SELECT `name` FROM " . REGION . " WHERE `region_id` = ? ", array($city['region_id']));
    $country = DB::$dbs->queryFetch("SELECT `name` FROM " . COUNTRY . " WHERE `country_id` = ? ", array($city['country_id']));

    return '<b>' . $city['name'] . '</b> (' . $region['name'] . ', ' . $country['name'] . ')';

}

function gen_timer_start()
{
    global $timer_start_time;
    $start_time = microtime();
    $start_array = explode(" ", $start_time);
    $timer_start_time = $start_array[1] + $start_array[0];
    return $timer_start_time;
}

function gen_timer_stop()
{
    global $timer_start_time, $lng;
    $end_time = microtime();
    $end_array = explode(" ", $end_time);
    $timer_stop_time = $end_array[1] + $end_array[0];
    $time = $timer_stop_time - $timer_start_time;
    $time = substr($time, 0, 5);
    return "[$time " . $lng['soniya'] . "]";
}

function vrem($time = '', $format = 'd.m.y / H:i')
{
    global $lng;

    defined('SITE_TIME') or define("SITE_TIME", time());

    if (!is_numeric($time)) {
        $time = SITE_TIME;
    }
    $shift = 0;
    $date = date($format, $time + $shift);
    $today = date("d.m.y", SITE_TIME + $shift);
    $yesterday = date("d.m.y", strtotime("-1 day") + $shift);
    $date = str_replace($today, '' . $lng['Bugun'] . '', $date);
    $date = str_replace($yesterday, '' . $lng['Kecha'] . '', $date);
    $search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $replace = array('' . $lng['Yanvar'] . '', '' . $lng['Fevral'] . '', '' . $lng['Mart'] . '', '' . $lng['Aprel'] . '', '' . $lng['May'] . '', '' . $lng['Iyun'] . '', '' . $lng['Iyul'] . '', '' . $lng['Avgust'] . '', '' . $lng['Sentyabr'] . '', '' . $lng['Oktyabr'] . '', '' . $lng['Noyabr'] . '', '' . $lng['Dekabr'] . '');
    $date = str_replace($search, $replace, $date);
    return $date;

}

function level($id)
{
    global $lng;

    $pos = DB::$dbs->queryFetch("SELECT * FROM " . POSITIONS . " WHERE `id` = ? ", array($id));

    if (empty($pos['id'])) {
        return '' . $lng['Foydalanuvchi'] . '';
    } else {
        return '' . icon('level.png') . ' ' . $pos['position'] . '';
    }

}

function privilegy($privilegy, $id = NULL)
{

    global $user;

    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . PRIVILEGE . " WHERE `pos` = ? && `privil` = ?", array($user['level'], $privilegy));

    if (!empty($id)) {
        $ank = DB::$dbs->queryFetch("SELECT `level` FROM " . USERS . " WHERE `user_id` = ? ", array($id));
        $pos = DB::$dbs->queryFetch("SELECT `prioritet` FROM " . POSITIONS . " WHERE `id` = ? ", array($ank['level']));

        $mypos = DB::$dbs->queryFetch("SELECT `prioritet` FROM " . POSITIONS . " WHERE `id` = ? ", array($user['level']));
        if ($sql == TRUE) {
            if ($pos['prioritet'] >= $mypos['prioritet']) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    } else {
        return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . PRIVILEGE . " WHERE `pos` = ? && `privil` = ?", array($user['level'], $privilegy));
    }
}

function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 85)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);

    if ($size === false) return false;


    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;

}

function calc_age($data)
{
    $ex_age = explode('/', $data);
    $age = date('Y') - $ex_age[2];
    if (date('m') < $ex_age[1] || (date('m') == $ex_age[1] && date('d') < $ex_age[0])) {
        $age--;
    }
    $age = (int)$age;

    if ($age == date('Y')) $age = 0;
    return $age;
}

function text($str)
{

    $str = smiles($str);

    $str = BBcode($str);

    $str = nl2br($str);

    return $str;
}

function bbsmile()
{
    echo '<span style="float:right;padding-right:10px;"><a href="' . HOME . '/smiles">' . icon('smile.png') . ' </a>  
	<a href="' . HOME . '/bb">' . icon('text.png') . ' </a></span>';
}


function userLink($id, $link = NULL)
{

    $user = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    $url = (empty($link) || $link == 'page' ? onlineIcon($user['user_id']) . ' ' : NULL) . '<a href="' . HOME . '/id' . $id . '">' . (empty($link) ? color_nick($user['user_id']) : $link) . '</a>';
    return $url;

}

function smiles($str)
{
    $sql = DB::$dbs->query("SELECT `url`, `name` FROM " . SMILES . "");
    while ($smile = $sql->fetch()) {
        $str = str_replace($smile['name'], '<img src="' . HOME . '/files/smiles/' . $smile['url'] . '" alt="' . $smile['name'] . '"/>', $str);
    }
    return $str;
}

function BBcode($msg)
{
    global $lng;

    $bbcode = array();
    $bbcode['/\[i\](.+)\[\/i\]/isU'] = '<em>$1</em>';
    $bbcode['/\[b\](.+)\[\/b\]/isU'] = '<strong>$1</strong>';
    $bbcode['/\[u\](.+)\[\/u\]/isU'] = '<span style="text-decoration:underline;">$1</span>';
    $bbcode['/\[big\](.+)\[\/big\]/isU'] = '<span style="font-size:large;">$1</span>';
    $bbcode['/\[small\](.+)\[\/small\]/isU'] = '<span style="font-size:small;">$1</span>';
    $bbcode['/\[red\](.+)\[\/red\]/isU'] = '<span style="color:#ff0000;">$1</span>';
    $bbcode['/\[yellow\](.+)\[\/yellow\]/isU'] = '<span style="color:#ffff22;">$1</span>';
    $bbcode['/\[green\](.+)\[\/green\]/isU'] = '<span style="color:#00bb00;">$1</span>';
    $bbcode['/\[blue\](.+)\[\/blue\]/isU'] = '<span style="color:#0000bb;">$1</span>';
    $bbcode['/\[white\](.+)\[\/white\]/isU'] = '<span style="color:#ffffff;">$1</span>';
    $bbcode['/\[size=([0-9]+)\](.+)\[\/size\]/isU'] = '<span style="font-size:$1px;">$2</span>';
    $bbcode['/\[right\](.+?)\[\/right\]/isU'] = '<span style="float:right;padding-right:10px;">$1</span>';
    $bbcode['/\[url=(.*?)\\](.*?)\\[\/url\\]/isU'] = '<a href="$1" target=\"_blank\">$2</a>';
    $bbcode['/\[img\](.+?)\[\/img\]/isU'] = '<img src="$1" alt="Prime Social" alt="Prime Social" />';
    $bbcode['/\[color=(\#[0-9A-F]{3,6}|[a-z\-]+)\](.*)\[\/color]/isU'] = '<span style="color:$1">$2</span>';
    $bbcode['/\[bcolor=(\#[0-9A-F]{3,6}|[a-z\-]+)\](.*)\[\/bcolor]/isU'] = '<span style="border-radius:2px;padding:3px;background:$1">$2</span>';
    $bbcode['/\[sit\](.+?)\[\/sit\]/isU'] = '<div class="cit">$1</div>';
    $bbcode['/\[c\](.+?)\[\/c\]/isU'] = '<center>$1</center>';
    if (count($bbcode)) $msg = preg_replace(array_keys($bbcode), array_values($bbcode), $msg);

    $msg = preg_replace('#\[google\](.*?)\[/google\]#si', '<a href="http://www.google.ru/search?q=$1">' . $lng['Google dan izlash'] . ' <span style="color:#0000ff">G</span><span style="color:#ff0000">o</span><span style="color:#FFD700">o</span><span style="color:#0000ff">g</span><span style="color:#008000">l</span><span style="color:#ff0000">e</span>: <i>$1</i></a> ', $msg);
    $msg = preg_replace('#\[googleimg\](.*?)\[/googleimg\]#si', '<a href="http://www.google.com.ua/images?q=$1">' . $lng['Google dan rasm izlash'] . ' <span style="color:#0000ff">G</span><span style="color:#ff0000">o</span><span style="color:#FFD700">o</span><span style="color:#0000ff">g</span><span style="color:#008000">l</span><span style="color:#ff0000">e</span>: <i>$1</i></a> ', $msg);

    return $msg;
}

function get_size($size)
{

    if ($size < 1024) $size = $size . 'Bt';
    if ($size > 1024 and $size < 1048576) $size = round($size / 1024, 1) . 'Kb';
    if ($size >= 1048576) $size = round(($size / 1024) / 1024, 1) . 'Mb';

    return $size;
}

function SubstrMaus($text, $len, $start = 0, $mn = ' ...')
{
    $text = trim($text);
    if (function_exists('mb_substr')) {
        return mb_substr($text, $start, $len) . (mb_strlen($text) > $len - $start ? $mn : null);
    }
    if (function_exists('iconv')) {
        return iconv_substr($text, $start, $len) . (iconv_strlen($text) > $len - $start ? $mn : null);
    }

    return $text;
}

# Anketa 
function smok($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `gender`, `smok` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($user['gender'] == 0) {
        $array = array(1 => '' . $lng['Chekmaganman'] . '', 2 => '' . $lng['Tashaganman'] . '', 3 => '' . $lng['Tashayapman'] . '', 4 => '' . $lng['Faqat ichgan paytda'] . '', 5 => '' . $lng['Paravozdak tutaman'] . '',
            6 => '' . $lng['Chekaman'] . '');
    } else {
        $array = array(1 => '' . $lng['Chekmaganman'] . '', 2 => '' . $lng['Tashaganman'] . '', 3 => '' . $lng['Tashayapman'] . '', 4 => '' . $lng['Faqat ichgan paytda'] . '', 5 => '' . $lng['Paravozdak tutaman'] . '',
            6 => '' . $lng['Chekaman'] . '');
    }
    return $array[$user[smok]];
}

function alco($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `gender`, `alco` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($user['gender'] == 0) {
        $array = array(1 => '' . $lng['Ichmaganman'] . '', 2 => '' . $lng['Tashaganman'] . '', 3 => '' . $lng['Mumkun'] . '', 4 => '' . $lng['Tez tashlayman'] . '', 5 => '' . $lng['Ichaman'] . '');
    } else {
        $array = array(1 => '' . $lng['Ichmaganman'] . '', 2 => '' . $lng['Tashaganman'] . '', 3 => '' . $lng['Mumkun'] . '', 4 => '' . $lng['Tez tashlayman'] . '', 5 => '' . $lng['Ichaman'] . '');
    }
    return $array[$user[alco]];
}

function narco($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `gender`, `narco` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($user['gender'] == 0) {
        $array = array(1 => '' . $lng['Aql va ijod'] . '', 2 => '' . $lng['Rahmdillik va rostgo`ylik'] . '', 3 => '' . $lng['Tiket'] . 'Boylik va kuchlilik', 4 => '' . $lng['Jasurlik va qat`iyat'] . '', 5 => '' . $lng['Hazilkashlik va hayotni sevish'] . '',
            6 => '' . $lng['Chiroy va sog`lomlik'] . '');
    } else {
        $array = array(1 => '' . $lng['Aql va ijod'] . '', 2 => '' . $lng['Rahmdillik va rostgo`ylik'] . '', 3 => '' . $lng['Tiket'] . 'Boylik va kuchlilik', 4 => '' . $lng['Jasurlik va qat`iyat'] . '', 5 => '' . $lng['Hazilkashlik va hayotni sevish'] . '',
            6 => '' . $lng['Chiroy va sog`lomlik'] . '');
    }
    return $array[$user[narco]];
}

function poznakom($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `poznakom`, `age1`, `age2` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    $array = array(1 => '' . $lng['Qiz bola bilan'] . '', 2 => '' . $lng['O`g`il bola bilan'] . '');

    return $array[$user[poznakom]] . (!empty($user['age1']) && !empty($user['age2']) ? '  ' . $lng['yoshdan2'] . ' ' . $user['age1'] . ' ' . $lng['yoshdan'] . '  ' . $lng['yoshgacha2'] . ' ' . $user['age2'] . ' ' . $lng['yoshgacha'] . '' : NULL);
}

function goal($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `goal` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    $array = array(1 => '' . $lng['Do`stlik'] . '', 2 => '' . $lng['Suhbat'] . '', 3 => '' . $lng['Flirt'] . '', 4 => '' . $lng['Sevgi'] . '', 5 => '' . $lng['Uchrashuv'] . '', 6 => '' . $lng['Jiddiy a`loqa uchun'] . '');

    return $array[$user[goal]];
}

function family_status($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `gender`, `family_status` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($user['gender'] == 0) {
        $array = array(1 => '' . $lng['Bo`shman'] . '', 2 => '' . $lng['Erga chiqqanman'] . '', 3 => '' . $lng['Bandman'] . '', 4 => '' . $lng['Ikkinchi juftimni izlashdaman'] . '', 5 => '' . $lng['Sevganim yo`q'] . '',
            6 => '' . $lng['Hamma vaqt tayyorman'] . '');
    } else {
        $array = array(1 => '' . $lng['Bo`shman2'] . '', 2 => '' . $lng['Uylanganman'] . '', 3 => '' . $lng['Bandman2'] . '', 4 => '' . $lng['Ikkinchi juftimni izlashdaman'] . '', 5 => '' . $lng['Sevganim yo`q'] . '',
            6 => '' . $lng['Hamma vaqt tayyorman'] . '');
    }

    return $array[$user[family_status]];
}

function children($id)
{
    global $lng;
    $user = DB::$dbs->queryFetch("SELECT `children` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    $array = array(1 => '' . $lng['Hali yo`q'] . '', 2 => '' . $lng['Yo`q'] . '', 3 => '' . $lng['Bor'] . '', 4 => '' . $lng['Yo`q, ammo istayman'] . '', 5 => '' . $lng['Ular katta'] . '');

    return $array[$user[children]];
}

function orientation($id)
{
    $user = DB::$dbs->queryFetch("SELECT `orientation` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    $array = array(1 => 'Getero', 2 => 'Gomo', 3 => 'Bi');

    return $array[$user[orientation]];
}

function birthday($day = NULL, $month = NULL, $year = NULL)
{
    global $lng;
    $array = array(1 => '' . $lng['Yanvar'] . '', 2 => '' . $lng['Fevral'] . '', 3 => '' . $lng['Mart'] . '', 4 => '' . $lng['Aprel'] . '', 5 => '' . $lng['May'] . '', 6 => '' . $lng['Iyun'] . '', 7 => '' . $lng['Iyul'] . '', 8 => '' . $lng['Avgust'] . '',
        9 => '' . $lng['Sentyabr'] . '', 10 => '' . $lng['Oktyabr'] . '', 11 => '' . $lng['Noyabr'] . '', 12 => '' . $lng['Dekabr'] . '');

    return $day . ' ' . $array[$month] . ' ' . $year . '' . $lng['yil'] . '.';
}

function GradientLetter($text, $from = '', $to = '', $mode = 'hex')
{
    $text = iconv("UTF-8", "windows-1251", $text);
    if ($mode == 'hex') {
        $to = hexdec($to['0'] . $to['1']) . ',' . hexdec($to['2'] . $to['3']) . ',' . hexdec($to['4'] . $to['5']);
        $from = hexdec($from['0'] . $from['1']) . "," . hexdec($from['2'] . $from['3']) . "," . hexdec($from['4'] . $from['5']);
    }
    if (empty($text)) return null; else $levels = strlen($text);
    if (empty($from)) $from = array(0, 0, 255); else $from = explode(',', $from);
    if (empty($to)) $to = array(255, 0, 0); else $to = explode(',', $to);
    $output = null;
    for ($i = 1; $i <= $levels; $i++) {
        for ($ii = 0; $ii < 3; $ii++) {
            $tmp[$ii] = $from[$ii] - $to[$ii];
            $tmp[$ii] = floor($tmp[$ii] / $levels);
            $rgb[$ii] = $from[$ii] - ($tmp[$ii] * $i);
            if ($rgb[$ii] > 255) $rgb[$ii] = 255;
            $rgb[$ii] = dechex($rgb[$ii]);
            $rgb[$ii] = strtoupper($rgb[$ii]);
            if (strlen($rgb[$ii]) < 2) $rgb[$ii] = '0' . $rgb[$ii];
        }
        $output .= '<span style="color: #' . $rgb['0'] . $rgb['1'] . $rgb['2'] . '">' . $text[$i - 1] . '</span>';
    }
    return iconv("windows-1251", "UTF-8", $output);
}

/* Online statusni kursatish */
function onlineIcon($id)
{
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `last_time`, `gender`, `vip`, `icon` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($ank['last_time'] > (time() - 2000)) {
        if (empty($ank['gender'])) {
            /* Ayol */
            $online = '<img src="' . HOME . '/style/img/user/woman_on.png" />';
        } else {
            /* Erkak */
            $online = '<img src="' . HOME . '/style/img/user/man_on.png" />';
        }
    } else {
        if (empty($ank['gender'])) {
            /* Ayol */
            $online = '<img src="' . HOME . '/style/img/user/woman_off.png" />';
        } else {
            /* Erkak */
            $online = '<img src="' . HOME . '/style/img/user/man_off.png" />';
        }
    }

    /* Vip Status */
    if ($ank['vip'] > time()) {
        if ($ank['last_time'] > (time() - 2000)) {
            if (empty($ank['gender'])) {
                /* Ayol */
                $vip = '<img src="' . HOME . '/style/img/user/vip.gif" />';
            } else {
                /* Erkak */
                $vip = '<img src="' . HOME . '/style/img/user/vip.gif" />';
            }
        } else {
            if (empty($ank['gender'])) {
                /* Ayol */
                $vip = '<img src="' . HOME . '/style/img/user/woman_off.png" />';
            } else {
                /* Erkak */
                $vip = '<img src="' . HOME . '/style/img/user/man_off.png" />';
            }
        }
        return $vip;
    }

    /* Shahsiy ikonka */
    if ($ank['icon']) {
        if ($ank['last_time'] > (time() - 2000)) {
            if (empty($ank['gender'])) {
                /* Ayol */
                $icon = '<img src="' . HOME . '/files/icons_user/' . $ank['icon'] . '" />';
            } else {
                /* Erkak */
                $icon = '<img src="' . HOME . '/files/icons_user/' . $ank['icon'] . '" />';
            }
        } else {
            if (empty($ank['gender'])) {
                /* Ayol */
                $icon = '<img src="' . HOME . '/style/img/user/woman_off.png" />';
            } else {
                /* Erkak */
                $icon = '<img src="' . HOME . '/style/img/user/man_off.png" />';
            }
        }
        return $icon;
    }

    return $online;
}

function mobile_detect()
{
    global $user;
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $accept = strtolower($_SERVER['HTTP_ACCEPT']);
    if ((strpos($accept, 'text/vnd.wap.wml') !== false) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)) {
        DB::$dbs->query("UPDATE " . USERS . " SET `is_mobile` = ? WHERE `user_id` = ?", array(1, $user['user_id']));
        return true;
    }
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        DB::$dbs->query("UPDATE " . USERS . " SET `is_mobile` = ? WHERE `user_id` = ?", array(1, $user['user_id']));
        return true;
    }
    if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $user_agent)
        || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))) {
        DB::$dbs->query("UPDATE " . USERS . " SET `is_mobile` = ? WHERE `user_id` = ?", array(1, $user['user_id']));
        return true;
    }

    DB::$dbs->query("UPDATE " . USERS . " SET `is_mobile` = ? WHERE `user_id` = ?", array(2, $user['user_id']));

    return false;
}

/* Antispam */
function antiSpam($str)
{
    global $lng;

    $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
    # Shahsiy habarlardagi shubxali suzlar
    $mailSpam = array('kiring', 'taklif qilaman', 'sizni kutamiz', 'wap', 'Ijtimoiy tarmoq', 'moderlar', 'zaibal', 'kut', 'уебан');

    foreach ($mailSpam AS $value) {
        if (preg_match("/" . $value . "/i", $str)) {
            return true;
            exit();
        }
    }

    return false;

}


function antilink($str)
{

    $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");

    if (preg_match("/primesocial.ru/i", $str)) {
        return false;
    }

    $domains = array('.com', '.net', '.ru', '.su', '.in', '.biz', '.name', '.mobi', '.wen',
        '. com', '. net', '. ru', '. su', '. in', '. biz', '. name', '. mobi', '. wen',
        'http://', 'www.', 'www .'
    );

    foreach ($domains AS $value) {
        if (preg_match("/" . $value . "/i", $str)) {
            return true;
            exit();
        }
    }

    return false;

}

function copyright()
{
    echo '<span style="padding:8px;font-size:11px;">&copy; <a href="http://primesocial.ru/">Prime Social</a></span>';
}

/* Avatar */
function avatar($id, $w = "48", $h = "48")
{
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `gender`, `photo` FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($ank['photo']) {
        $avatar = '<a href="' . HOME . '/files/photo/' . $ank['photo'] . '"> <img src="' . HOME . '/files/photo/' . $ank['photo'] . '" width="' . $w . '" height="' . $h . '" class="avatar"/></a>';
    } else {
        if (empty($ank['gender'])) {
            /* Ayol */
            $avatar = '<img src="' . HOME . '/style/img/ayol.png" width="' . $w . '" height="' . $h . '" class="avatar"/>';
        } else {
            /* Erkak */
            $avatar = '<img src="' . HOME . '/style/img/erkak.png" width="' . $w . '" height="' . $h . '" class="avatar"/>';
        }

    }
    return $avatar;
}

/* Muqova */
function cover($id)
{
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `gender`, cover FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($ank['cover']) {
        $cover = '<a href="' . HOME . '/files/cover/' . $ank['cover'] . '"> <img src="' . HOME . '/files/cover/' . $ank['cover'] . '" class="cover"/></a>';
    } else {
        if (empty($ank['gender'])) {
            /* Ayol */
            $cover = '<img src="' . HOME . '/style/img/ayolfon.png" class="cover"/>';
        } else {
            /* Erkak */
            $cover = '<img src="' . HOME . '/style/img/erkakfon.png" class="cover"/>';
        }

    }

    return $cover;
}
/* WEB Muqova */
function webcover($id)
{
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `gender`, cover FROM " . USERS . " WHERE `user_id` = ?", array($id));

    if ($ank['cover']) {
        $webcover = '<a href="' . HOME . '/files/cover/' . $ank['cover'] . '"> <img src="' . HOME . '/files/cover/' . $ank['cover'] . '" class="cover"/></a>';
    } else {
        if (empty($ank['gender'])) {
            /* Ayol */
            $webcover = '<img src="' . HOME . '/style/img/ayolfon.png" class="cover"/>';
        } else {
            /* Erkak */
            $webcover = '<img src="' . HOME . '/style/img/erkakfon.png" class="cover"/>';
        }

    }

    return $webcover;
}