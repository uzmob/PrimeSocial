<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

$online_women = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `gender` = ? && `last_time` > ?", array(0, (time() - 2000)));
$online_man = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `gender` = ? && `last_time` > ?", array(1, (time() - 2000)));
$online_all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `last_time` > ?", array((time() - 2000)));
echo '<div class = "tepa"><center>' . $lng['Onlayn'] . ' <a href="' . HOME . '/online/"> ' . $online_all . '</a> / 
' . $lng['Qizlar2'] . ' <a href="' . HOME . '/tanishuv/search/women/online/"> ' . $online_women . ' </a> / 
' . $lng['Yigitlar2'] . ' <a href="' . HOME . '/tanishuv/search/man/online/"> ' . $online_man . ' </a>
<br/><br/>';
$all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " ORDER BY `balls` DESC LIMIT 3");
if (empty($all)) {
} else {
    $sql = DB::$dbs->query("SELECT * FROM " . USERS . "  ORDER BY `balls` DESC LIMIT 3");
    echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
    while ($ank = $sql->fetch()) {
        echo '<td><table cellspacing="0" cellpadding="0" width="100%" ><tr>';
        echo '<td ><center>';
        echo ' ' . avatar($ank['user_id'], 50, 50) . '';
        echo '<br/>';
        echo '' . user_choice($ank['user_id'], 'link') . '';
        echo '</td></tr></table></td>';
    }
    echo '</tr></table>';
}
echo '</center></div>';

/* Tiket */
if (privilegy('ticket')) {
    $ticket = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . TOUCH_MSG . " WHERE `status` = ?", array(1));
    if ($ticket > 0) {
        echo DIV_BLOCK . '<a href="' . HOME . '/panel/ticket/">' . $lng['Tiket'] . '</a> <b>+' . $ticket . '</b>' . CLOSE_DIV;
    }
}


echo '<div class="white">';
/* 1 Qator */
echo '<table cellspacing="0" cellpadding="0" width="100%"><tr>';
echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/loads/" class="imenu"> <br/>' . icon('ifile.png', 40, 40) . ' <br/> <span>' . $lng['Yuklamalar markazi'] . '</span>
<br/><span class="mini">' . count_loads() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/guestbook/" class="imenu"> <br/>' . icon('ichat.png', 40, 40) . '<br/> <span>' . $lng['Jonli efir'] . '</span>
<br/><span class="mini">' . count_guestbook() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';

echo '<a href="' . HOME . '/tanishuv/" class="imenu"><br/>' . icon('iheart.png', 40, 40) . '<br/> <span>' . $lng['Tanishuv'] . '</span>
 <br/><span class="mini">' . count_users() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';
echo '</tr></table>';


/* 2 Qator */
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/forum/" class="imenu"><br/>' . icon('iforum.png', 40, 40) . '<br/> <span>' . $lng['Forum'] . '</span>
<br/><span class="mini">' . count_forum() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/album/" class="imenu"><br/>' . icon('ifoto.png', 40, 40) . '<br/> <span>' . $lng['Foto'] . '</span>
<br/><span class="mini">' . count_album() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/blog/" class="imenu"><br/> ' . icon('iblog.png', 40, 40) . '<br/> <span>' . $lng['Blog'] . '</span> 
<br/><span class="mini">' . count_blog() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';
echo '</tr></table>';

echo '</div>';


echo '<div class="white">';
echo '<div class="grey" style="margin-left: 10px;margin-right: 10px;border-radius:4px 4px 0 0;"> &nbsp; ' . $lng['Saytimizda nimalar yangi'] . '?</div>';
echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-left: 10px;margin-right: 10px;">';


/* Yangiliklar */
$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . NEWS . "");
if (empty($all)) {
} else {
    echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr><td class="white" width="5%">';
    echo '' . icon('RSS-60.png', 40, 40) . '';
    echo '</td>';

    echo '<td class="white" style="vertical-align:top;" width="95%" >';
    $n = new Navigator($all, $config['write']['news'], '');
    $sql = DB::$dbs->query("SELECT * FROM " . NEWS . " ORDER BY `id` DESC LIMIT 1");

    while ($new = $sql->fetch()) {
        $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . NEWS_COMM . " WHERE `new_id` = ?", array($new['id']));
        echo '<a href="' . HOME . '/news/comm/' . $new['id'] . '/"><b>' . $new['title'] . '</b><br/>';
        echo '<font style="font-size:13px;">' . text($new['afisha']) . '</font><br/>';

    }
    echo '</td></tr></table>';
    $new = DB::$dbs->queryFetch("SELECT * FROM " . NEWS . " ORDER BY `id` DESC LIMIT 1");
    echo '<div class="lines">
          ' . icon('comm.png', 16, 16) . ' <span class="mini">' . $comm . '&nbsp;  &nbsp; &raquo;  &nbsp; ' . $lng['Yangiliklar'] . '</span>  <span class="count">' . vrem($new['time']) . '</span>';
    echo '</a>';
    echo '</div>';
}


//////////////////// Forum mavzulari ////////////////
$sql = html($_SESSION['search']);
$count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUMS_THEME . " WHERE `name` LIKE '%" . $sql . "%'");

if (empty($count)) {
} else {

    $sql = DB::$dbs->query("SELECT * FROM ".FORUMS_THEME." ORDER BY `activ` DESC LIMIT 1");
    while ($theme = $sql->fetch()) {
        $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUMS_POST . " WHERE `theme_id` = ? ", array($theme['id']));
        $page = ceil(($posts / $config['write']['forum_post']));


        echo '<div class="lines">';
        echo '' . avatar($theme['user_id'], 40, 40) . '  ' . userLink($theme['user_id']) . ' 
		<a style="vertical-align:top;margin-left:-40px;" href="' . HOME . '/forum/' . $theme['forum_id'] . '/' . $theme['forumc_id'] . '/' . $theme['id'] . '/?p=' . $page . '">' . $theme['name'] . '</a> <br/>';
        ///echo '<span class="mini" style="vertical-align:top;color:green;">'. $lng['Forumdan'] .'</span> ';
        echo ' ' . icon('comm.png', 16, 16) . ' <span class="mini">' . $posts . '
		&nbsp;  &nbsp; &raquo;  &nbsp; ' . $lng['Forumdan'] . '</span>';
        echo '  <span class="count">' . vrem($theme['time']) . '</span>';
        echo '</div>';
    }

}
//////////////////// Forum mavzulari ////////////////


//////////////////// Yuklamalar /////////////////////
$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LOADS_FILE . " ORDER BY `id` DESC");
if (empty($all)) {
    
} else {
    $n = new Navigator($all, $config['write']['loads_file'], '');
    $sql = DB::$dbs->query("SELECT * FROM " . LOADS_FILE . " ORDER BY `id` DESC LIMIT 1");
    while ($file = $sql->fetch()) {

        echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr><td class="white" width="5%">';
        $screens = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LOADS_SCREEN . " WHERE `file_id` = ? ", array($file['id']));
        if (!empty($screens)) {
            $sql22 = DB::$dbs->query("SELECT * FROM " . LOADS_SCREEN . " ORDER BY `id` DESC LIMIT 1");
            while ($screen = $sql22->fetch()) {
                echo '<img src="' . HOME . '/files/loads/screen/' . $screen['url'] . '" style="height:40px;width:40px;border-radius:55%;"  />';
            }
        } else {
                echo '<img src="' . HOME . '/style/img/noscreen.png" style="height:40px;width:40px;border-radius:55%;"  />';
		}
        echo '</td>';

        echo '<td class="white" style="vertical-align:top;" width="95%" >';
        echo '<a href="' . HOME . '/loads/' . $file['folder_id'] . '/' . $file['folderc_id'] . '/' . $file['id'] . '/">';
        echo '' . icon('zip.png') . ' 
' . $file['name'] . ' ' . $file['type'] . '</a>
<br/><span style="color:#757575;font-size:13px;">' . get_size($file['size']) . '</span>';


        echo '</td></tr></table>';
        echo '<div class="lines">';
        $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LOADS_COMM . " WHERE `file_id` = ?", array($file['id']));
        echo '' . icon('comm.png', 16, 16) . '<span class="mini"> ' . $comm . ' &nbsp;  &nbsp; &raquo;  &nbsp; ' . $lng['Yuklamalar markazi'] . '</span>';
        echo ' <span class="count">' . vrem($file['time']) . '</span></div>';
    }

}
//////////////////// Yuklamalar /////////////////////

//////////////////// Blog ////////////////
$sql = html($_SESSION['search']);
$count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . BLOG . " WHERE `title` LIKE '%" . $sql . "%' OR `blog` LIKE '%" . $sql . "%'");

if (empty($count)) {
} else {

    $sql = DB::$dbs->query("SELECT * FROM " . BLOG . " WHERE `title` LIKE '%" . $sql . "%' OR `blog` LIKE '%" . $sql . "%' ORDER BY `id` DESC LIMIT 1");
    while ($blog = $sql->fetch()) {
        $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . BLOG_COMM . " WHERE `blog_id` = ? ", array($blog['id']));

        echo '<div class="lines">';
        echo '' . avatar($blog['user_id'], 40, 40) . ' ';
        echo ' ' . userLink($blog['user_id']) . ' <a style="vertical-align:top;margin-left:-40px;" href="' . HOME . '/blog/' . $blog['id'] . '/">' . $blog['title'] . '</a>  
		<br/>';
        echo ' ' . icon('comm.png', 16, 16) . ' <span class="mini">' . $comm . ' &nbsp;  &nbsp; &raquo;  &nbsp; ' . $lng['Blogdan'] . '</span>';
        echo '  <span class="count">' . vrem($blog['time']) . '</span>';
        echo '</div>';
    }

}
//////////////////// Blog ////////////////


//////////////////// Kutubxona ////////////////
$sql = html($_SESSION['search']);
$count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LIB_ARTICL . " WHERE `title` LIKE '%" . $sql . "%' OR `info` LIKE '%" . $sql . "%'");

if (empty($count)) {
} else {

    $sql = DB::$dbs->query("SELECT * FROM " . LIB_ARTICL . " WHERE `title` LIKE '%" . $sql . "%' OR `info` LIKE '%" . $sql . "%' ORDER BY `id` DESC LIMIT 1");
    while ($articl = $sql->fetch()) {
        $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LIB_COMM . " WHERE `articl_id` = ?", array($articl['id']));
        echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">';
        if (!empty($articl['screen'])) {
            echo '<a href="' . HOME . '/files/lib/screen/' . $articl['screen'] . '">
		<img src="' . HOME . '/files/lib/screen/' . $articl['screen'] . '" style="border-radius:55%;width:40px;height:40px;"/></a>';
        }
        echo '</td>';

        echo '<td class="lines" style="vertical-align:top;" width="95%" >';
        echo '<a href="' . HOME . '/lib/' . $articl['folder_id'] . '/' . $articl['folderc_id'] . '/' . $articl['id'] . '/">' . $articl['title'] . '</a>
<span class="mini" style="color:green;">' . $lng['Kutubxonadan'] . '</span> <span class="count">' . $comm . '</span><br/>';
        echo '<span class="mini">';
        echo SubstrMaus(text($articl['info']), 60);
        echo '</span></td></tr></table>';
    }

}
//////////////////// Kutubxona ////////////////


//////////////////// Eng yangi ////////////////
$new = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " ORDER BY `recording_date` DESC LIMIT 1");
echo '<div class="lines">';
echo ' ' . avatar($new['user_id'], 40, 40) . ' <span style="vertical-align:top;margin-left:10px;">' . user_choice($new['user_id'], 'link') . '</span><br/>
' . (!empty($new['gender']) ? '<span class="mini" style="color:green;">' . $lng['Eng yangi'] . '</span> ' : '<span class="mini" style="color:green;">' . $lng['Eng yangi2'] . '</span> ') . '
  <span class="count">' . vrem($new['recording_date']) . '</span>';
echo '</div>';
//////////////////// Eng yangi ////////////////

//////////////////// Guruhlar ////////////////
$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS . "");

if ($all == 0) {
} else {
    $sql = DB::$dbs->query("SELECT * FROM " . GROUPS . " ORDER BY `id` DESC LIMIT 1");
    while ($group = $sql->fetch()) {
        $peoples = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `group_id` = ? ", array($group['id']));
        echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="white" width="5%">';
        echo '' . (empty($group['logo']) ? '<img src="' . HOME . '/style/img/nogroup.png" style="width:45px;height:45px;border-radius:55%;"/>' : '<img src="' . HOME . '/files/groups/' . $group['logo'] . '"  style="width:45px;height:45px;border-radius:55%;"/>') . '';

        echo '</td>';

        echo '<td class="white" style="vertical-align:top;" width="95%" >';
        echo '<a href="' . HOME . '/groups/' . $group['id'] . '/">' . $group['name'] . '</a> 

		   
<span class="count">' . $peoples . '</span></br>';
        echo '<span class="mini">';
        echo SubstrMaus(text($group['info']), 40);
        echo '</span><br /> ';
        echo '</td></tr></table>';

    }
}
//////////////////// Guruhlar ////////////////

echo '</div>';
echo '</div>';


/* VIP / Start */
$vip = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `vip` > ? ", array(time()));
if (!empty($vip)) {

    $ank = DB::$dbs->queryFetch("SELECT `photo`, `user_id` FROM " . USERS . " WHERE `vip` > ? AND `last_time` > ? ORDER BY RAND() DESC LIMIT 1", array(time(), (time() - 2000)));
    if (!empty($ank)) {


        echo '<div class="white">';
        echo '<div class="grey" style="margin-left: 10px;margin-right: 10px;border-radius:4px 4px 0 0;"> &nbsp; ' . $lng['Saytimiz liderlari'] . '
<a href="' . HOME . '/shop/vip/"><span class="count">' . $lng['Ulanish'] . '</span></a></div>';
        echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-left: 10px;margin-right: 10px;">';

        echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="white" width="5%">';
        echo '' . avatar($ank['user_id'], 50, 50) . '';
        echo '</td>';

        echo '<td class="white" width="95%" >';
        echo user_choice($ank['user_id'], 'link') . '';
        echo '</td></tr></table>';
        echo '</div></div>';
    }

}
/* VIP / End */


echo '<div class="white">';

/// 3 Qator ///
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/chat/" class="imenu"><br/> ' . icon('ichat.png', 40, 40) . ' <br/>
 <span>' . $lng['Chat'] . '</span><br/><span class="mini">' . count_chat() . '</span></a>';

echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/groups/" class="imenu"><br/> ' . icon('igroup.png', 40, 40) . ' <br/> <span>' . $lng['Guruhlar'] . '</span>
<br/><span class="mini">' . count_group() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/news/" class="imenu"><br/> ' . icon('inews.png', 40, 40) . ' <br/> <span>' . $lng['Yangiliklar'] . '</span>
<br/><span class="mini">' . count_news() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';
echo '</tr></table>';


/// 4 Qator ///
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/users/" class="imenu"><br/>' . icon('ipeople.png', 40, 40) . '<br/> <span>' . $lng['Foydalanuvchilar'] . '</span>
<br/><span class="mini">' . count_users() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/lib/" class="imenu"><br/> ' . icon('ibook.png', 40, 40) . '<br/> <span>' . $lng['Kutubxona'] . '</span>
<br/><span class="mini">' . count_lib() . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';

echo '<td style="font-size:13px;width:32%;"><center>';
echo '<a href="' . HOME . '/faq/" class="imenu"><br/> ' . icon('isite.png', 40, 40) . '<br/>
 <span>' . $lng['Sayt haqida'] . '</span></a>';
echo '</center></td>';

echo '<td style="width:1%;">';
echo '</td>';
echo '</tr></table>';
echo '</div>';

?>